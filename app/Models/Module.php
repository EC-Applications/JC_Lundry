<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $casts = [
        'id'=>'integer',
        'stores_count'=>'integer',
        'status'=>'string'
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeLaundry($query)
    {
        return $query->where('module_type', 'laundry');
    }

    public function scopeNotLaundry($query)
    {
        return $query->where('module_type', '!=' ,'laundry');
    }
}
