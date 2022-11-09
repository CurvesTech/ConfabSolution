<?php
namespace App;

class SingleManning {
    private $map;

    public function __construct() {
        $this->map = [];
    }

    public function addRecord(string $day, int $minutes) {
        $this->map[$day] = $minutes;
    }

    public function getTotalMinutes() : int {
        $count = 0;
        foreach($this->map as $day => $minsInDay) {
            $count += $minsInDay;
        }

        return $count;
    }

}