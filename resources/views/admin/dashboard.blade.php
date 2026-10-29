@php
    $hadirHariIni = $hariIni['hadir'] + $hariIni['terlambat'];
    $maxTren = max(1, collect($tren)->max(fn ($d) => $d['hadir'] + $d['terlambat'] + $d['izin'] + $d['alpa']));
    $seri = [
        'hadir' => ['label' => 'Hadir', 'class' => 'bg-emerald-500'],
        'terlambat' => ['label' => 'Terlambat', 'class' => 'bg-brand-500'],
        'izin' => ['label' => 'Izin/Sakit', 'class' => 'bg-sky-400'],
        'alpa' => ['label' => 'Alpa', 'class' => 'bg-red-400'],
    ];
@endphp

<x-layouts.admin title="Dashboard" :subtitle="'Ringkasan presensi '.today()->translatedFormat('l, j F Y')">
    <x-slot:actions>
        <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary"><x-icon name="clipboard" size="16" /> Lihat Rekap</a>
        <a href="{{ route('admin.izin.index') }}" class="btn btn-primary">
            <x-icon name="file-text" size="16" /> Tinjau Izin
            @if ($jumlahIzinPending)
                <span class="rounded-full bg-ink-900 px-1.5 py-0.5 text-[11px] leading-none text-white">{{ $jumlahIzinPending }}</span>
            @endif
        </a>
    </x-slot:actions>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Hadir hari ini" :value="$hadirHariIni" icon="check-circle" tone="green"
                     :hint="$hariIni['terlambat'].' terlambat · '.$persentaseHadir.'% kehadiran'" />
        <x-stat-card label="Belum presensi" :value="$belumPresensi" icon="clock" tone="amber" :hint="'dari '.$totalSiswa.' siswa'" />
        <x-stat-card label="Izin menunggu" :value="$jumlahIzinPending" icon="file-text" tone="blue" hint="perlu ditinjau" :href="route('admin.izin.index')" />
        <x-stat-card label="Total siswa" :value="$totalSiswa" icon="users" tone="brand" :hint="$totalKelas.' kelas'" :href="route('admin.siswa.index')" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Grafik 7 hari --}}
        <section class="card p-5 xl:col-span-2" x-data="{ aktif: null }">
            <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-ink-900">Tren 7 hari terakhir</h2>
                    <p class="text-sm text-ink-500">Jumlah rekap per status setiap hari.</p>
                </div>
                <div class="flex flex-wrap gap-3 text-xs text-ink-500">
                    @foreach ($seri as $s)
                        <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm {{ $s['class'] }}"></span>{{ $s['label'] }}</span>
                    @endforeach
                </div>
            </div>

            <div class="flex h-56 items-end gap-2 sm:gap-4">
                @foreach ($tren as $i => $hari)
                    @php $total = $hari['hadir'] + $hari['terlambat'] + $hari['izin'] + $hari['alpa']; @endphp
                    <div class="relative flex h-full flex-1 flex-col items-center justify-end gap-2"
                         @mouseenter="aktif = {{ $i }}" @mouseleave="aktif = null">
                        <div x-cloak x-show="aktif === {{ $i }}" x-transition.opacity
                             class="absolute bottom-full z-10 mb-2 w-36 rounded-lg bg-ink-900 p-2.5 text-xs text-white shadow-pop">
                            <p class="mb-1 font-semibold">{{ $hari['tanggal']->translatedFormat('l, j M') }}</p>
                            @foreach ($seri as $key => $s)
                                <p class="flex justify-between"><span class="text-ink-300">{{ $s['label'] }}</span><span class="tabular-nums">{{ $hari[$key] }}</span></p>
                            @endforeach
                        </div>
                        <div class="flex w-full max-w-12 flex-col-reverse overflow-hidden rounded-lg bg-ink-100 transition"
                             :class="aktif === {{ $i }} ? 'ring-2 ring-ink-900/10' : ''"
                             style="height: {{ $total ? max(4, round($total / $maxTren * 100)) : 4 }}%">
                            @foreach ($seri as $key => $s)
                                @if ($hari[$key])
                                    <div class="{{ $s['class'] }}" style="height: {{ $hari[$key] / max(1, $total) * 100 }}%"></div>
                                @endif
                            @endforeach
                        </div>
                        <span @class(['text-xs', 'font-semibold text-ink-900' => $hari['tanggal']->isToday(), 'text-ink-500' => ! $hari['tanggal']->isToday()])>
                            {{ $hari['tanggal']->translatedFormat('D') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Izin menunggu --}}
        <section class="card flex flex-col">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <h2 class="font-semibold text-ink-900">Izin menunggu</h2>
                <a href="{{ route('admin.izin.index') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">Lihat semua</a>
            </div>
            @forelse ($izinPending as $izin)
                <a href="{{ route('admin.izin.index') }}" class="flex items-center gap-3 border-b border-ink-100 px-5 py-3 transition last:border-b-0 hover:bg-brand-50/50">
                    <x-avatar :initials="$izin->siswa->inisial" :seed="$izin->siswa->siswa_nama" size="sm" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-ink-900">{{ $izin->siswa->siswa_nama }}</p>
                        <p class="truncate text-xs text-ink-500">{{ $izin->jenis->label() }} · {{ $izin->periode }}</p>
                    </div>
                    <x-icon name="chevron-right" size="16" class="text-ink-300" />
                </a>
            @empty
                <x-empty-state icon="check-circle" title="Tidak ada antrean" description="Semua pengajuan izin sudah diproses." class="flex-1" />
            @endforelse
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Presensi terbaru --}}
        <section class="card overflow-hidden xl:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-200 px-5 py-4">
                <h2 class="font-semibold text-ink-900">Presensi terbaru hari ini</h2>
                <a href="{{ route('admin.rekap.index', ['tanggal_mulai' => today()->toDateString(), 'tanggal_sampai' => today()->toDateString()]) }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">Rekap hari ini</a>
            </div>
            @if ($presensiTerbaru->isEmpty())
                <x-empty-state icon="map-pin" title="Belum ada presensi" description="Presensi siswa hari ini akan muncul di sini secara otomatis." />
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr><th>Nama</th><th>Masuk</th><th>Pulang</th><th>Jarak</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($presensiTerbaru as $rekap)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <x-avatar :initials="$rekap->siswa->inisial" :seed="$rekap->siswa->siswa_nama" size="sm" />
                                            <div class="min-w-0">
                                                <p class="truncate font-medium text-ink-900">{{ $rekap->siswa->siswa_nama }}</p>
                                                <p class="truncate text-xs text-ink-400">{{ $rekap->siswa->kelas->kelas_nama }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="tabular-nums">{{ $rekap->jamMasuk() ?? '—' }}</td>
                                    <td class="tabular-nums">{{ $rekap->jamKeluar() ?? '—' }}</td>
                                    <td class="tabular-nums">{{ $rekap->jarak !== null ? $rekap->jarak.' m' : '—' }}</td>
                                    <td><x-badge :status="$rekap->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Aktivitas --}}
        <section class="card">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <h2 class="font-semibold text-ink-900">Aktivitas terbaru</h2>
                <a href="{{ route('admin.log') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">Semua log</a>
            </div>
            @forelse ($aktivitas as $log)
                <div class="flex gap-3 border-b border-ink-100 px-5 py-3 last:border-b-0">
                    <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-500"></span>
                    <div class="min-w-0">
                        <p class="text-sm text-ink-700">{{ $log->pesan }}</p>
                        <p class="mt-0.5 text-xs text-ink-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <x-empty-state icon="history" title="Belum ada aktivitas" />
            @endforelse
        </section>
    </div>
</x-layouts.admin>
