@extends('layouts.app')
@section('title', 'Beranda Guru')

@section('content')
    @php
        $myEvents = \App\Models\Event::with('room')->where('user_id', auth()->id())->latest()->take(3)->get();
        $unread = \App\Models\RoomNotification::where('user_id', auth()->id())->where('is_read', false)->count();
    @endphp

    <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->fullname }}</h1>
    <p class="mb-8 mt-1 text-board/70">Pinjam ruangan atau laporkan fasilitas yang rusak.</p>

    @if ($unread > 0)
        <a href="/events" class="mb-5 block rounded-lg border border-chalk bg-chalk/30 px-4 py-3 text-sm font-semibold">
            Ada {{ $unread }} pemberitahuan baru tentang pengajuan Anda. Lihat sekarang
        </a>
    @endif

    <div class="grid gap-3 sm:grid-cols-2">
        <a href="/events/create" class="btn btn-accent justify-start py-4 text-base">Ajukan peminjaman ruang</a>
        <a href="/complaints/create" class="btn btn-outline justify-start py-4 text-base">Lapor kerusakan</a>
    </div>

    <h2 class="mb-3 mt-10 text-lg font-bold">Pengajuan terakhir Anda</h2>
    <div class="card divide-y divide-board/10">
        @forelse ($myEvents as $event)
            <div class="flex items-center justify-between gap-3 p-4">
                <div class="min-w-0">
                    <p class="truncate font-semibold">{{ $event->event_name }}</p>
                    <p class="text-sm text-board/70">{{ $event->room->room_name }}, {{ $event->date->locale('id')->translatedFormat('j M Y') }}</p>
                </div>
                <span class="badge shrink-0 {{ $event->status === 'disetujui' ? 'badge-done' : ($event->status === 'ditolak' ? 'badge-void' : 'badge-soft') }}">{{ ucfirst($event->status) }}</span>
            </div>
        @empty
            <p class="p-4 text-sm text-board/70">Belum ada pengajuan. Mulai dari tombol "Ajukan peminjaman ruang".</p>
        @endforelse
    </div>
    @if ($myEvents->isNotEmpty())
        <a href="/events" class="mt-3 inline-block text-sm font-semibold underline underline-offset-4">Lihat semua agenda</a>
    @endif
@endsection
