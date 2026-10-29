@props(['title', 'heading', 'description', 'switchLabel' => null, 'switchHref' => null])

{{-- Halaman login split-screen: form di kiri, panel brand kuning di kanan. --}}
<x-layouts.guest :title="$title">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="flex flex-col px-6 py-8 sm:px-12 lg:px-20">
            <div class="flex items-center justify-between">
                <x-logo with-text />
                @if ($switchHref)
                    <a href="{{ $switchHref }}" class="text-sm font-medium text-ink-500 transition hover:text-ink-900">{{ $switchLabel }} →</a>
                @endif
            </div>

            <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center py-12">
                <h1 class="font-serif text-4xl font-medium tracking-tight text-ink-900">{{ $heading }}</h1>
                <p class="mt-2 text-ink-500">{{ $description }}</p>
                <div class="mt-8">{{ $slot }}</div>
            </div>

            <p class="text-xs text-ink-400">&copy; {{ now()->year }} {{ config('app.name') }} · {{ \App\Models\Setting::get('school_name') }}</p>
        </div>

        <div class="relative hidden overflow-hidden bg-brand-500 lg:block">
            <div class="absolute -top-24 -right-24 size-96 rounded-full bg-brand-400"></div>
            <div class="absolute -bottom-32 -left-16 size-[28rem] rounded-full bg-brand-600/30"></div>

            <div class="relative flex h-full flex-col justify-center px-16">
                <div class="w-full max-w-md rotate-[-2deg] rounded-2xl bg-white p-5 shadow-2xl shadow-brand-900/20">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="font-serif text-lg font-medium">Presensi Hari Ini</p>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">● Live</span>
                    </div>
                    @foreach ([['Budi Santoso', 'BS', '06:52', 'Hadir', 'green'], ['Citra Lestari', 'CL', '07:04', 'Hadir', 'green'], ['Agus Setiawan', 'AS', '07:21', 'Terlambat', 'amber'], ['Dewi Anggraini', 'DA', '—', 'Izin', 'blue']] as [$nama, $ini, $jam, $status, $warna])
                        <div class="flex items-center gap-3 border-t border-ink-100 py-3">
                            <x-avatar :initials="$ini" :seed="$nama" size="sm" />
                            <span class="flex-1 text-sm font-medium text-ink-800">{{ $nama }}</span>
                            <span class="text-sm text-ink-500 tabular-nums">{{ $jam }}</span>
                            <x-badge :color="$warna">{{ $status }}</x-badge>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 max-w-md text-ink-900">
                    <p class="font-serif text-3xl leading-snug font-medium">Presensi berbasis lokasi, tanpa antre dan tanpa kertas.</p>
                    <p class="mt-3 text-ink-800/80">Siswa cukup membuka ponsel di area sekolah. Rekap, izin, dan laporan tersusun otomatis.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
