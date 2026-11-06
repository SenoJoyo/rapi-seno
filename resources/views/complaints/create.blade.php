@extends('layouts.app')
@section('title', 'Lapor Kerusakan')

@section('content')
    <h1 class="text-2xl font-bold">Lapor kerusakan</h1>
    <p class="mb-6 mt-1 text-board/70">Sertakan foto agar PIC ruangan cepat menindaklanjuti.</p>

    <form method="POST" action="/complaints" enctype="multipart/form-data" class="card max-w-xl space-y-4 p-5">
        @csrf
        @if ($event)
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <div class="rounded-lg border border-board/15 bg-chalk/30 p-4 text-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-board/60">Data laporan (otomatis)</p>
                <dl class="mt-2 space-y-1">
                    <div class="flex gap-2"><dt class="w-20 shrink-0 text-board/70">Ruangan</dt><dd class="font-semibold">{{ $event->room->room_name }}</dd></div>
                    <div class="flex gap-2"><dt class="w-20 shrink-0 text-board/70">Pelapor</dt><dd class="font-semibold">{{ auth()->user()->fullname }}</dd></div>
                    <div class="flex gap-2"><dt class="w-20 shrink-0 text-board/70">Agenda</dt><dd>{{ $event->event_name }}, {{ $event->date->locale('id')->translatedFormat('j M Y') }}</dd></div>
                </dl>
            </div>
        @else
            <div>
                <label for="room_id" class="label">Ruangan</label>
                <select id="room_id" name="room_id" class="field" required>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->room_name }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-sm text-board/70">Pelapor: <span class="font-semibold">{{ auth()->user()->fullname }}</span></p>
        @endif
        <div>
            <label for="description" class="label">Apa yang rusak?</label>
            <textarea id="description" name="description" rows="3" class="field" required>{{ old('description') }}</textarea>
        </div>
        <div>
            <label for="photo" class="label">Foto bukti</label>
            <input id="photo" type="file" name="photo" accept="image/*"
                   class="field file:mr-3 file:rounded-md file:border-0 file:bg-board file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white" required>
            <p class="hint">Maksimal 5 MB.</p>
        </div>
        <button class="btn btn-primary w-full sm:w-auto">Kirim laporan</button>
    </form>
@endsection
