@php
    $user = auth()->user();

    $icons = [
        'home'     => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z',
        'calendar' => 'M7 3v4M17 3v4M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1z',
        'list'     => 'M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01',
        'plus'     => 'M12 8v8M8 12h8M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z',
        'room'     => 'M5 21V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16M3 21h18M15 9h4a1 1 0 0 1 1 1v11M9 9h2M9 13h2M9 17h2',
        'alert'    => 'M12 4 2.5 20h19L12 4zM12 10v4M12 17h.01',
        'flag'     => 'M5 21V4M5 4h11l-2 4 2 4H5',
        'login'    => 'M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3M14 8l4 4-4 4M18 12H8',
        'logout'   => 'M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 8l-4 4 4 4M6 12h10',
        'report'   => 'M7 3h8l4 4v14H7zM15 3v4h4M10 12h6M10 16h6',
    ];

    // [label, url, ikon, pola URL yang membuat menu ini aktif]
    if (! $user) {
        $items = [
            ['Kalender', '/', 'calendar', ['/']],
            ['Masuk', '/login', 'login', ['login']],
        ];
    } elseif ($user->isAdmin()) {
        $items = [
            ['Beranda', '/admin/dashboard', 'home', ['admin/dashboard']],
            ['Agenda', '/events', 'list', ['events', 'events/create', 'admin/events/*']],
            ['Ruangan', '/admin/rooms', 'room', ['admin/rooms']],
            ['Pengaduan', '/admin/complaints', 'alert', ['admin/complaints']],
            ['Laporan', '/admin/reports/daily', 'report', ['admin/reports/*']],
            ['Kalender', '/', 'calendar', ['/']],
        ];
    } elseif ($user->isPic()) {
        $items = [
            ['Pengaduan', '/pic/dashboard', 'alert', ['pic/dashboard']],
            ['Agenda', '/events', 'list', ['events']],
            ['Kalender', '/', 'calendar', ['/']],
        ];
    } else {
        $items = [
            ['Beranda', '/guru/dashboard', 'home', ['guru/dashboard']],
            ['Ajukan', '/events/create', 'plus', ['events/create']],
            ['Agenda', '/events', 'list', ['events']],
            ['Lapor', '/complaints/create', 'flag', ['complaints/create']],
            ['Kalender', '/', 'calendar', ['/']],
        ];
    }

    $homeUrl = $items[0][1];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head')
    <title>@yield('title', 'RAPI-Sekolah')</title>
    @stack('head')
</head>
<body class="min-h-screen bg-paper font-sans text-board antialiased">

    {{-- Sidebar (desktop) --}}
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col bg-board text-white md:flex print:hidden">
        <a href="{{ $homeUrl }}" class="px-6 pb-6 pt-7 text-xl font-bold tracking-tight">
            RAPI<span class="font-normal text-white/70">-Sekolah</span>
        </a>

        <nav aria-label="Menu utama" class="flex-1 overflow-y-auto px-3">
            <ul class="space-y-1">
                @foreach ($items as [$label, $url, $icon, $patterns])
                    @php $active = request()->is(...$patterns); @endphp
                    <li>
                        <a href="{{ $url }}" @if($active) aria-current="page" @endif
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                                  {{ $active ? 'bg-chalk text-board' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg>
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        @auth
            <div class="border-t border-white/15 p-4">
                <p class="truncate text-sm font-semibold">{{ $user->fullname }}</p>
                <p class="mb-3 text-xs text-white/70">{{ ucfirst($user->role->role_name) }}</p>
                <form action="/logout" method="POST">
                    @csrf
                    <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/80 transition hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icons['logout'] }}"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    <div class="md:pl-64 print:pl-0">

        {{-- Bar atas (mobile) --}}
        <header class="sticky top-0 z-20 flex items-center justify-between bg-board px-4 py-3 text-white md:hidden print:hidden">
            <a href="{{ $homeUrl }}" class="text-lg font-bold tracking-tight">
                RAPI<span class="font-normal text-white/70">-Sekolah</span>
            </a>
            @auth
                <form action="/logout" method="POST">
                    @csrf
                    <button class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-white/80 hover:bg-white/10" aria-label="Keluar">
                        <span class="max-w-[8rem] truncate">{{ $user->fullname }}</span>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icons['logout'] }}"/></svg>
                    </button>
                </form>
            @endauth
        </header>

        <main class="mx-auto max-w-4xl px-4 pb-28 pt-6 md:px-8 md:pb-12 md:pt-10 print:max-w-none print:p-0">

            @if (session('success'))
                <div role="status" class="mb-5 rounded-lg border border-board/20 bg-board/10 px-4 py-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg border border-chalk bg-chalk/30 px-4 py-3 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Tab bawah (mobile) --}}
    <nav aria-label="Menu utama" class="fixed inset-x-0 bottom-0 z-30 border-t border-white/10 bg-board text-white md:hidden print:hidden"
         style="padding-bottom: env(safe-area-inset-bottom, 0px)">
        <ul class="flex">
            @foreach ($items as [$label, $url, $icon, $patterns])
                @php $active = request()->is(...$patterns); @endphp
                <li class="relative flex-1">
                    @if ($active)
                        <span class="absolute left-1/2 top-0 h-0.5 w-8 -translate-x-1/2 rounded-b bg-chalk"></span>
                    @endif
                    <a href="{{ $url }}" @if($active) aria-current="page" @endif
                       class="flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium {{ $active ? 'text-chalk' : 'text-white/70' }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg>
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    @stack('scripts')
</body>
</html>
