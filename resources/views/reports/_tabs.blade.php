{{-- Tab Harian/Bulanan + tombol unduh & cetak. Butuh $active ('daily'|'monthly') dan $csvUrl. --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 print:hidden">
    <div class="inline-flex rounded-lg border border-board/20 p-1 text-sm font-semibold">
        <a href="/admin/reports/daily"
           class="rounded-md px-4 py-1.5 {{ $active === 'daily' ? 'bg-board text-white' : 'text-board/70 hover:bg-board/5' }}"
           @if ($active === 'daily') aria-current="page" @endif>Harian</a>
        <a href="/admin/reports/monthly"
           class="rounded-md px-4 py-1.5 {{ $active === 'monthly' ? 'bg-board text-white' : 'text-board/70 hover:bg-board/5' }}"
           @if ($active === 'monthly') aria-current="page" @endif>Bulanan</a>
    </div>
    <div class="flex gap-2">
        <a href="{{ $csvUrl }}" class="btn btn-outline btn-sm">Unduh CSV</a>
        <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">Cetak / simpan PDF</button>
    </div>
</div>
