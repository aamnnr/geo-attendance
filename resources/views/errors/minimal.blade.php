<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-white">
    <div class="fixed inset-x-0 top-0 h-1 bg-brand-500"></div>

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-6">
        <div class="absolute -top-32 -right-32 size-96 rounded-full bg-brand-100"></div>
        <div class="absolute -bottom-40 -left-24 size-[28rem] rounded-full bg-brand-50"></div>

        <div class="relative max-w-md text-center">
            <p class="font-serif text-8xl font-semibold tracking-tight text-brand-500 sm:text-9xl">@yield('code')</p>
            <h1 class="mt-4 font-serif text-3xl font-medium tracking-tight text-ink-900">@yield('message')</h1>
            <p class="mt-3 text-ink-500">Maaf, halaman yang Anda tuju tidak dapat ditampilkan saat ini.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ url('/') }}" class="btn btn-dark">Kembali ke beranda</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Halaman sebelumnya</a>
            </div>
        </div>
    </main>
</body>
</html>
