<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    // get min shift time and max shift times for this day.
    public static function getMinMaxTimesInDay(int $rotaId, Carbon $date) {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        $minMaxData = static::where('rota_id', $this->rota->id)
            ->where('start_time', '>=', $startOfDay->format('Y-m-d'))
            ->where('end_time', '<=', $endOfDay->format('Y-m-d'))
            ->select(DB::raw('min(start_time) as min_start_time, max(end_time) as max_end_time'))
            ->first();
        return $minMaxData;
    }

    private static function getShiftsBetweenQuery(int $rotaId, Carbon $startTime, Carbon $endTime) {
        return static::where('rota_id', $rotaId)
            ->where('start_time', '>=', $startTime->format('Y-m-d'))
            ->where('end_time', '<=', $endOfDay->format('Y-m-d')); 
    }

    public static function getShiftsBetweenCount(int $rotaId, Carbon $startTime, Carbon $endTime) : int {
        return static::getShiftsBetweenQuery()->count();
    }
}
