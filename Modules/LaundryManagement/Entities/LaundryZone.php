<?php

namespace Modules\LaundryManagement\Entities;

use App\Models\DeliveryMan;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class LaundryZone extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
        'coordinates'
    ];

    protected $casts = [
        'status'=>'int',
    ];
    protected $fillable = [];
    
    public function deliverymen()
    {
        return $this->hasMany(DeliveryMan::class, 'zone_id')->where('is_laundry_dm',1);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
}
