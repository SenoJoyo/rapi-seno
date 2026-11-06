@extends('layouts.app')
@section('title', 'Pengaduan')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Pengaduan kerusakan</h1>

    <ul class="space-y-3">
        @forelse ($complaints as $c)
            <li class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-semibold">{{ $c->room->room_name }}</p>
                    <span class="badge shrink-0 {{ $c->status_repair === 'Sudah Diperbaiki' ? 'badge-done' : ($c->status_repair === 'Menunggu Spare Part' ? 'badge-soft' : 'badge-warn') }}">
                        {{ $c->status_repair }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-board/70">PIC: {{ $c->room->pic->fullname ?? 'Belum ditentukan' }}</p>
                <p class="mt-2">{{ $c->description }}</p>

                @include('complaints._photo')

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    @if ($c->status_repair !== 'Sudah Diperbaiki')
                        <a href="/admin/complaints/{{ $c->id }}/whatsapp" class="btn btn-accent btn-sm">Ingatkan PIC lewat WhatsApp</a>
                    @endif
                    <a href="/admin/complaints/{{ $c->id }}/edit" class="btn btn-outline btn-sm">Edit</a>
                    <form method="POST" action="/admin/complaints/{{ $c->id }}"
                          onsubmit="return confirm('Hapus pengaduan ini beserta fotonya? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline btn-sm">Hapus</button>
                    </form>
                </div>
            </li>
        @empty
            <li class="card p-6 text-center text-sm text-board/70">Belum ada pengaduan.</li>
        @endforelse
    </ul>
@endsection
