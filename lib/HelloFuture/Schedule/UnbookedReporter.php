<?php
namespace HelloFuture\Schedule;

/*
 * 
 */

class UnbookedReporter
{
    /*
     * 
     */

    private $ranges = [];
    /*
     * 
     */
    private $totalMinutes = 0;

    /*
     * Create Unbooked ranges from the booked ranges.
     * Starts  from the first day booked, 
     * through to the last day booked.
     * Then try and find each booking in 
     * theses unbooked days.
     */

    public function setRanges(array $bookedRanges)
    {
        \usort($bookedRanges, function($a, $b) {
            return $a->getStart() > $b->getStart();
        });
        /*
         * 
         */
        $end = end($bookedRanges)->getEndOfDay();
        reset($bookedRanges);
        $start = $bookedRanges[0]->getStartOfDay();
        $dayCnt = (int) $end->diff($start)->format('%a');
        $unbookedRanges = [];
        for ($i = 0; $i <= $dayCnt; $i++) {
            $dayStart = Range::getStartOfDayStatic($start)->modify("+{$i} day");
            $dayEnd = Range::getEndOfDayStatic($start)->modify("+{$i} day");
            $unbookedRanges[] = new Range($dayStart, $dayEnd);
        }

        while ($bookedRanges) {
            $unbookedCnt = count($unbookedRanges);
            $booking = array_shift($bookedRanges);
            /*
             * Search for the booking in the list of unbooked
             */
            for ($i = 0; $i < $unbookedCnt; $i++) {
                /*
                 * It either spans completly, or splits unbooked
                 * -1, 0, or 1 or 2 ranges
                 */
                $remainders = Range::getRemainderStatic($unbookedRanges[$i], $booking);
                if (-1 !== $remainders) {
                    /*
                     * '0', booking completly spans this period of 'unbooked',
                     * remove the unbooked
                     */
                    if (!$remainders) {
                        array_splice($unbookedRanges, $i, 1);
                    } else {
                        /*
                         * Partial span replace, current unbooked with the remainders,
                         * can be 1 or 2 Ranges
                         */
                        array_splice($unbookedRanges, $i, 1, ($remainders));
                    }
                    $booking = null;
                    break;
                }
            }
            /*
             * Booked slot may still be set as 
             * it falls ouside any unbookable range
             */
        }
        foreach ($unbookedRanges as $unbooked) {
            $this->totalMinutes += $unbooked->getMinutes();
        }
        $this->ranges = $unbookedRanges;
    }
    /*
     * 
     */

    public function getRanges(): array
    {
        return $this->ranges;
    }
    /*
     * 
     */

    public function getTotalHours(): int
    {
        return (int) ($this->totalMinutes / 60);
    }
}
