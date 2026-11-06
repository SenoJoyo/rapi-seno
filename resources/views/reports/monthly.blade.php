@extends('layouts.app')
@section('title', 'Laporan Bulanan')

@section('content')
    @include('reports._tabs', ['active' => 'monthly', 'csvUrl' => '/admin/reports/monthly?month='.$month->format('Y-m').'&format=csv'])

    <header class="mb-6">
        <p class="hidden text-sm font-semibold text-board/70 print:block">RAPI-Sekolah</p>
        <h1 class="text-2xl font-bold">Laporan bulanan</h1>
        <p class="mt-1 text-board/70">{{ $month->copy()->locale('id')->translatedFormat('F Y') }}</p>
    </header>

    <form method="GET" action="/admin/reports/monthly" class="mb-6 flex flex-wrap items-end gap-2 print:hidden">
        <a href="/admin/reports/monthly?month={{ $prev }}" class="btn btn-outline">Bulan sebelumnya</a>
        <div>
            <label for="month" class="label">Bulan</label>
            <input id="month" type="month" name="month" value="{{ $month->format('Y-m') }}" class="field">
        </div>
        <button class="btn btn-primary">Tampilkan</button>
        <a href="/admin/reports/monthly?month={{ $next }}" class="btn btn-outline">Bulan berikutnya</a>
    </form>

    @include('reports._stats')

    <p class="mt-3 text-sm text-board/70">
        Total pemakaian ruang (agenda disetujui): <strong>{{ number_format($totalHours, 1, ',', '.') }} jam</strong>.
        Seluruh pengaduan yang belum selesai sampai sekarang: <strong>{{ $backlog }}</strong>.
    </p>

    <section class="mt-8" aria-labelledby="h-room">
        <h2 id="h-room" class="mb-3 font-bold">Rekap per ruangan</h2>
        <div class="card overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-board/5 text-xs uppercase tracking-wide text-board/70">
                    <tr>
                        <th scope="col" class="px-4 py-2.5">Ruangan</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Pengajuan</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Disetujui</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Jam pakai</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Kerusakan</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Belum selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-board/10">
                    @forelse ($perRoom as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-semibold">{{ $row['room']->room_name }}</td>
                            <td class="px-4 py-2.5 text-right">{{ $row['loans'] }}</td>
                            <td class="px-4 py-2.5 text-right">{{ $row['approved'] }}</td>
                            <td class="px-4 py-2.5 text-right">{{ number_format($row['hours'], 1, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right">{{ $row['damages'] }}</td>
                            <td class="px-4 py-2.5 text-right">{{ $row['open'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-board/70">Belum ada aktivitas ruangan pada bulan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="h-day">
        <h2 id="h-day" class="mb-3 font-bold">Rekap per hari</h2>
        <div class="card overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-board/5 text-xs uppercase tracking-wide text-board/70">
                    <tr>
                        <th scope="col" class="px-4 py-2.5">Tanggal</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Pengajuan</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Disetujui</th>
                        <th scope="col" class="px-4 py-2.5 text-right">Kerusakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-board/10">
                    @forelse ($perDay as $row)
                        <tr>
                            <td class="px-4 py-2.5">
                                <a href="/admin/reports/daily?date={{ $row['date']->format('Y-m-d') }}" class="font-semibold underline-offset-2 hover:underline">
                                    {{ $row['date']->copy()->locale('id')->translatedFormat('D, d M') }}
                                </a>
                            </td>
                            <td class="px-4 py-2.5 text-right">{{ $row['loans'] }}</td>
                            <td class="px-4 py-2.5 text-right">{{ $row['approved'] }}</td>
                            <td class="px-4 py-2.5 text-right">{{ $row['damages'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-board/70">Belum ada aktivitas pada bulan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8" aria-labelledby="h-loans">
        <h2 id="h-loans" class="mb-3 font-bold">Detail peminjaman ruang</h2>
        @include('reports._loans', ['events' => $events, 'showDate' => true])
    </section>

    <section class="mt-8" aria-labelledby="h-damages">
        <h2 id="h-damages" class="mb-3 font-bold">Detail kerusakan</h2>
        @include('reports._damages', ['complaints' => $complaints, 'showDate' => true])
        <p class="hint">Status perbaikan adalah status saat laporan ini dibuka.</p>
    </section>

    <p class="mt-8 hidden text-xs text-board/60 print:block">
        Dicetak {{ now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i') }} WIB oleh {{ auth()->user()->fullname }}
    </p>
@endsection
