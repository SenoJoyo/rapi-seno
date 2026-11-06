<?php

namespace App\Http\Controllers;

use App\Models\Event;

class CalendarController extends Controller
{
    public function publicCalendar()
    {
        return view('calendar.public');
    }

    public function feed()
    {
        $events = Event::with('room')
            ->where('status', '!=', 'ditolak')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->event_name . ' @ ' . $event->room->room_name,
                    'start' => $event->date->format('Y-m-d') . 'T' . $event->start_time,
                    'end' => $event->date->format('Y-m-d') . 'T' . $event->end_time,
                ];
            });

        return response()->json($events);
    }
}