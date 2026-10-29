@php
    $aksi = $kondisi['aksi'];
    $bisaPresensi = in_array($aksi, ['masuk', 'pulang'], true);
    $rekap = $kondisi['rekap'];
@endphp

<x-layouts.siswa title="Presensi" :subtitle="$kondisi['tanggal']->translatedFormat('l, j F Y').' · '.$sekolah['nama']">
    <div x-data="presensiMap({ school: @js($sekolah) })" class="grid gap-6 lg:grid-cols-5">
        {{-- Peta --}}
        <div class="card relative overflow-hidden lg:col-span-3">
            <div x-ref="map" class="h-[22rem] w-full sm:h-[28rem] lg:h-[34rem]"></div>

            <div class="pointer-events-none absolute inset-x-3 top-3 flex flex-wrap gap-2">
                <span class="pointer-events-auto inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-xs font-medium text-ink-700 shadow-pop">
                    <span class="size-2 rounded-full bg-brand-500"></span> Radius {{ $sekolah['radius'] }} m
                </span>
                <span x-cloak x-show="status === 'ready'" class="pointer-events-auto inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-xs font-medium text-ink-700 shadow-pop">
                    <span class="size-2 rounded-full bg-sky-500"></span> Akurasi ±<span x-text="accuracy"></span> m
                </span>
            </div>
        </div>

        {{-- Panel status --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="card p-6">
                <p class="text-sm font-medium text-ink-500">Jarak ke sekolah</p>

                {{-- Mencari lokasi --}}
                <div x-show="status === 'locating'" class="mt-3 flex items-center gap-3">
                    <span class="size-6 animate-spin rounded-full border-[3px] border-brand-200 border-t-brand-500"></span>
                    <span class="text-sm text-ink-600">Mencari lokasi Anda…</span>
                </div>

                {{-- Gagal --}}
                <div x-cloak x-show="status === 'error'" class="mt-3 rounded-xl bg-red-50 p-4">
                    <p class="flex items-start gap-2 text-sm text-red-700"><x-icon name="alert" size="17" class="mt-0.5" /> <span x-text="error"></span></p>
                    <button type="button" class="btn btn-secondary btn-sm mt-3" @click="locate()"><x-icon name="refresh" size="14" /> Coba lagi</button>
                </div>

                {{-- Siap --}}
                <div x-cloak x-show="status === 'ready'" class="mt-1">
                    <p class="font-serif text-5xl font-medium tracking-tight text-ink-900 tabular-nums" x-text="distanceLabel"></p>
                    <span class="mt-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                          :class="inside ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                        <span class="size-1.5 rounded-full" :class="inside ? 'bg-emerald-500' : 'bg-red-500'"></span>
                        <span x-text="inside ? 'Di dalam area sekolah' : 'Di luar area sekolah'"></span>
                    </span>
                    <p x-show="!inside" class="mt-3 text-sm text-ink-500">Dekati area sekolah hingga berada dalam radius {{ $sekolah['radius'] }} m untuk dapat presensi.</p>
                </div>
            </div>

            <div class="card p-6">
                @if ($bisaPresensi)
                    <p class="text-sm text-ink-600">{{ $kondisi['pesan'] }}</p>
                    @if ($rekap?->jamMasuk())
                        <p class="mt-2 text-xs text-ink-500">Masuk tercatat pukul <strong class="text-ink-800">{{ $rekap->jamMasuk() }}</strong> · <x-badge :status="$rekap->status" /></p>
                    @endif

                    <form x-ref="form" method="POST" action="{{ route('siswa.presensi.store') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="latitude" :value="lat">
                        <input type="hidden" name="longitude" :value="lng">
                    </form>

                    <button type="button" @click="submit()" :disabled="!inside || submitting"
                            class="btn {{ $aksi === 'masuk' ? 'btn-primary' : 'btn-dark' }} mt-5 w-full py-4 text-base">
                        <span x-show="submitting" class="size-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                        <x-icon name="{{ $aksi === 'masuk' ? 'log-in' : 'log-out' }}" size="18" x-show="!submitting" />
                        {{ $aksi === 'masuk' ? 'Presensi Masuk' : 'Presensi Pulang' }}
                    </button>
                    <p class="mt-3 text-center text-xs text-ink-400">Lokasi diverifikasi ulang oleh server saat presensi dikirim.</p>
                @else
                    <div class="flex items-start gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full {{ $aksi === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-ink-100 text-ink-600' }}">
                            <x-icon :name="$aksi === 'selesai' ? 'check-circle' : 'info'" size="19" />
                        </span>
                        <div>
                            <p class="font-medium text-ink-900">{{ $aksi === 'selesai' ? 'Presensi lengkap' : 'Presensi tidak tersedia' }}</p>
                            <p class="mt-1 text-sm text-ink-500">{{ $kondisi['pesan'] }}</p>
                            @if ($rekap?->jamMasuk())
                                <p class="mt-2 text-sm text-ink-600 tabular-nums">Masuk {{ $rekap->jamMasuk() }} · Pulang {{ $rekap->jamKeluar() ?? '--:--' }}</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary mt-5 w-full">Kembali ke beranda</a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.siswa>
