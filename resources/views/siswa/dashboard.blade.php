@php
    use App\Enums\StatusPresensi;

    $rekap = $kondisi['rekap'];
    $aksi = $kondisi['aksi'];
    $bisaPresensi = in_array($aksi, ['masuk', 'pulang'], true);
    $jam = (int) now()->format('H');
    $sapaan = match (true) {
        $jam < 11 => 'Selamat pagi',
        $jam < 15 => 'Selamat siang',
        $jam < 18 => 'Selamat sore',
        default => 'Selamat malam',
    };
    $infoAksi = [
        'masuk' => ['icon' => 'map-pin', 'tone' => 'bg-brand-100 text-brand-800'],
        'pulang' => ['icon' => 'log-out', 'tone' => 'bg-sky-100 text-sky-700'],
        'selesai' => ['icon' => 'check-circle', 'tone' => 'bg-emerald-100 text-emerald-700'],
        'tutup' => ['icon' => 'clock', 'tone' => 'bg-red-100 text-red-700'],
        'libur' => ['icon' => 'calendar-off', 'tone' => 'bg-violet-100 text-violet-700'],
        'izin' => ['icon' => 'file-text', 'tone' => 'bg-sky-100 text-sky-700'],
        'tidak_ada_jadwal' => ['icon' => 'calendar', 'tone' => 'bg-ink-100 text-ink-600'],
    ][$aksi];
    $keliling = 2 * pi() * 42;
    $totalBulan = array_sum($bulanIni);
@endphp

<x-layouts.siswa :title="$sapaan.', '.str($siswa->siswa_nama)->before(' ')" :subtitle="$siswa->kelas->kelas_nama.' · NIS '.$siswa->siswa_nis">
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Kartu hari ini --}}
        <section class="card relative overflow-hidden lg:col-span-2">
            <div class="absolute -top-20 -right-20 size-64 rounded-full bg-brand-50"></div>
            <div class="relative p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-ink-500">{{ $kondisi['tanggal']->translatedFormat('l, j F Y') }}</p>
                        <p class="mt-1 font-serif text-5xl font-medium tracking-tight text-ink-900 tabular-nums"
                           x-data="{ now: '' }"
                           x-init="const tick = () => now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); tick(); setInterval(tick, 1000)"
                           x-text="now">{{ now()->format('H:i:s') }}</p>
                    </div>
                    @if ($rekap)
                        <x-badge :status="$rekap->status" class="text-sm" />
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-ink-200/70 bg-white/80 p-4">
                        <p class="flex items-center gap-1.5 text-xs font-medium text-ink-500"><x-icon name="log-in" size="14" /> Jam masuk</p>
                        <p class="mt-1 text-2xl font-semibold text-ink-900 tabular-nums">{{ $rekap?->jamMasuk() ?? '--:--' }}</p>
                    </div>
                    <div class="rounded-xl border border-ink-200/70 bg-white/80 p-4">
                        <p class="flex items-center gap-1.5 text-xs font-medium text-ink-500"><x-icon name="log-out" size="14" /> Jam pulang</p>
                        <p class="mt-1 text-2xl font-semibold text-ink-900 tabular-nums">{{ $rekap?->jamKeluar() ?? '--:--' }}</p>
                    </div>
                </div>

                <div class="mt-5 flex flex-col gap-4 rounded-xl bg-ink-50 p-4 sm:flex-row sm:items-center">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full {{ $infoAksi['tone'] }}">
                        <x-icon :name="$infoAksi['icon']" size="19" />
                    </span>
                    <p class="flex-1 text-sm text-ink-700">{{ $kondisi['pesan'] }}</p>
                    @if ($bisaPresensi)
                        <a href="{{ route('siswa.presensi') }}" class="btn btn-primary">
                            <x-icon name="map-pin" size="16" /> {{ $aksi === 'masuk' ? 'Presensi Masuk' : 'Presensi Pulang' }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Ringkasan bulan ini --}}
        <section class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-ink-900">Bulan {{ now()->translatedFormat('F') }}</h2>
                <a href="{{ route('siswa.rekap') }}" class="text-xs font-medium text-ink-500 hover:text-ink-900">Detail →</a>
            </div>

            <div class="mt-5 flex items-center gap-5">
                <div class="relative size-28 shrink-0">
                    <svg viewBox="0 0 100 100" class="size-full -rotate-90">
                        <circle cx="50" cy="50" r="42" fill="none" stroke-width="10" class="stroke-ink-100" />
                        <circle cx="50" cy="50" r="42" fill="none" stroke-width="10" stroke-linecap="round" class="stroke-brand-500 transition-all duration-700"
                                stroke-dasharray="{{ $keliling }}" stroke-dashoffset="{{ $keliling * (1 - $persentaseHadir / 100) }}" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-semibold text-ink-900 tabular-nums">{{ $persentaseHadir }}%</span>
                        <span class="text-[11px] text-ink-500">kehadiran</span>
                    </div>
                </div>
                <p class="text-sm text-ink-500">{{ $totalBulan }} hari tercatat bulan ini. Pertahankan kehadiranmu!</p>
            </div>

            <dl class="mt-5 grid grid-cols-2 gap-2">
                @foreach (StatusPresensi::cases() as $status)
                    <div class="flex items-center justify-between rounded-lg border border-ink-100 px-3 py-2">
                        <dt><x-badge :status="$status" /></dt>
                        <dd class="font-semibold text-ink-900 tabular-nums">{{ $bulanIni[$status->value] ?? 0 }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- Jadwal hari ini --}}
        <section class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-ink-900">Jadwal hari ini</h2>
                <a href="{{ route('siswa.jadwal') }}" class="text-xs font-medium text-ink-500 hover:text-ink-900">Semua →</a>
            </div>
            @forelse ($kondisi['jadwal'] as $jadwal)
                <div class="mt-4 flex gap-3">
                    <div class="w-14 shrink-0 text-right text-xs leading-5 text-ink-500 tabular-nums">{{ $jadwal->jamMulai() }}<br>{{ $jadwal->jamSelesai() }}</div>
                    <div class="flex-1 border-l-2 border-brand-400 pl-3">
                        <p class="text-sm font-medium text-ink-900">{{ $jadwal->mapel }}</p>
                        <p class="text-xs text-ink-500">{{ $jadwal->guru ?? '—' }}</p>
                    </div>
                </div>
            @empty
                <x-empty-state icon="calendar" title="Tidak ada pelajaran" description="Tidak ada jadwal untuk kelasmu hari ini." class="py-8" />
            @endforelse
        </section>

        {{-- Riwayat terakhir --}}
        <section class="card overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-200 px-6 py-4">
                <h2 class="font-semibold text-ink-900">Riwayat presensi terakhir</h2>
                <a href="{{ route('siswa.rekap') }}" class="text-xs font-medium text-ink-500 hover:text-ink-900">Lihat semua →</a>
            </div>
            @if ($riwayat->isEmpty())
                <x-empty-state icon="clipboard" title="Belum ada riwayat" description="Riwayat presensimu akan muncul di sini." />
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($riwayat as $item)
                        <li class="flex items-center gap-4 px-6 py-3.5">
                            <div class="flex size-11 shrink-0 flex-col items-center justify-center rounded-xl bg-ink-50 leading-none">
                                <span class="text-base font-semibold text-ink-900">{{ $item->tanggal->format('j') }}</span>
                                <span class="text-[10px] text-ink-500 uppercase">{{ $item->tanggal->translatedFormat('M') }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink-900">{{ $item->tanggal->translatedFormat('l') }}</p>
                                <p class="text-xs text-ink-500 tabular-nums">{{ $item->jamMasuk() ?? '--:--' }} – {{ $item->jamKeluar() ?? '--:--' }}</p>
                            </div>
                            <x-badge :status="$item->status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.siswa>
