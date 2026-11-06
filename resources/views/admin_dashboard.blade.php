@extends('layouts.app')
@section('title', 'Beranda Admin')

@section('content')
    @php
        $pendingEvents = \App\Models\Event::where('status', 'diajukan')->count();
        $openComplaints = \App\Models\Complaint::where('status_repair', '!=', 'Sudah Diperbaiki')->count();
    @endphp

    <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->fullname }}</h1>
    <p class="mb-8 mt-1 text-board/70">Yang perlu ditangani hari ini.</p>

    <div class="card divide-y divide-board/10">
        <a href="/events" class="flex items-center justify-between gap-4 p-5 transition hover:bg-board/5">
            <div>
                <p class="font-semibold">Agenda menunggu persetujuan</p>
                <p class="text-sm text-board/70">Setujui, tolak, atau pindahkan ruangan.</p>
            </div>
            <span class="badge {{ $pendingEvents ? 'badge-warn' : 'badge-void' }} text-sm">{{ $pendingEvents }}</span>
        </a>
        <a href="/admin/complaints" class="flex items-center justify-between gap-4 p-5 transition hover:bg-board/5">
            <div>
                <p class="font-semibold">Pengaduan belum selesai</p>
                <p class="text-sm text-board/70">Pantau perbaikan dan kirim pengingat WhatsApp ke PIC.</p>
            </div>
            <span class="badge {{ $openComplaints ? 'badge-warn' : 'badge-void' }} text-sm">{{ $openComplaints }}</span>
        </a>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="/events/create" class="btn btn-primary">Buat agenda</a>
        <a href="/admin/rooms" class="btn btn-outline">Tambah ruangan</a>
        <a href="/admin/reports/daily" class="btn btn-outline">Laporan harian</a>
        <a href="/admin/reports/monthly" class="btn btn-outline">Laporan bulanan</a>
    </div>
@endsection
