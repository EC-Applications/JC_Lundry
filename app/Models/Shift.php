<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $dates = ['start_time', 'end_time', 'date'];

    protected $casts = [
        'parent_id' => 'integer',
        'zone_id' => 'integer'
    ];

    public function special_shifts()
    {
        return $this->hasMany(Shift::class, 'parent_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'parent_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function time_log()
    {
        return $this->hasMany(DMTimeLog::class);
    }
}
