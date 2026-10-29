@props([
    'title',
    'subtitle' => null,
    'nav' => [],
    'user' => ['nama' => '', 'peran' => '', 'inisial' => ''],
    'logoutRoute',
    'profileRoute' => null,
    'bottomNav' => false,
])

{{--
    Kerangka aplikasi (admin & siswa): rail ikon di kiri seperti referensi,
    judul serif, slot "tabs" untuk tab bergaris bawah, dan slot "actions".
    Jangan dipakai langsung; gunakan <x-layouts.admin> atau <x-layouts.siswa>.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='9' fill='%23f5b622'/><path d='M16 7a7 7 0 0 0-7 7c0 5 7 11 7 11s7-6 7-11a7 7 0 0 0-7-7Zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z' fill='%2316161a'/></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen" x-data="{ drawer: false }" @keydown.escape.window="drawer = false">
    <div class="fixed inset-x-0 top-0 z-50 h-1 bg-brand-500"></div>

    {{-- Rail navigasi (desktop) --}}
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-20 flex-col items-center border-r border-ink-200/70 bg-white pt-6 pb-5 lg:flex">
        <x-logo class="mb-8" />

        <nav class="flex flex-1 flex-col items-center gap-2" aria-label="Navigasi utama">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['active'] ?? $item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   @class([
                       'group relative flex size-11 items-center justify-center rounded-full transition',
                       'bg-brand-500 text-ink-900 shadow-sm shadow-brand-500/40' => $active,
                       'text-ink-500 hover:bg-ink-100 hover:text-ink-900' => ! $active,
                   ])
                   @if ($active) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" size="20" />
                    <span class="sr-only">{{ $item['label'] }}</span>
                    @if (! empty($item['badge']))
                        <span class="absolute -top-0.5 -right-0.5 flex min-w-4.5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] leading-4.5 font-bold text-white ring-2 ring-white">{{ $item['badge'] }}</span>
                    @endif
                    <span class="pointer-events-none absolute left-full ml-3 rounded-md bg-ink-900 px-2.5 py-1.5 text-xs font-medium whitespace-nowrap text-white opacity-0 shadow-pop transition group-hover:opacity-100 group-focus-visible:opacity-100">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <x-user-menu :user="$user" :logout-route="$logoutRoute" :profile-route="$profileRoute" placement="right" />
    </aside>

    {{-- Top bar (mobile) --}}
    <header class="sticky top-1 z-30 flex items-center justify-between border-b border-ink-200/70 bg-white/90 px-4 py-3 backdrop-blur lg:hidden">
        <button type="button" class="btn-icon -ml-2" @click="drawer = true" aria-label="Buka menu">
            <x-icon name="menu" size="22" />
        </button>
        <x-logo with-text />
        <x-user-menu :user="$user" :logout-route="$logoutRoute" :profile-route="$profileRoute" placement="bottom" />
    </header>

    {{-- Drawer (mobile) --}}
    <div x-cloak x-show="drawer" class="fixed inset-0 z-50 lg:hidden">
        <div x-show="drawer" x-transition.opacity class="absolute inset-0 bg-ink-900/40" @click="drawer = false"></div>
        <nav x-show="drawer"
             x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
             class="absolute inset-y-0 left-0 flex w-72 flex-col bg-white p-5 shadow-pop">
            <div class="mb-6 flex items-center justify-between">
                <x-logo with-text />
                <button type="button" class="btn-icon" @click="drawer = false" aria-label="Tutup menu"><x-icon name="x" /></button>
            </div>
            <div class="space-y-1">
                @foreach ($nav as $item)
                    @php $active = request()->routeIs($item['active'] ?? $item['route']); @endphp
                    <a href="{{ route($item['route']) }}" @class([
                        'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                        'bg-brand-500 text-ink-900' => $active,
                        'text-ink-600 hover:bg-ink-100' => ! $active,
                    ])>
                        <x-icon :name="$item['icon']" size="19" />
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if (! empty($item['badge']))
                            <span class="rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </nav>
    </div>

    <div class="flex min-h-screen flex-col pt-1 lg:pl-20 {{ $bottomNav ? 'pb-20 lg:pb-0' : '' }}">
        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-10 lg:py-9">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="font-serif text-[1.75rem] leading-tight font-medium tracking-tight text-ink-900 sm:text-4xl">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-1.5 text-sm text-ink-500">{{ $subtitle }}</p>
                    @endif
                </div>
                @if (isset($actions) && $actions->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
                @endif
            </div>

            @if (isset($tabs) && $tabs->isNotEmpty())
                <nav class="-mx-4 mb-6 flex gap-6 overflow-x-auto overflow-y-hidden border-b border-ink-200 px-4 sm:mx-0 sm:px-0">{{ $tabs }}</nav>
            @endif

            {{ $slot }}
        </main>

        <footer class="mx-auto w-full max-w-7xl px-4 py-5 text-xs text-ink-400 sm:px-6 lg:px-10">
            &copy; {{ config('app.name') }} {{ now()->year }} &middot; {{ \App\Models\Setting::get('school_name') }}
        </footer>
    </div>

    {{-- Bottom navigation (mobile, portal siswa) --}}
    @if ($bottomNav)
        <nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 border-t border-ink-200 bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur lg:hidden" aria-label="Navigasi bawah">
            @foreach (collect($nav)->take(5) as $item)
                @php $active = request()->routeIs($item['active'] ?? $item['route']); @endphp
                <a href="{{ route($item['route']) }}" @class([
                    'flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium transition',
                    'text-ink-900' => $active,
                    'text-ink-400' => ! $active,
                ])>
                    <span @class(['flex h-7 w-12 items-center justify-center rounded-full transition', 'bg-brand-500' => $active])>
                        <x-icon :name="$item['icon']" size="19" />
                    </span>
                    {{ $item['short'] ?? $item['label'] }}
                </a>
            @endforeach
        </nav>
    @endif

    <x-toasts />
    <x-confirm-dialog />
    @stack('scripts')
</body>
</html>
