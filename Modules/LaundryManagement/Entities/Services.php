<?php

namespace Modules\LaundryManagement\Entities;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $casts = [
        'status' => 'int'
    ];
    protected $fillable = [];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function laundry_items()
    {
        return $this->belongsToMany(LaundryItem::class)->using('Modules\LaundryManagement\Entities\LaundryItemServices')->withTimestamps()->withPivot('price');
    }

    public function laundry_banners()
    {
        return $this->hasMany(LaundryBanner::class);
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
