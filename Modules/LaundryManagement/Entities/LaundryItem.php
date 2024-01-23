<?php

namespace Modules\LaundryManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class LaundryItem extends Model
{
    protected $casts = [
        'status' => 'int'
    ];
    protected $fillable = [];

    public function services()
    {
        return $this->belongsToMany(Services::class)->using('Modules\LaundryManagement\Entities\LaundryItemServices')->withPivot('price')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
