<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use App\Scopes\ZoneScope;

class Zone extends Model
{
    use HasFactory;
    use SpatialTrait;

    protected $spatialFields = [
        'coordinates'
    ];

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function deliverymen()
    {
        return $this->hasMany(DeliveryMan::class)->where('is_laundry_dm',0);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Restaurant::class);
    }


    public function campaigns()
    {
        return $this->hasManyThrough(Campaigns::class, Restaurant::class);
    }

    public function shift()
    {
        return $this->hasMany(Shift::class);
    }

    public function time_log()
    {
        return $this->hasMany(DMTimeLog::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class)->withPivot(['per_km_shipping_charge','minimum_shipping_charge'])->using('App\Models\ModuleZone');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }
}
