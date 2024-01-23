<?php

namespace Modules\LaundryManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class LaundryBanner extends Model
{
    protected $fillable = [];

    public function service()
    {
        return $this->belongsTo(Services::class, 'services_id', 'id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
}
