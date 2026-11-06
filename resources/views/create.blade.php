<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Peminjaman Ruang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="max-w-md mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Pengajuan Peminjaman Ruang & Agenda</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-3 text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/events" class="bg-white p-4 rounded shadow space-y-3">
        @csrf
        <div>
            <label class="block text-sm">Nama Agenda</label>
            <input type="text" name="event_name" class="w-full border rounded p-2" required>
        </div>
        <div>
            <label class="block text-sm">Ruangan</label>
            <select name="room_id" class="w-full border rounded p-2" required>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm">Tanggal</label>
            <input type="date" name="date" class="w-full border rounded p-2" required>
        </div>
        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-sm">Jam Mulai</label>
                <input type="time" name="start_time" class="w-full border rounded p-2" required>
            </div>
            <div class="flex-1">
                <label class="block text-sm">Jam Selesai</label>
                <input type="time" name="end_time" class="w-full border rounded p-2" required>
            </div>
        </div>
        <button class="bg-blue-700 text-white px-4 py-2 rounded w-full">Ajukan</button>
    </form>

    <a href="/events" class="block text-center text-sm mt-3 text-blue-700">Lihat Semua Agenda</a>
</div>
</body>
</html>