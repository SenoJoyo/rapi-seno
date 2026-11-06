<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /** Domain email untuk akun PIC yang dibuat otomatis. */
    private const PIC_EMAIL_DOMAIN = 'sekolah.sch.id';

    public function index()
    {
        $rooms = Room::with('pic')->get();
        $picUsers = User::whereHas('role', fn ($q) => $q->where('role_name', 'pic'))->get();

        return view('rooms.index', compact('rooms', 'picUsers'));
    }

    public function store(Request $request)
    {
        $picRoleIds = Role::where('role_name', 'pic')->pluck('id')->all();

        $data = $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            // Kosong = akun PIC dibuat otomatis untuk ruangan ini.
            'pic_id' => ['nullable', Rule::exists('users', 'id')->whereIn('role_id', $picRoleIds)],
            'pic_phone' => 'nullable|string|max:20',
        ]);

        $credentials = null;

        DB::transaction(function () use (&$data, &$credentials) {
            if (empty($data['pic_id'])) {
                [$pic, $password] = $this->createPicAccount($data['room_name'], $data['pic_phone'] ?? null);

                $data['pic_id'] = $pic->id;
                $credentials = [
                    'room' => $data['room_name'],
                    'fullname' => $pic->fullname,
                    'username' => $pic->username,
                    'password' => $password,
                ];
            }

            Room::create(Arr::only($data, ['room_name', 'capacity', 'location', 'pic_id']));
        });

        return back()
            ->with('success', 'Ruangan berhasil ditambahkan.')
            ->with('pic_credentials', $credentials);
    }

    /**
     * Buat akun PIC baru untuk sebuah ruangan.
     *
     * @return array{0: User, 1: string} akun PIC dan password polos (hanya ditampilkan sekali).
     */
    private function createPicAccount(string $roomName, ?string $phone): array
    {
        $slug = Str::limit(Str::slug($roomName), 40, '');
        $base = 'pic-'.($slug !== '' ? $slug : 'ruang');

        $username = $base;
        $suffix = 2;
        while (
            User::where('username', $username)->exists()
            || User::where('email', $username.'@'.self::PIC_EMAIL_DOMAIN)->exists()
        ) {
            $username = $base.'-'.$suffix++;
        }

        $password = Str::password(10, symbols: false, spaces: false);

        $pic = User::create([
            'username' => $username,
            'fullname' => 'PIC '.$roomName,
            'email' => $username.'@'.self::PIC_EMAIL_DOMAIN,
            'phone_number' => $phone,
            'password' => $password, // di-hash otomatis oleh cast pada model User
            'role_id' => Role::firstOrCreate(['role_name' => 'pic'])->id,
        ]);

        return [$pic, $password];
    }
}
