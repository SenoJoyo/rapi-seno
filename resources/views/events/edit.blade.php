@extends('layouts.app')
@section('title', 'Edit Agenda')

@section('content')
    <a href="/events" class="text-sm font-semibold underline underline-offset-4">Kembali ke agenda</a>

    <h1 class="mb-6 mt-3 text-2xl font-bold">Edit agenda</h1>

    <form method="POST" action="/admin/events/{{ $event->id }}" class="card max-w-xl space-y-4 p-5">
        @csrf
        @method('PUT')
        <div>
            <label for="event_name" class="label">Nama agenda</label>
            <input id="event_name" type="text" name="event_name" value="{{ old('event_name', $event->event_name) }}" class="field" required>
        </div>
        <div>
            <label class="label">Ruangan</label>
            <p class="field bg-chalk/30">{{ $event->room->room_name }}</p>
            <p class="hint">Untuk mengganti ruangan, pakai <a href="/admin/events/{{ $event->id }}/move" class="font-semibold underline underline-offset-4">Pindahkan ruangan</a> agar pemohon menerima alasannya.</p>
        </div>
        <div>
            <label for="date" class="label">Tanggal</label>
            <input id="date" type="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" class="field" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="start_time" class="label">Jam mulai</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time', substr($event->start_time, 0, 5)) }}" class="field" required>
            </div>
            <div>
                <label for="end_time" class="label">Jam selesai</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time', substr($event->end_time, 0, 5)) }}" class="field" required>
            </div>
        </div>
        <div>
            <label for="status" class="label">Status</label>
            <select id="status" name="status" class="field" required>
                @foreach (['diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $event->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="reject_reason" class="label">Alasan penolakan</label>
            <textarea id="reject_reason" name="reject_reason" rows="3" maxlength="255" class="field">{{ old('reject_reason', $event->reject_reason) }}</textarea>
            <p class="hint">Wajib diisi kalau status Ditolak. Pemohon akan menerima alasan ini.</p>
        </div>
        <button class="btn btn-primary w-full sm:w-auto">Simpan perubahan</button>
    </form>
@endsection
