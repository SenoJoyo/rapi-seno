<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Event;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Laporan harian & bulanan untuk peminjaman ruang (events) dan kerusakan (complaints).
 * Semua batas hari/bulan dihitung dalam WIB.
 */
class ReportController extends Controller
{
    private const TZ = 'Asia/Jakarta';

    public function daily(Request $request)
    {
        $day = $this->parseDay($request->query('date'));
        $data = $this->gather($day->copy()->startOfDay(), $day->copy()->endOfDay());

        if ($request->query('format') === 'csv') {
            return $this->csvResponse('laporan-harian-'.$day->format('Y-m-d').'.csv', $data['events'], $data['complaints']);
        }

        return view('reports.daily', $data + [
            'day' => $day,
            'prev' => $day->copy()->subDay()->format('Y-m-d'),
            'next' => $day->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    public function monthly(Request $request)
    {
        $month = $this->parseMonth($request->query('month'));
        $from = $month->copy()->startOfMonth();
        $to = $month->copy()->endOfMonth();
        $data = $this->gather($from, $to);

        if ($request->query('format') === 'csv') {
            return $this->csvResponse('laporan-bulanan-'.$month->format('Y-m').'.csv', $data['events'], $data['complaints']);
        }

        /** @var Collection $events */
        $events = $data['events'];
        /** @var Collection $complaints */
        $complaints = $data['complaints'];

        // Rekap per ruangan (hanya ruangan yang ada aktivitas pada bulan ini).
        $perRoom = Room::orderBy('room_name')->get()
            ->map(function (Room $room) use ($events, $complaints) {
                $roomEvents = $events->where('room_id', $room->id);
                $roomComplaints = $complaints->where('room_id', $room->id);

                return [
                    'room' => $room,
                    'loans' => $roomEvents->count(),
                    'approved' => $roomEvents->where('status', 'disetujui')->count(),
                    'hours' => $this->approvedHours($roomEvents),
                    'damages' => $roomComplaints->count(),
                    'open' => $roomComplaints->where('status_repair', '!=', 'Sudah Diperbaiki')->count(),
                ];
            })
            ->filter(fn (array $row) => $row['loans'] > 0 || $row['damages'] > 0)
            ->values();

        // Rekap per hari (hanya hari yang ada aktivitas).
        $loansByDay = $events->groupBy(fn (Event $e) => $e->date->format('Y-m-d'));
        $damagesByDay = $complaints->groupBy(fn (Complaint $c) => $c->reportedAtLocal()->format('Y-m-d'));

        $perDay = collect(CarbonPeriod::create($from, $to))
            ->map(function ($date) use ($loansByDay, $damagesByDay) {
                $key = $date->format('Y-m-d');
                $loans = $loansByDay->get($key, collect());

                return [
                    'date' => Carbon::parse($key, self::TZ),
                    'loans' => $loans->count(),
                    'approved' => $loans->where('status', 'disetujui')->count(),
                    'damages' => $damagesByDay->get($key, collect())->count(),
                ];
            })
            ->filter(fn (array $row) => $row['loans'] > 0 || $row['damages'] > 0)
            ->values();

        return view('reports.monthly', $data + [
            'month' => $month,
            'prev' => $month->copy()->subMonthNoOverflow()->format('Y-m'),
            'next' => $month->copy()->addMonthNoOverflow()->format('Y-m'),
            'perRoom' => $perRoom,
            'perDay' => $perDay,
            'totalHours' => $this->approvedHours($events),
        ]);
    }

    /**
     * Ambil data & ringkasan untuk rentang $from..$to (WIB).
     */
    private function gather(Carbon $from, Carbon $to): array
    {
        $events = Event::with('room', 'user')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // created_at disimpan dalam UTC, jadi batas WIB dikonversi dulu ke UTC.
        $complaints = Complaint::with('room.pic', 'reporter', 'handler')
            ->whereBetween('created_at', [$from->copy()->utc(), $to->copy()->utc()])
            ->orderBy('created_at')
            ->get();

        return [
            'events' => $events,
            'complaints' => $complaints,
            'loanStats' => [
                'total' => $events->count(),
                'disetujui' => $events->where('status', 'disetujui')->count(),
                'diajukan' => $events->where('status', 'diajukan')->count(),
                'ditolak' => $events->where('status', 'ditolak')->count(),
            ],
            'damageStats' => [
                'total' => $complaints->count(),
                'belum' => $complaints->where('status_repair', 'Belum Diperbaiki')->count(),
                'menunggu' => $complaints->where('status_repair', 'Menunggu Spare Part')->count(),
                'sudah' => $complaints->where('status_repair', 'Sudah Diperbaiki')->count(),
            ],
            // Semua pengaduan yang belum selesai sampai saat ini, di luar periode laporan.
            'backlog' => Complaint::where('status_repair', '!=', 'Sudah Diperbaiki')->count(),
        ];
    }

    /** Total jam pemakaian dari agenda yang disetujui. */
    private function approvedHours(Collection $events): float
    {
        $minutes = $events->where('status', 'disetujui')->sum(
            fn (Event $e) => Carbon::parse($e->start_time)->diffInMinutes(Carbon::parse($e->end_time), true)
        );

        return round($minutes / 60, 1);
    }

    private function parseDay(?string $value): Carbon
    {
        try {
            $date = Carbon::createFromFormat('!Y-m-d', (string) $value, self::TZ);

            if ($date && $date->format('Y-m-d') === $value) {
                return $date;
            }
        } catch (\Throwable) {
            // jatuh ke hari ini
        }

        return now(self::TZ)->startOfDay();
    }

    private function parseMonth(?string $value): Carbon
    {
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value.'-01', self::TZ);

            if ($date && $date->format('Y-m') === $value) {
                return $date;
            }
        } catch (\Throwable) {
            // jatuh ke bulan ini
        }

        return now(self::TZ)->startOfMonth();
    }

    private function csvResponse(string $filename, Collection $events, Collection $complaints)
    {
        return response()->streamDownload(function () use ($events, $complaints) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8

            $put = fn (array $row) => fputcsv($out, array_map([self::class, 'safeCell'], $row), ';', '"', '\\');

            $put(['PEMINJAMAN RUANG']);
            $put(['Tanggal', 'Mulai', 'Selesai', 'Ruangan', 'Kegiatan', 'Peminjam', 'Status', 'Keterangan']);
            foreach ($events as $e) {
                $put([
                    $e->date->format('d/m/Y'),
                    substr($e->start_time, 0, 5),
                    substr($e->end_time, 0, 5),
                    $e->room->room_name,
                    $e->event_name,
                    $e->user->fullname,
                    ucfirst($e->status),
                    $e->status === 'ditolak' ? $e->reject_reason : $e->move_reason,
                ]);
            }

            $put([]);
            $put(['KERUSAKAN']);
            $put(['Tanggal lapor', 'Jam', 'Ruangan', 'Deskripsi', 'Pelapor', 'PIC', 'Status perbaikan', 'Ditangani oleh']);
            foreach ($complaints as $c) {
                $at = $c->reportedAtLocal();
                $put([
                    $at->format('d/m/Y'),
                    $at->format('H:i'),
                    $c->room->room_name,
                    $c->description,
                    $c->reporter->fullname ?? '-',
                    $c->room->pic->fullname ?? '-',
                    $c->status_repair,
                    $c->handler->fullname ?? '-',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Cegah formula injection saat CSV dibuka di Excel/Sheets. */
    public static function safeCell(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
