@extends('layouts.app')
@section('title', 'Edit Pengaduan')

@section('content')
    <a href="/admin/complaints" class="text-sm font-semibold underline underline-offset-4">Kembali ke pengaduan</a>

    <h1 class="mb-6 mt-3 text-2xl font-bold">Edit pengaduan</h1>

    <form method="POST" action="/admin/complaints/{{ $complaint->id }}" enctype="multipart/form-data" class="card max-w-xl space-y-4 p-5">
        @csrf
        @method('PUT')
        <div>
            <label for="room_id" class="label">Ruangan</label>
            <select id="room_id" name="room_id" class="field" required>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected(old('room_id', $complaint->room_id) == $room->id)>{{ $room->room_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="description" class="label">Deskripsi kerusakan</label>
            <textarea id="description" name="description" rows="4" class="field" required>{{ old('description', $complaint->description) }}</textarea>
        </div>
        <div>
            <label for="status_repair" class="label">Status perbaikan</label>
            <select id="status_repair" name="status_repair" class="field" required>
                @foreach (['Belum Diperbaiki', 'Menunggu Spare Part', 'Sudah Diperbaiki'] as $status)
                    <option value="{{ $status }}" @selected(old('status_repair', $complaint->status_repair) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="photo" class="label">Foto</label>
            @if ($complaint->photo_path)
                <img src="{{ asset('storage/'.$complaint->photo_path) }}" alt="Foto kerusakan saat ini" class="mb-2 max-h-48 rounded-lg border border-board/10">
            @endif
            <input id="photo" type="file" name="photo" accept="image/*" class="field">
            <p class="hint">Kosongkan kalau tidak ingin mengganti foto.</p>
        </div>
        <button class="btn btn-primary w-full sm:w-auto">Simpan perubahan</button>
    </form>
@endsection
