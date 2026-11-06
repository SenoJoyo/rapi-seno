{{-- Tabel peminjaman ruang. Butuh $events; opsional $showDate. --}}
<div class="card overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-board/5 text-xs uppercase tracking-wide text-board/70">
            <tr>
                @if ($showDate ?? false)
                    <th scope="col" class="px-4 py-2.5">Tanggal</th>
                @endif
                <th scope="col" class="px-4 py-2.5">Waktu</th>
                <th scope="col" class="px-4 py-2.5">Ruangan</th>
                <th scope="col" class="px-4 py-2.5">Kegiatan</th>
                <th scope="col" class="px-4 py-2.5">Peminjam</th>
                <th scope="col" class="px-4 py-2.5">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-board/10">
            @forelse ($events as $e)
                <tr class="align-top print:break-inside-avoid">
                    @if ($showDate ?? false)
                        <td class="whitespace-nowrap px-4 py-2.5">{{ $e->date->format('d/m/Y') }}</td>
                    @endif
                    <td class="whitespace-nowrap px-4 py-2.5">{{ substr($e->start_time, 0, 5) }} - {{ substr($e->end_time, 0, 5) }}</td>
                    <td class="px-4 py-2.5 font-semibold">{{ $e->room->room_name }}</td>
                    <td class="px-4 py-2.5">{{ $e->event_name }}</td>
                    <td class="px-4 py-2.5">{{ $e->user->fullname }}</td>
                    <td class="px-4 py-2.5">
                        <span class="badge {{ $e->status === 'disetujui' ? 'badge-done' : ($e->status === 'ditolak' ? 'badge-void' : 'badge-soft') }}">{{ ucfirst($e->status) }}</span>
                        @if ($e->status === 'ditolak' && $e->reject_reason)
                            <p class="mt-1 text-xs text-board/70">Alasan: {{ $e->reject_reason }}</p>
                        @endif
                        @if ($e->move_reason)
                            <p class="mt-1 text-xs text-board/70">Dipindahkan: {{ $e->move_reason }}</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ ($showDate ?? false) ? 6 : 5 }}" class="px-4 py-6 text-center text-board/70">Tidak ada peminjaman ruang pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
