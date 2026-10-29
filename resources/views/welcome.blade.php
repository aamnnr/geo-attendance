@php
    $fitur = [
        ['icon' => 'map-pin', 'judul' => 'Geofencing', 'teks' => 'Presensi hanya bisa dilakukan di dalam radius sekolah. Jarak dihitung otomatis dari GPS ponsel.'],
        ['icon' => 'clipboard', 'judul' => 'Rekap otomatis', 'teks' => 'Status hadir, terlambat, dan alpa tercatat sendiri setiap hari tanpa input manual.'],
        ['icon' => 'file-text', 'judul' => 'Izin online', 'teks' => 'Siswa mengajukan izin beserta lampiran; admin menyetujui dalam satu klik.'],
        ['icon' => 'download', 'judul' => 'Export Excel & PDF', 'teks' => 'Unduh laporan presensi per kelas dan per periode untuk arsip atau rapat.'],
    ];
@endphp

<x-layouts.guest title="Presensi Berbasis Lokasi">
    <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-10">
        <x-logo with-text />
        <nav class="flex items-center gap-2">
            <a href="{{ route('admin.login') }}" class="btn btn-ghost hidden sm:inline-flex">Admin</a>
            <a href="{{ route('siswa.login') }}" class="btn btn-dark">Masuk <x-icon name="arrow-right" size="15" /></a>
        </nav>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl items-center gap-14 px-6 pt-10 pb-20 lg:grid-cols-2 lg:px-10 lg:pt-16">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-medium text-brand-800">
                    <span class="size-1.5 rounded-full bg-brand-500"></span> {{ \App\Models\Setting::get('school_name') }}
                </span>
                <h1 class="mt-6 font-serif text-5xl leading-[1.05] font-medium tracking-tight text-ink-900 sm:text-6xl">
                    Presensi sekolah, <span class="relative whitespace-nowrap"><span class="relative z-10">cukup dari ponsel.</span><span class="absolute inset-x-0 bottom-1 -z-0 h-3 bg-brand-300/70"></span></span>
                </h1>
                <p class="mt-6 max-w-lg text-lg text-ink-500">
                    {{ config('app.name') }} mencatat kehadiran siswa berdasarkan lokasi. Tanpa antre, tanpa kertas, dan rekap langsung siap diunduh.
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('siswa.login') }}" class="btn btn-primary px-6 py-3 text-base"><x-icon name="map-pin" size="18" /> Masuk sebagai Siswa</a>
                    <a href="{{ route('admin.login') }}" class="btn btn-secondary px-6 py-3 text-base"><x-icon name="shield" size="18" /> Masuk sebagai Admin</a>
                </div>
            </div>

            {{-- Kartu pratinjau gaya referensi --}}
            <div class="relative">
                <div class="absolute -inset-6 -z-10 rounded-[2.5rem] bg-brand-500"></div>
                <div class="absolute -top-10 -right-6 -z-10 size-40 rounded-full bg-brand-300"></div>
                <div class="card overflow-hidden shadow-2xl shadow-brand-900/20">
                    <div class="flex items-center gap-1.5 border-b border-ink-100 bg-ink-50 px-4 py-3">
                        <span class="size-2.5 rounded-full bg-red-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-emerald-400"></span>
                    </div>
                    <div class="p-5">
                        <p class="font-serif text-2xl font-medium text-ink-900">Kehadiran & Izin</p>
                        <div class="mt-4 flex items-center gap-4">
                            <p class="text-lg font-semibold">12 <span class="font-medium">Pengajuan</span></p>
                            <div class="segmented">
                                <span class="segmented-item" aria-current="page">Menunggu <span class="segmented-count">4</span></span>
                                <span class="segmented-item">Disetujui <span class="segmented-count">8</span></span>
                            </div>
                        </div>
                        <table class="table mt-4">
                            <tbody>
                                @foreach ([['Budi Santoso', 'BS', 'Sakit', '2 hari', true], ['Citra Lestari', 'CL', 'Urusan Keluarga', '1 hari', true], ['Agus Setiawan', 'AS', 'Dispensasi', '3 hari', false]] as [$nama, $ini, $jenis, $durasi, $pending])
                                    <tr>
                                        <td class="pl-0"><div class="flex items-center gap-3"><x-avatar :initials="$ini" :seed="$nama" size="sm" /><span class="font-medium text-ink-900">{{ $nama }}</span></div></td>
                                        <td class="hidden sm:table-cell">{{ $durasi }}</td>
                                        <td>{{ $jenis }}</td>
                                        <td class="pr-0 text-right">
                                            @if ($pending)
                                                <span class="inline-flex gap-1 text-emerald-600"><x-icon name="check-circle" size="19" /><x-icon name="x-circle" size="19" class="text-red-500" /></span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-sm font-medium"><x-icon name="check" size="14" class="text-emerald-600" /> Disetujui</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-ink-100 bg-ink-50">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-10">
                <h2 class="max-w-xl font-serif text-3xl font-medium tracking-tight text-ink-900 sm:text-4xl">Semua yang dibutuhkan untuk presensi harian.</h2>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($fitur as $f)
                        <div class="card p-6 transition hover:-translate-y-1 hover:shadow-pop">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-brand-100 text-brand-800"><x-icon :name="$f['icon']" size="20" /></span>
                            <h3 class="mt-5 font-semibold text-ink-900">{{ $f['judul'] }}</h3>
                            <p class="mt-2 text-sm text-ink-500">{{ $f['teks'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="mx-auto max-w-7xl px-6 py-8 text-xs text-ink-400 lg:px-10">
        &copy; {{ now()->year }} {{ config('app.name') }} · {{ \App\Models\Setting::get('school_name') }}
    </footer>
</x-layouts.guest>
