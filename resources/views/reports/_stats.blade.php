{{-- Kartu ringkasan. Butuh $loanStats dan $damageStats. --}}
<div class="grid gap-6 sm:grid-cols-2">
    <section aria-labelledby="stat-loans">
        <h2 id="stat-loans" class="mb-2 text-sm font-bold uppercase tracking-wide text-board/70">Peminjaman ruang</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ([['Total pengajuan', $loanStats['total']], ['Disetujui', $loanStats['disetujui']], ['Menunggu', $loanStats['diajukan']], ['Ditolak', $loanStats['ditolak']]] as [$label, $value])
                <div class="card p-4">
                    <p class="text-2xl font-bold">{{ $value }}</p>
                    <p class="text-xs text-board/70">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section aria-labelledby="stat-damages">
        <h2 id="stat-damages" class="mb-2 text-sm font-bold uppercase tracking-wide text-board/70">Kerusakan</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ([['Total laporan', $damageStats['total']], ['Belum diperbaiki', $damageStats['belum']], ['Menunggu spare part', $damageStats['menunggu']], ['Sudah diperbaiki', $damageStats['sudah']]] as [$label, $value])
                <div class="card p-4">
                    <p class="text-2xl font-bold">{{ $value }}</p>
                    <p class="text-xs text-board/70">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
