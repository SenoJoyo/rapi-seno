@extends('layouts.app')
@section('title', 'Laporan Harian')

@section('content')
    @include('reports._tabs', ['active' => 'daily', 'csvUrl' => '/admin/reports/daily?date='.$day->format('Y-m-d').'&format=csv'])

    <header class="mb-6">
        <p class="hidden text-sm font-semibold text-board/70 print:block">RAPI-Sekolah</p>
        <h1 class="text-2xl font-bold">Laporan harian</h1>
        <p class="mt-1 text-board/70">{{ $day->copy()->locale('id')->translatedFormat('l, d F Y') }}</p>
    </header>

    <form method="GET" action="/admin/reports/daily" class="mb-6 flex flex-wrap items-end gap-2 print:hidden">
        <a href="/admin/reports/daily?date={{ $prev }}" class="btn btn-outline">Hari sebelumnya</a>
        <div>
            <label for="date" class="label">Tanggal</label>
            <input id="date" type="date" name="date" value="{{ $day->format('Y-m-d') }}" class="field">
        </div>
        <button class="btn btn-primary">Tampilkan</button>
        <a href="/admin/reports/daily?date={{ $next }}" class="btn btn-outline">Hari berikutnya</a>
    </form>

    @include('reports._stats')

    <section class="mt-8" aria-labelledby="h-loans">
        <h2 id="h-loans" class="mb-3 font-bold">Peminjaman ruang</h2>
        @include('reports._loans', ['events' => $events])
    </section>

    <section class="mt-8" aria-labelledby="h-damages">
        <h2 id="h-damages" class="mb-3 font-bold">Kerusakan dilaporkan hari ini</h2>
        @include('reports._damages', ['complaints' => $complaints])
        <p class="hint">
            Status perbaikan adalah status saat laporan ini dibuka.
            Seluruh pengaduan yang belum selesai sampai sekarang: <strong>{{ $backlog }}</strong>.
        </p>
    </section>

    <p class="mt-8 hidden text-xs text-board/60 print:block">
        Dicetak {{ now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i') }} WIB oleh {{ auth()->user()->fullname }}
    </p>
@endsection
