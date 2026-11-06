<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_name', 'capacity', 'location', 'pic_id', 'is_active',
    ];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}