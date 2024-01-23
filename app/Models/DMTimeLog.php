<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DMTimeLog extends Model
{
    use HasFactory;

    public function getDurationAttribute($value)
    {
        $hours = (int) $value;
        $minutes  = 60 * ($value - $hours);
        $hours = $hours > 0 ? "{$hours} hrs": 0;
        $minutes = $minutes > 0 ? " {$minutes} mins" : "";
        return $hours.$minutes;
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }
}
