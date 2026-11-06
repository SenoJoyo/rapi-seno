@extends('layouts.app')
@section('title', 'Tolak Agenda')

@section('content')
    <a href="/events" class="text-sm font-semibold underline underline-offset-4">Kembali ke agenda</a>

    <h1 class="mb-6 mt-3 text-2xl font-bold">Tolak pengajuan agenda</h1>

    <div class="card mb-5 max-w-xl p-5">
        <p class="font-semibold">{{ $event->event_name }}</p>
        <p class="mt-1 text-sm text-board/70">
            {{ $event->room->room_name }}<br>
            {{ $event->date->locale('id')->translatedFormat('j F Y') }}, {{ substr($event->start_time, 0, 5) }} sampai {{ substr($event->end_time, 0, 5) }}<br>
            Diajukan oleh {{ $event->user->fullname }}
        </p>
    </div>

    <form method="POST" action="/admin/events/{{ $event->id }}/reject" class="card max-w-xl space-y-4 p-5">
        @csrf
        <div>
            <label for="reject_reason" class="label">Alasan penolakan</label>
            <textarea id="reject_reason" name="reject_reason" rows="3" minlength="5" maxlength="255" class="field" required>{{ old('reject_reason') }}</textarea>
            <p class="hint">Alasan ini dikirim ke pemohon sebagai pemberitahuan.</p>
        </div>
        <button class="btn btn-primary w-full sm:w-auto">Tolak dan beri tahu pemohon</button>
    </form>
@endsection
