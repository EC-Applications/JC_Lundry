<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $casts = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'priority' => 'integer',
        'status' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function childes()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function category_restaurants()
    {
        return $this->hasMany(CategoryRestaurant::class);
    }

    public function category_restaurant()
    {
        return $this->hasOne(CategoryRestaurant::class);
    }
}
