@extends('layouts.app')
@section('title', 'Ajukan Agenda')

@section('content')
    <h1 class="text-2xl font-bold">Ajukan peminjaman ruang</h1>
    <p class="mb-6 mt-1 text-board/70">Isi detail agenda. Admin akan menyetujui atau menolak pengajuan Anda.</p>

    <form method="POST" action="/events" class="card max-w-xl space-y-4 p-5">
        @csrf
        <div>
            <label for="event_name" class="label">Nama agenda</label>
            <input id="event_name" type="text" name="event_name" value="{{ old('event_name') }}" class="field" required>
        </div>
        <div>
            <label for="room_id" class="label">Ruangan</label>
            <select id="room_id" name="room_id" class="field" required>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->room_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="date" class="label">Tanggal</label>
            {{-- min = hari ini: tanggal sebelumnya tidak bisa dipilih --}}
            <input id="date" type="date" name="date" min="{{ $today }}" value="{{ old('date') }}" class="field" required>
            <p class="hint">Tanggal yang sudah lewat tidak bisa dipilih.</p>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="start_time" class="label">Jam mulai</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" class="field" required>
            </div>
            <div>
                <label for="end_time" class="label">Jam selesai</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" class="field" required>
            </div>
        </div>
        <button class="btn btn-primary w-full sm:w-auto">Ajukan</button>
    </form>
@endsection
