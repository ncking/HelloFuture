<?php
namespace HelloFuture\Schedule;

/*
 * A time span, booked or unbooked
 */

class Client
{
    /*
     * 
     */

    private $name;
    /*
     * 
     */
    private $totalMinutes;

    /*
     * 
     */

    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /*
     * 
     */

    public function getName(): string
    {
        return $this->name;
    }
    /*
     * 
     */

    public function addEvent(Range $range)
    {
        $this->ranges[] = $range;
        $this->totalMinutes += $range->getMinutes();
    }
    /*
     * 
     */

    public function getTotalHours(): float
    {
        return (int) $this->totalMinutes / 60;
    }
}
