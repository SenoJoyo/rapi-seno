<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head')
    <title>Masuk - RAPI-Sekolah</title>
</head>
<body class="min-h-screen bg-board font-sans text-board antialiased">
    <div class="mx-auto flex min-h-screen max-w-sm flex-col justify-center px-4 py-10">
        <p class="mb-6 text-center text-2xl font-bold tracking-tight text-white">
            RAPI<span class="font-normal text-white/70">-Sekolah</span>
        </p>

        <div class="rounded-xl bg-paper p-6">
            <h1 class="mb-1 text-xl font-bold">Masuk</h1>
            <p class="mb-5 text-sm text-board/70">Gunakan akun dari admin sekolah.</p>

            @if ($errors->any())
                <div role="alert" class="mb-4 rounded-lg border border-chalk bg-chalk/30 px-3 py-2.5 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="label">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" class="field" autocomplete="username" required autofocus>
                </div>
                <div>
                    <label for="password" class="label">Password</label>
                    <input id="password" type="password" name="password" class="field" autocomplete="current-password" required>
                </div>
                <button class="btn btn-primary w-full">Masuk</button>
            </form>
        </div>

        <a href="/" class="mt-5 text-center text-sm text-white/80 underline underline-offset-4 hover:text-white">
            Lihat kalender tanpa masuk
        </a>
    </div>
</body>
</html>
