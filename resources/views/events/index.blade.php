@extends('layouts.app')
@section('title', 'Agenda')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">Agenda</h1>
        @if (auth()->user()->isAdmin() || auth()->user()->isGuru())
            <a href="/events/create" class="btn btn-primary">Ajukan agenda</a>
        @endif
    </div>

    @foreach ($notifications ?? [] as $notif)
        @if ($notif->type === 'disetujui')
            <div role="status" class="mb-4 rounded-lg border border-board/20 bg-board/10 px-4 py-3 text-sm">
                <p class="font-semibold">Pengajuan berhasil: agenda disetujui</p>
                <p>Agenda "{{ $notif->event->event_name ?? '-' }}" sudah disetujui admin.</p>
            </div>
        @elseif ($notif->type === 'ditolak')
            <div role="alert" class="mb-4 rounded-lg border border-chalk bg-chalk/30 px-4 py-3 text-sm">
                <p class="font-semibold">Pengajuan tidak berhasil: agenda ditolak</p>
                <p>Agenda "{{ $notif->event->event_name ?? '-' }}" ditolak admin.</p>
                <p class="text-board/80">Alasan: {{ $notif->reason }}</p>
            </div>
        @else
            <div role="status" class="mb-4 rounded-lg border border-chalk bg-chalk/30 px-4 py-3 text-sm">
                <p class="font-semibold">Ruangan agenda Anda dipindahkan</p>
                <p>Dari {{ $notif->old_room_name }} ke {{ $notif->new_room_name }}.</p>
                <p class="text-board/80">Alasan: {{ $notif->reason }}</p>
            </div>
        @endif
    @endforeach

    <ul class="card divide-y divide-board/10">
        @forelse ($events as $event)
            <li class="flex gap-4 p-4">
                <div class="w-12 shrink-0 text-center">
                    <div class="text-2xl font-bold leading-none">{{ $event->date->format('d') }}</div>
                    <div class="mt-1 text-xs text-board/70">{{ $event->date->locale('id')->translatedFormat('M') }}</div>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-semibold">{{ $event->event_name }}</h2>
                        <span class="badge {{ $event->status === 'disetujui' ? 'badge-done' : ($event->status === 'ditolak' ? 'badge-void' : 'badge-soft') }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-sm text-board/70">
                        {{ $event->room->room_name }}, {{ substr($event->start_time, 0, 5) }} sampai {{ substr($event->end_time, 0, 5) }}
                    </p>
                    <p class="text-xs text-board/60">Diajukan oleh {{ $event->user->fullname }}</p>

                    @if ($event->status === 'ditolak' && $event->reject_reason)
                        <p class="mt-2 rounded-md bg-chalk/30 px-2.5 py-1.5 text-xs">
                            Ditolak. Alasan: {{ $event->reject_reason }}
                        </p>
                    @endif

                    @if ($event->move_reason)
                        <p class="mt-2 rounded-md bg-chalk/30 px-2.5 py-1.5 text-xs">
                            Ruangan dipindah. Alasan: {{ $event->move_reason }}
                        </p>
                    @endif

                    @if (auth()->user()->isAdmin() || (auth()->user()->isGuru() && $event->user_id === auth()->id()))
                        <div class="mt-3">
                            <a href="/complaints/create?event={{ $event->id }}" class="btn btn-accent btn-sm">Lapor kerusakan</a>
                        </div>
                    @endif

                    @if (auth()->user()->isAdmin())
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if ($event->status === 'diajukan')
                                <form method="POST" action="/admin/events/{{ $event->id }}/approve">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">Setujui</button>
                                </form>
                                <a href="/admin/events/{{ $event->id }}/reject" class="btn btn-outline btn-sm">Tolak</a>
                            @endif
                            <a href="/admin/events/{{ $event->id }}/move" class="btn btn-outline btn-sm">Pindahkan ruangan</a>
                            <a href="/admin/events/{{ $event->id }}/edit" class="btn btn-outline btn-sm">Edit</a>
                            <form method="POST" action="/admin/events/{{ $event->id }}"
                                  onsubmit="return confirm('Hapus agenda &quot;{{ addslashes($event->event_name) }}&quot;? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline btn-sm">Hapus</button>
                            </form>
                        </div>
                    @endif
                </div>
            </li>
        @empty
            <li class="p-6 text-center text-sm text-board/70">Belum ada agenda.</li>
        @endforelse
    </ul>
@endsection
