<?php
namespace HelloFuture\Schedule;

/*
 * An employee calendar.
 * Assumes events *dont* overlap.
 * 
 */

use Sabre\VObject\Reader;

class Calendar
{
    /*
     * 
     */

    private $clientReporter;
    /*
     * 
     */
    private $unbookedReporter;

    /*
     * 
     */

    public function __construct(string $data, string $tz = 'Europe/London')
    {
        $this->clientReporter = new ClientReporter;
        $this->unbookedReporter = new UnbookedReporter;
        $vCalendar = Reader::read($data, Reader::OPTION_FORGIVING);
        $this->parse($vCalendar, $tz);
    }
    /*
     * 
     */

    public function getClientReporter(): ClientReporter
    {
        return $this->clientReporter;
    }
    /*
     * 
     */

    public function getUnbookedReporter(): UnbookedReporter
    {
        return $this->unbookedReporter;
    }
    /*
     * 
     */

    private function parse($vCalendar, string $tz)
    {
        $ranges = [];
        $tzString = $vCalendar->select('X-WR-TIMEZONE') ? $vCalendar->select('X-WR-TIMEZONE')[0]->getValue() : $tz;
        $tz = new \DateTimeZone($tzString);
        foreach ($vCalendar->VEVENT as $i => $event) {
            /*
             * Expanding of recurring events not implemented
             * $vCalendar->expand() to create
             */
            if ($event->duration) {
                throw new \Exception('Cannont process repeating events: not implemented');
            }
            $endDt = $event->dtend->getDateTime()->setTimezone($tz);
            $startDt = $event->dtstart->getDateTime()->setTimezone($tz);
            $range = new Range($startDt, $endDt);
            $ranges[] = $range;
            /*
             * Add data to our reports
             */
            $this->clientReporter->addRange($range, $event->summary);
        }
        $this->unbookedReporter->setRanges($ranges);
    }
}
