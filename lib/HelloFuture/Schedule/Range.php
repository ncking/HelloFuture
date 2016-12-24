<?php
namespace HelloFuture\Schedule;

/*
 * We hide all the messy Date calculation here
 */

class Range
{
    /*
     *
     */

    private $startDt;
    /*
     *
     */
    private $endDt;
    /*
     *
     */
    private $startOfDayDt;
    /*
     *
     */
    private $endOfDayDt;

    /*
     *
     */

    public function __construct(\DateTimeImmutable $startDt, \DateTimeImmutable $endDt)
    {
        if ($startDt > $endDt) {
            throw new \InvalidArgumentException('start time after endtime');
        } else if ($endDt->diff($startDt)->format('%a') > 0) {
            throw new \InvalidArgumentException('Bookable events cannot span more than one day');
        }
        $this->startDt = $startDt;
        $this->endDt = $endDt;
        $this->startOfDayDt = self::getStartOfDayStatic($startDt);
        $this->endOfDayDt = self::getEndOfDayStatic($endDt);
    }
    /*
     *
     */

    public function getStart(): \DateTimeImmutable
    {
        return $this->startDt;
    }
    /*
     *
     */

    public function getEnd(): \DateTimeImmutable
    {
        return $this->endDt;
    }
    /*
     *
     */

    public function isFirstAvilable(): bool
    {
        return $this->startDt <= $this->startOfDayDt;
    }
    /*
     *
     */

    public function isFullDay(): bool
    {
        return ($this->isFirstAvilable() && $this->endDt == $this->endOfDayDt);
    }
    /*
     *
     */

    public function getMinutes(): int
    {
        return $this->isFullDay() ? (8 * 60) : ($this->endDt->getTimestamp() - $this->startDt->getTimestamp() ) / 60;
    }
    /*
     *
     */

    public function getHours(): float
    {
        return ($this->getMinutes() / 60);
    }
    /*
     *
     */

    public function getStartOfDay(): \DateTimeImmutable
    {
        return $this->startOfDayDt;
    }
    /*
     *
     */

    public function getEndOfDay(): \DateTimeImmutable
    {
        return $this->endOfDayDt;
    }
  
    /*
     *
     */

    public static function getStartOfDayStatic(\DateTimeImmutable $dt): \DateTimeImmutable
    {
        return $dt->setTime(9, 0, 0);
    }
    /*
     *
     */

    public static function getEndOfDayStatic(\DateTimeImmutable $dt): \DateTimeImmutable
    {
        return $dt->setTime(17, 30, 0);
    }
    /*
     *
     */

    public static function getRemainderStatic(Range $source, Range $cut)
    {

        if (
            ($cut->getEnd() < $source->getStart()) ||
            ($cut->getStart() > $source->getEnd())) {
            return -1;
        }
        /*
         * 
         */
        $before = $cut->getStart() > $source->getStart();
        $after = $source->getEnd() > $cut->getEnd();


        if ($before <= 0 & $after <= 0) {
            return 0;
        }
        /*
         * 
         */
        $remainders = [];
        if ($before) {
            $remainders[] = new self($source->getStart(), $cut->getStart());
        }
        if ($after) {
            $remainders[] = new self($cut->getEnd(), $source->getEnd());
        }
        return $remainders;
    }
}
