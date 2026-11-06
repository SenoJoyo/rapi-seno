<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomNotification extends Model
{
    protected $fillable = [
        'user_id', 'event_id', 'type', 'old_room_name', 'new_room_name', 'reason', 'is_read',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}