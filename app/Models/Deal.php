<?php

namespace App\Models;

use App\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deal extends Model
{
    use HasFactory;

    protected $casts = [
        'restaurant_id'=>'integer',
        'price'=>'float',
        'status'=>'integer',
        'options'=>'array'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeAvailable($query, $schedule_at)
    {
        return $query->where(function($q)use($schedule_at){
            $q->where('start_at', '<=', $schedule_at)->where('end_at', '>=', $schedule_at);
        });
    }

    protected static function booted()
    {
        if(auth('vendor')->check() || auth('vendor_employee')->check())
        {
            static::addGlobalScope(new RestaurantScope);
        } 
    }
}
