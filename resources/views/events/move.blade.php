@extends('layouts.app')
@section('title', 'Pindahkan Ruangan')

@section('content')
    <a href="/events" class="text-sm font-semibold underline underline-offset-4">Kembali ke agenda</a>

    <h1 class="mb-6 mt-3 text-2xl font-bold">Pindahkan ruangan</h1>

    <div class="card mb-5 max-w-xl p-5">
        <p class="font-semibold">{{ $event->event_name }}</p>
        <p class="mt-1 text-sm text-board/70">
            Ruangan sekarang: {{ $event->room->room_name }}<br>
            {{ $event->date->locale('id')->translatedFormat('j F Y') }}, {{ substr($event->start_time, 0, 5) }} sampai {{ substr($event->end_time, 0, 5) }}<br>
            Diajukan oleh {{ $event->user->fullname }}
        </p>
    </div>

    <form method="POST" action="/admin/events/{{ $event->id }}/move" class="card max-w-xl space-y-4 p-5">
        @csrf
        <div>
            <label for="new_room_id" class="label">Pindahkan ke ruangan</label>
            <select id="new_room_id" name="new_room_id" class="field" required>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected(old('new_room_id') == $room->id)>{{ $room->room_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="move_reason" class="label">Alasan pemindahan</label>
            <textarea id="move_reason" name="move_reason" rows="3" minlength="5" maxlength="255" class="field" required>{{ old('move_reason') }}</textarea>
            <p class="hint">Alasan ini dikirim ke pemohon sebagai pemberitahuan.</p>
        </div>
        <button class="btn btn-primary w-full sm:w-auto">Pindahkan dan beri tahu pemohon</button>
    </form>
@endsection
