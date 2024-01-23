<?php

namespace Modules\LaundryManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class LaundryDeliveryType extends Model
{
    protected $casts = [
        'charge' => 'float',
        'duration' => 'integer',
        'status' => 'int'
    ];
    protected $fillable = [];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
}
