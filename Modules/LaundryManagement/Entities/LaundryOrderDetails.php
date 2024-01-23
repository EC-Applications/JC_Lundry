<?php

namespace Modules\LaundryManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class LaundryOrderDetails extends Model
{
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'float',

    ];
    protected $fillable = [];


    public function laundry_item()
    {
        return $this->belongsTo(LaundryItem::class,'laundry_item_id', 'id');
    }

    public function laundry_order()
    {
        return $this->belongsTo(LaundryOrder::class,'laundry_orders_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class,'services_id', 'id');
    }

    public function item_track()
    {
        return $this->hasMany(LaundryItemDetailsTrack::class, 'laundry_order_detail_id', 'id');

    }
}
