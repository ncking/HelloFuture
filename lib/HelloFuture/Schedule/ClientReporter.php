<?php
namespace HelloFuture\Schedule;

/*
 * 
 */

class ClientReporter
{
    /*
     * 
     */

    private $clientMap = [];
    /*
     * Total range of bookings in minutes
     */
    private $bookedTotalMinutes = 0;

    /*
     * Clients sorted A-Z
     */

    public function getClients(): array
    {
        \ksort($this->clientMap);
        return $this->clientMap;
    }
    /*
     * clients sorted by client hours descending
     */

    public function getClientsByHours(): array
    {
        $array = $this->getClients();
        \uasort($array, function($a, $b) {
            return $a->getTotalHours() < $b->getTotalHours();
        });
        return $array;
    }
    /*
     * 
     */

    public function getTotalHours(): int
    {
        return ($this->bookedTotalMinutes / 60);
    }
    /*
     *  
     */

    public function addRange(Range $range, string $clientName)
    {
        $key = trim($clientName);
        if (!isset($this->clientMap[$key])) {
            $this->clientMap[$key] = new Client($clientName);
        }
        $this->clientMap[$key]->addEvent($range);
        $this->bookedTotalMinutes += $range->getMinutes();
    }
}
