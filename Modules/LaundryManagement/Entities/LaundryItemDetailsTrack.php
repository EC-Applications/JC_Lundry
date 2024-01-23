<?php

namespace Modules\LaundryManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class LaundryItemDetailsTrack extends Model
{
    protected $fillable = [];

    public function details()
    {
        return $this->belongsTo(LaundryOrderDetails::class,  'laundry_order_detail_id');
    }
}
