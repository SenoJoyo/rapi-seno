@extends('layouts.app')
@section('title', 'Pengaduan Ruangan Saya')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Pengaduan di ruangan Anda</h1>

    <ul class="space-y-3">
        @forelse ($complaints as $c)
            <li class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-semibold">{{ $c->room->room_name }}</p>
                    <span class="badge shrink-0 {{ $c->status_repair === 'Sudah Diperbaiki' ? 'badge-done' : ($c->status_repair === 'Menunggu Spare Part' ? 'badge-soft' : 'badge-warn') }}">
                        {{ $c->status_repair }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-board/70">
                    Dilaporkan {{ $c->reporter->fullname }}, {{ $c->reported_at->format('d M Y H:i') }}
                </p>
                <p class="mt-2">{{ $c->description }}</p>

                @include('complaints._photo')

                <form method="POST" action="/pic/complaints/{{ $c->id }}/status" class="mt-4 flex flex-wrap items-center gap-2">
                    @csrf
                    <label for="status-{{ $c->id }}" class="sr-only">Status perbaikan</label>
                    <select id="status-{{ $c->id }}" name="status_repair" class="field w-auto min-w-0 flex-1 py-2 text-sm sm:flex-none">
                        <option value="Belum Diperbaiki" @selected($c->status_repair == 'Belum Diperbaiki')>Belum Diperbaiki</option>
                        <option value="Menunggu Spare Part" @selected($c->status_repair == 'Menunggu Spare Part')>Menunggu Spare Part</option>
                        <option value="Sudah Diperbaiki" @selected($c->status_repair == 'Sudah Diperbaiki')>Sudah Diperbaiki</option>
                    </select>
                    <button class="btn btn-primary btn-sm py-2.5">Simpan status</button>
                </form>
            </li>
        @empty
            <li class="card p-6 text-center text-sm text-board/70">Belum ada pengaduan untuk ruangan yang Anda tangani.</li>
        @endforelse
    </ul>
@endsection
