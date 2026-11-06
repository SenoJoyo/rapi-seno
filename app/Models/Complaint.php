<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'room_id', 'reporter_id', 'description', 'photo_path',
        'status_repair', 'handler_id', 'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    /**
     * Waktu laporan dalam WIB. Memakai created_at (selalu disimpan Laravel dalam zona waktu aplikasi),
     * karena reported_at diisi default MySQL sehingga zona waktunya bisa berbeda.
     */
    public function reportedAtLocal(): Carbon
    {
        return Carbon::parse($this->created_at ?? $this->reported_at)->setTimezone('Asia/Jakarta');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }
}