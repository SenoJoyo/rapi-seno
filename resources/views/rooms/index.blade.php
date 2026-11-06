@extends('layouts.app')
@section('title', 'Kelola Ruangan')

@section('content')
    <h1 class="mb-6 text-2xl font-bold">Ruangan</h1>

    @if (session('pic_credentials'))
        @php $cred = session('pic_credentials'); @endphp
        <div role="status" class="mb-6 rounded-lg border border-chalk bg-chalk/30 px-4 py-4 text-sm">
            <p class="font-bold">Akun PIC untuk ruang {{ $cred['room'] }} sudah dibuat</p>
            <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
                <dt class="text-board/70">Nama</dt><dd class="font-semibold">{{ $cred['fullname'] }}</dd>
                <dt class="text-board/70">Username</dt><dd class="font-mono font-semibold">{{ $cred['username'] }}</dd>
                <dt class="text-board/70">Password</dt><dd class="font-mono font-semibold">{{ $cred['password'] }}</dd>
            </dl>
            <p class="mt-3 text-xs text-board/80">Catat dan berikan ke PIC sekarang. Password hanya ditampilkan sekali dan tidak bisa dilihat lagi setelah halaman ini ditutup.</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-5">
        <form method="POST" action="/admin/rooms" class="card space-y-4 p-5 lg:col-span-2 lg:self-start">
            @csrf
            <h2 class="font-bold">Tambah ruangan</h2>
            <div>
                <label for="room_name" class="label">Nama ruangan</label>
                <input id="room_name" type="text" name="room_name" value="{{ old('room_name') }}" class="field" required>
            </div>
            <div>
                <label for="capacity" class="label">Kapasitas</label>
                <input id="capacity" type="number" min="0" name="capacity" value="{{ old('capacity') }}" class="field" required>
            </div>
            <div>
                <label for="location" class="label">Lokasi</label>
                <input id="location" type="text" name="location" value="{{ old('location') }}" class="field">
            </div>
            <div>
                <label for="pic_id" class="label">PIC ruangan</label>
                <select id="pic_id" name="pic_id" class="field">
                    <option value="">Buat akun PIC otomatis (disarankan)</option>
                    @foreach ($picUsers as $pic)
                        <option value="{{ $pic->id }}" @selected(old('pic_id') == $pic->id)>Pakai PIC yang sudah ada: {{ $pic->fullname }}</option>
                    @endforeach
                </select>
                <p class="hint">Akun PIC baru dibuat otomatis saat ruangan disimpan, lengkap dengan username dan password.</p>
            </div>
            <div>
                <label for="pic_phone" class="label">Nomor WhatsApp PIC baru <span class="font-normal text-board/60">(opsional)</span></label>
                <input id="pic_phone" type="tel" name="pic_phone" value="{{ old('pic_phone') }}" class="field" placeholder="08123456789">
                <p class="hint">Dipakai untuk pengingat kerusakan. Diabaikan kalau memilih PIC yang sudah ada.</p>
            </div>
            <button class="btn btn-primary w-full">Tambah ruangan</button>
        </form>

        <section class="lg:col-span-3">
            <h2 class="mb-3 font-bold">Daftar ruangan</h2>
            <ul class="card divide-y divide-board/10">
                @forelse ($rooms as $room)
                    <li class="p-4">
                        <p class="font-semibold">{{ $room->room_name }}</p>
                        <p class="text-sm text-board/70">
                            Kapasitas {{ $room->capacity }} orang{{ $room->location ? ', '.$room->location : '' }}
                        </p>
                        <p class="text-sm text-board/70">PIC: {{ $room->pic->fullname ?? 'Belum ditentukan' }}@if ($room->pic) <span class="font-mono text-xs">({{ $room->pic->username }})</span>@endif</p>
                    </li>
                @empty
                    <li class="p-6 text-center text-sm text-board/70">Belum ada ruangan. Tambahkan lewat formulir.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
