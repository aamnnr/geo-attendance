<x-layouts.siswa title="Jadwal Pelajaran" :subtitle="'Kelas '.$kelas->kelas_nama">
    <div x-data="{ hari: @js($hariIni->value) }">
        <nav class="-mx-4 mb-6 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0" aria-label="Pilih hari">
            @foreach ($hariList as $hari)
                @php $jumlah = $jadwalPerHari->get($hari->value)?->count() ?? 0; @endphp
                <button type="button" @click="hari = @js($hari->value)"
                        class="flex shrink-0 flex-col items-center rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                        :class="hari === @js($hari->value) ? 'border-ink-900 bg-ink-900 text-white shadow-sm' : 'border-ink-200 bg-white text-ink-600 hover:border-ink-300'">
                    <span>{{ $hari->value }}</span>
                    <span class="mt-0.5 text-[11px] font-normal opacity-70">
                        {{ $hari === $hariIni ? 'Hari ini' : ($jumlah ? $jumlah.' mapel' : 'Libur') }}
                    </span>
                </button>
            @endforeach
        </nav>

        @foreach ($hariList as $hari)
            @php $daftar = $jadwalPerHari->get($hari->value, collect()); @endphp
            <section x-show="hari === @js($hari->value)" @if ($hari !== $hariIni) x-cloak @endif class="card p-6 sm:p-8">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="font-serif text-2xl font-medium text-ink-900">{{ $hari->value }}</h2>
                    <span class="text-sm text-ink-500">{{ $daftar->count() }} mata pelajaran</span>
                </div>

                @if ($daftar->isEmpty())
                    <x-empty-state icon="calendar" title="Tidak ada pelajaran" description="Tidak ada jadwal di hari {{ $hari->value }}." />
                @else
                    <ol class="relative space-y-4 border-l-2 border-ink-100 pl-6">
                        @foreach ($daftar as $jadwal)
                            <li class="relative">
                                <span class="absolute top-1.5 -left-[31px] size-3.5 rounded-full border-[3px] border-white bg-brand-500 ring-2 ring-brand-200"></span>
                                <div class="flex flex-col gap-1 rounded-xl border border-ink-200/70 p-4 transition hover:border-brand-300 hover:bg-brand-50/40 sm:flex-row sm:items-center sm:gap-6">
                                    <p class="w-28 shrink-0 text-sm font-semibold text-ink-900 tabular-nums">{{ $jadwal->jamMulai() }} – {{ $jadwal->jamSelesai() }}</p>
                                    <div class="min-w-0">
                                        <p class="font-medium text-ink-900">{{ $jadwal->mapel }}</p>
                                        <p class="flex items-center gap-1.5 text-sm text-ink-500"><x-icon name="user" size="14" /> {{ $jadwal->guru ?? 'Guru belum ditentukan' }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        @endforeach
    </div>
</x-layouts.siswa>
