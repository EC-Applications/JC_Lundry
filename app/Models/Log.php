<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $casts = [
        'current_state' => 'array'
    ];

    public function logable()
    {
        return $this->morphTo();
    }
    public function food()
    {
        return $this->belongsTo(Food::class, 'model_id');
    }
}
