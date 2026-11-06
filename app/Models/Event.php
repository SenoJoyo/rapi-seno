<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'event_name', 'date', 'start_time', 'end_time',
        'status', 'approved_by', 'move_reason', 'reject_reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeConflicting(Builder $query, int $roomId, string $date, string $startTime, string $endTime)
    {
        return $query->where('room_id', $roomId)
            ->where('date', $date)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);
    }
}