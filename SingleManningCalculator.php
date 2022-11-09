<?php

namespace App;

use Carbon\Carbon;
use App\Models\Rota;
use App\Models\Shift;
use App\SingleManning;

class SingleManningCalculator {
    private Rota $rota;
    const INTERVAL_GRANULARITY_MINUTES = 15;
    
    public function __construct(Rota $rota) {
        $this->rota = $rota;
    }

    public function calculate() : SingleManning {
        $date = Carbon::parse($rota->week_commence_date);
        $singleManning = new SingleManning();
        $i = 0;
        while($i < 7) {
            
            $singleManningMinsForDay = $this->calculateForDay($date);
            $singleManning->addRecord($date->format('D'), $singleManningMinsForDay);
            $date->addDays(1);
            $i++;
        }

        return $singleManning;
    }

    private function calculateForDay(Carbon $date) : int {
        $minMaxTimes = Shift::getMinMaxTimesInDay($this->rota->id, $date);

        $earliestShiftStartTime = Carbon::parse($minMaxTimes->min_start_time);
        $latestShiftEndTime = Carbon::parse($minMaxTimes->max_end_time);

        $singleManningMinsForDay = 0;
        
        // an iterator from earliest start to latest finish
        $carbonIterator = $earliestShiftStartTime->clone();
        while($carbonIterator->lte($latestShiftEndTime)) {
            if(
                Shift::getShiftsBetweenCount(
                    $carbonIterator, $carbonIterator->clone()->addMins(static::INTERVAL_GRANULARITY_MINUTES)
                ) === 1
            ) {
                $singleManningMinsForDay += static::INTERVAL_GRANULARITY_MINUTES;
            }
            $carbonIterator->addMins(static::INTERVAL_GRANULARITY_MINUTES);
        }

        return $singleManningMinsForDay;
    }
}