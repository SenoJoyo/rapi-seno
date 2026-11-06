{{-- Tabel kerusakan. Butuh $complaints; opsional $showDate. Status = status saat laporan dicetak. --}}
<div class="card overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-board/5 text-xs uppercase tracking-wide text-board/70">
            <tr>
                <th scope="col" class="px-4 py-2.5">{{ ($showDate ?? false) ? 'Tanggal' : 'Jam' }}</th>
                <th scope="col" class="px-4 py-2.5">Ruangan</th>
                <th scope="col" class="px-4 py-2.5">Kerusakan</th>
                <th scope="col" class="px-4 py-2.5">Pelapor</th>
                <th scope="col" class="px-4 py-2.5">PIC</th>
                <th scope="col" class="px-4 py-2.5">Status perbaikan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-board/10">
            @forelse ($complaints as $c)
                @php $at = $c->reportedAtLocal(); @endphp
                <tr class="align-top print:break-inside-avoid">
                    <td class="whitespace-nowrap px-4 py-2.5">{{ ($showDate ?? false) ? $at->format('d/m/Y H:i') : $at->format('H:i') }}</td>
                    <td class="px-4 py-2.5 font-semibold">{{ $c->room->room_name }}</td>
                    <td class="px-4 py-2.5">{{ $c->description }}</td>
                    <td class="px-4 py-2.5">{{ $c->reporter->fullname ?? '-' }}</td>
                    <td class="px-4 py-2.5">{{ $c->room->pic->fullname ?? '-' }}</td>
                    <td class="px-4 py-2.5">
                        <span class="badge {{ $c->status_repair === 'Sudah Diperbaiki' ? 'badge-done' : ($c->status_repair === 'Menunggu Spare Part' ? 'badge-soft' : 'badge-warn') }}">{{ $c->status_repair }}</span>
                        @if ($c->handler)
                            <p class="mt-1 text-xs text-board/70">Ditangani: {{ $c->handler->fullname }}</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-board/70">Tidak ada laporan kerusakan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
