{{-- Foto kerusakan: thumbnail yang bisa diklik untuk dilihat besar (lightbox). Butuh $c (Complaint). --}}
@if ($c->photo_path)
    @php $photoUrl = asset('storage/' . $c->photo_path); @endphp
    <button type="button" class="group mt-3 block overflow-hidden rounded-lg border border-board/10 text-left"
            onclick="document.getElementById('photo-{{ $c->id }}').showModal()"
            aria-label="Lihat foto kerusakan ruang {{ $c->room->room_name }}">
        <img src="{{ $photoUrl }}" alt="" loading="lazy" class="h-32 w-48 object-cover transition group-hover:opacity-90">
        <span class="block bg-board/5 px-3 py-1.5 text-xs font-semibold">Lihat foto</span>
    </button>

    <dialog id="photo-{{ $c->id }}" class="m-auto w-[min(92vw,56rem)] rounded-xl border-0 bg-white p-0 shadow-2xl backdrop:bg-black/70"
            onclick="if (event.target === this) this.close()">
        <div class="flex items-center justify-between gap-3 border-b border-board/10 px-4 py-3">
            <div class="min-w-0">
                <p class="truncate font-semibold">{{ $c->room->room_name }}</p>
                <p class="truncate text-xs text-board/70">{{ $c->description }}</p>
            </div>
            <button type="button" class="btn btn-outline btn-sm shrink-0" onclick="this.closest('dialog').close()">Tutup</button>
        </div>
        <div class="bg-board/5 p-3">
            <img src="{{ $photoUrl }}" alt="Foto kerusakan ruang {{ $c->room->room_name }}" class="mx-auto max-h-[75vh] w-auto max-w-full rounded-lg object-contain">
        </div>
        <div class="px-4 py-2 text-right">
            <a href="{{ $photoUrl }}" target="_blank" rel="noopener" class="text-sm font-semibold underline underline-offset-4">Buka di tab baru</a>
        </div>
    </dialog>
@endif
