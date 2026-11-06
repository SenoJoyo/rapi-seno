<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Room;
use App\Models\RoomNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function create()
    {
        $rooms = Room::where('is_active', true)->get();
        $today = now('Asia/Jakarta')->toDateString();
        return view('events.create', compact('rooms', 'today'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'event_name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:'.now('Asia/Jakarta')->toDateString(),
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'date.after_or_equal' => 'Tanggal tidak boleh di hari sebelumnya.',
        ]);

        $conflict = Event::conflicting(
            $data['room_id'],
            $data['date'],
            $data['start_time'],
            $data['end_time']
        )->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Ruangan sudah dipesan pada rentang waktu tersebut. Silakan pilih waktu atau ruang lain.',
            ]);
        }

        /** @var User $currentUser */
        $currentUser = Auth::user();

        Event::create([
            ...$data,
            'user_id' => $currentUser->id,
            'status' => $currentUser->isAdmin() ? 'disetujui' : 'diajukan',
        ]);

        return back()->with('success', 'Pengajuan peminjaman berhasil disimpan.');
    }

    public function index()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $notifications = RoomNotification::where('user_id', $currentUser->id)
            ->where('is_read', false)
            ->get();

        RoomNotification::where('user_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $events = Event::with('room', 'user')->latest()->get();
        return view('events.index', compact('events', 'notifications'));
    }

    /**
     * APPROVAL: Admin menyetujui agenda, pemohon otomatis mendapat notifikasi.
     */
    public function approve(Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        DB::transaction(function () use ($event, $currentUser) {
            $event->update([
                'status' => 'disetujui',
                'approved_by' => $currentUser->id,
                'reject_reason' => null,
            ]);
            $this->notifyRequester($event, 'disetujui');
        });

        return back()->with('success', 'Agenda telah disetujui dan pemohon sudah diberi tahu.');
    }

    public function rejectForm(Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $event->load('room', 'user');

        return view('events.reject', compact('event'));
    }

    /**
     * Admin menolak agenda, wajib menyertakan alasan yang dikirim ke pemohon.
     */
    public function reject(Request $request, Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $data = $request->validate([
            'reject_reason' => 'required|string|min:5|max:255',
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi.',
            'reject_reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($event, $data) {
            $event->update([
                'status' => 'ditolak',
                'reject_reason' => $data['reject_reason'],
            ]);
            $this->notifyRequester($event, 'ditolak', $data['reject_reason']);
        });

        return redirect('/events')->with('success', 'Agenda telah ditolak dan alasan sudah dikirim ke pemohon.');
    }

    /**
     * Kirim notifikasi hasil pengajuan ke pemohon (dilewati bila admin mengurus agendanya sendiri).
     */
    private function notifyRequester(Event $event, string $type, ?string $reason = null): void
    {
        if ($event->user_id === Auth::id()) {
            return;
        }

        RoomNotification::create([
            'user_id' => $event->user_id,
            'event_id' => $event->id,
            'type' => $type,
            'reason' => $reason,
        ]);
    }

    public function moveForm(Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $rooms = Room::where('is_active', true)->where('id', '!=', $event->room_id)->get();

        return view('events.move', compact('event', 'rooms'));
    }

    public function moveRoom(Request $request, Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $data = $request->validate([
            'new_room_id' => ['required', 'exists:rooms,id', Rule::notIn([$event->room_id])],
            'move_reason' => 'required|string|min:5|max:255',
        ], [
            'new_room_id.not_in' => 'Pilih ruangan yang berbeda dari ruangan saat ini.',
            'move_reason.required' => 'Alasan pemindahan wajib diisi.',
            'move_reason.min' => 'Alasan pemindahan minimal 5 karakter.',
        ]);

        $conflict = Event::conflicting(
            $data['new_room_id'],
            $event->date->format('Y-m-d'),
            $event->start_time,
            $event->end_time
        )->where('id', '!=', $event->id)->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'new_room_id' => 'Ruang tujuan juga sudah terpakai pada waktu tersebut.',
            ]);
        }

        DB::transaction(function () use ($event, $data) {
            $oldRoomName = $event->room->room_name;
            $newRoom = Room::findOrFail($data['new_room_id']);

            $event->update([
                'room_id' => $newRoom->id,
                'move_reason' => $data['move_reason'],
            ]);

            RoomNotification::create([
                'user_id' => $event->user_id,
                'event_id' => $event->id,
                'type' => 'pindah',
                'old_room_name' => $oldRoomName,
                'new_room_name' => $newRoom->room_name,
                'reason' => $data['move_reason'],
            ]);
        });

        return redirect('/events')->with('success', 'Ruangan berhasil dipindahkan dan notifikasi telah dikirim ke pemohon.');
    }

    /**
     * Admin: form edit agenda.
     */
    public function edit(Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $event->load('room', 'user');

        return view('events.edit', compact('event'));
    }

    /**
     * Admin: simpan perubahan agenda (ruangan diubah lewat fitur "Pindahkan ruangan" agar ada alasan).
     */
    public function update(Request $request, Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $today = now('Asia/Jakarta')->toDateString();
        $originalDate = $event->date->format('Y-m-d');

        $data = $request->validate([
            'event_name' => 'required|string|max:255',
            'date' => [
                'required', 'date',
                function ($attribute, $value, $fail) use ($today, $originalDate) {
                    // Tanggal lampau hanya ditolak kalau admin mengubah tanggalnya.
                    if ($value !== $originalDate && $value < $today) {
                        $fail('Tanggal tidak boleh di hari sebelumnya.');
                    }
                },
            ],
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'status' => 'required|in:diajukan,disetujui,ditolak',
            'reject_reason' => 'nullable|required_if:status,ditolak|string|min:5|max:255',
        ], [
            'reject_reason.required_if' => 'Alasan penolakan wajib diisi kalau status Ditolak.',
            'reject_reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        if (in_array($data['status'], ['diajukan', 'disetujui'], true)) {
            $conflict = Event::conflicting(
                $event->room_id,
                $data['date'],
                $data['start_time'],
                $data['end_time']
            )->where('id', '!=', $event->id)->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'start_time' => 'Ruangan sudah dipesan pada rentang waktu tersebut.',
                ]);
            }
        }

        $statusChanged = $data['status'] !== $event->status;
        $data['reject_reason'] = $data['status'] === 'ditolak' ? $data['reject_reason'] : null;

        DB::transaction(function () use ($event, $data, $statusChanged) {
            $event->update($data);

            if ($statusChanged && $data['status'] === 'disetujui') {
                $this->notifyRequester($event, 'disetujui');
            } elseif ($statusChanged && $data['status'] === 'ditolak') {
                $this->notifyRequester($event, 'ditolak', $data['reject_reason']);
            }
        });

        return redirect('/events')->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * Admin: hapus agenda (notifikasi terkait ikut terhapus lewat foreign key cascade).
     */
    public function destroy(Event $event)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $event->delete();

        return redirect('/events')->with('success', 'Agenda berhasil dihapus.');
    }
}
