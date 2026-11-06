<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function create(Request $request)
    {
        $event = $this->resolveEvent($request->query('event'));
        $rooms = Room::where('is_active', true)->get();

        return view('complaints.create', compact('rooms', 'event'));
    }

    public function store(Request $request)
    {
        $event = $this->resolveEvent($request->input('event_id'));

        $data = $request->validate([
            'room_id' => $event ? 'nullable' : 'required|exists:rooms,id',
            'description' => 'required|string',
            'photo' => 'required|image|max:5120',
        ]);

        $path = $request->file('photo')->store('complaints', 'public');

        Complaint::create([
            // Kalau dari tombol pada agenda, ruangan diambil dari agenda (bukan dari input).
            'room_id' => $event ? $event->room_id : $data['room_id'],
            'reporter_id' => Auth::id(),
            'description' => $data['description'],
            'photo_path' => $path,
            'status_repair' => 'Belum Diperbaiki',
        ]);

        if ($event) {
            return redirect('/events')->with('success', 'Laporan kerusakan untuk ruang ' . $event->room->room_name . ' berhasil dikirim.');
        }

        return back()->with('success', 'Pengaduan kerusakan berhasil dikirim.');
    }

    /**
     * Ambil agenda asal laporan. Guru hanya boleh melapor dari agenda miliknya, admin dari agenda mana pun.
     */
    private function resolveEvent($eventId): ?Event
    {
        if (! $eventId) {
            return null;
        }

        /** @var User $user */
        $user = Auth::user();
        $event = Event::with('room', 'user')->findOrFail($eventId);

        abort_unless($user->isAdmin() || $event->user_id === $user->id, 403);

        return $event;
    }

    public function picIndex()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->isPic(), 403);

        $roomIds = Room::where('pic_id', $user->id)->pluck('id');

        $complaints = Complaint::with('room', 'reporter')
            ->whereIn('room_id', $roomIds)
            ->latest('reported_at')
            ->get();

        return view('complaints.pic_index', compact('complaints'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->isPic(), 403);

        if ($complaint->room->pic_id !== $user->id) {
            abort(403, 'Anda bukan PIC untuk ruangan ini.');
        }

        $data = $request->validate([
            'status_repair' => 'required|in:Sudah Diperbaiki,Menunggu Spare Part,Belum Diperbaiki',
        ]);

        $complaint->update([
            'status_repair' => $data['status_repair'],
            'handler_id' => $user->id,
        ]);

        return back()->with('success', 'Status perbaikan diperbarui.');
    }

    public function whatsappWarning(Complaint $complaint)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $pic = $complaint->room->pic;

        if (! $pic || ! $pic->phone_number) {
            return back()->withErrors(['whatsapp' => 'PIC ruangan ini belum memiliki nomor telepon.']);
        }

        $phone = preg_replace('/^0/', '62', preg_replace('/\D/', '', $pic->phone_number));

        $message = "Peringatan: Kerusakan fasilitas di ruang \"{$complaint->room->room_name}\" "
            . "belum ditangani (status: {$complaint->status_repair}). "
            . "Mohon segera ditindaklanjuti. - Waka/Admin RAPI-Sekolah";

        $waLink = "https://wa.me/{$phone}?text=" . urlencode($message);

        return redirect()->away($waLink);
    }

    public function adminIndex()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $complaints = Complaint::with('room.pic')->latest('reported_at')->get();

        return view('complaints.admin_index', compact('complaints'));
    }

    /**
     * Admin: form edit pengaduan.
     */
    public function edit(Complaint $complaint)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $rooms = Room::where('is_active', true)->orWhere('id', $complaint->room_id)->get();

        return view('complaints.edit', compact('complaint', 'rooms'));
    }

    /**
     * Admin: simpan perubahan pengaduan (foto opsional).
     */
    public function update(Request $request, Complaint $complaint)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'description' => 'required|string',
            'status_repair' => 'required|in:Belum Diperbaiki,Menunggu Spare Part,Sudah Diperbaiki',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($complaint->photo_path) {
                Storage::disk('public')->delete($complaint->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('complaints', 'public');
        }

        unset($data['photo']);
        $complaint->update($data);

        return redirect('/admin/complaints')->with('success', 'Pengaduan berhasil diperbarui.');
    }

    /**
     * Admin: hapus pengaduan beserta file fotonya.
     */
    public function destroy(Complaint $complaint)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($currentUser->isAdmin(), 403);

        if ($complaint->photo_path) {
            Storage::disk('public')->delete($complaint->photo_path);
        }

        $complaint->delete();

        return redirect('/admin/complaints')->with('success', 'Pengaduan berhasil dihapus.');
    }
}
