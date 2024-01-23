<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryCategory extends Model
{
    use HasFactory;

    public function module()
    {
        return $this->belongsTo(Module::class);
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
