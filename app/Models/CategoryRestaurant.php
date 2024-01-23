<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryRestaurant extends Model
{
    use HasFactory;
    protected $casts = [
        'category_id' => 'integer',
        'restaurant_id' => 'integer',
        'priority' => 'integer',
    ];
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
