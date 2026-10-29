@php
    use App\Enums\Hari;

    // Minggu hanya ditampilkan jika memang ada jadwal.
    $hariTampil = collect($hariList)->filter(fn (Hari $h) => $h !== Hari::Minggu || $jadwalPerHari->has($h->value));
    $hariOptions = collect($hariList)->mapWithKeys(fn (Hari $h) => [$h->value => $h->value]);
    $hariIni = Hari::fromDate(now());
@endphp

<x-layouts.admin title="Jadwal Pelajaran" subtitle="Jam pelajaran pertama menentukan batas terlambat, jam terakhir menentukan jam pulang.">
    <x-slot:actions>
        @if ($kelasAktif)
            <button type="button" class="btn btn-primary" @click="$dispatch('jadwal-create', { kelas_id: {{ $kelasAktif->kelas_id }} })">
                <x-icon name="plus" size="16" stroke="2.4" /> Tambah Jadwal
            </button>
        @endif
    </x-slot:actions>

    @if (! $kelasAktif)
        <div class="card">
            <x-empty-state icon="school" title="Belum ada kelas" description="Buat kelas terlebih dahulu untuk menyusun jadwal.">
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-primary">Kelola Kelas</a>
            </x-empty-state>
        </div>
    @else
        <div id="jadwal-form" x-data="crudForm({
                storeUrl: @js(route('admin.jadwal.store')),
                updateUrl: @js(route('admin.jadwal.update', '__ID__')),
                defaults: { kelas_id: @js((string) $kelasAktif->kelas_id), hari: 'Senin', mapel: '', guru: '', jam_mulai: '07:00', jam_selesai: '08:30' },
                old: @js(session()->getOldInput()),
                hasErrors: @js($errors->any()),
             })" @jadwal-create.window="create($event.detail)">

            {{-- Pemilih kelas --}}
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <nav class="segmented hidden max-w-full overflow-x-auto md:inline-flex" aria-label="Pilih kelas">
                    @foreach ($kelasList as $kelas)
                        <a href="{{ route('admin.jadwal.index', ['kelas_id' => $kelas->kelas_id]) }}" class="segmented-item"
                           @if ($kelas->is($kelasAktif)) aria-current="page" @endif>{{ $kelas->kelas_nama }}</a>
                    @endforeach
                </nav>
                <div class="relative md:hidden">
                    <select class="form-input appearance-none pr-10" aria-label="Pilih kelas"
                            @change="window.location = @js(route('admin.jadwal.index')) + '?kelas_id=' + $event.target.value">
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->kelas_id }}" @selected($kelas->is($kelasAktif))>{{ $kelas->kelas_nama }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" size="16" class="pointer-events-none absolute top-1/2 right-3.5 -translate-y-1/2 text-ink-400" />
                </div>
                <p class="text-sm text-ink-500">{{ $jadwalPerHari->sum(fn ($g) => $g->count()) }} jadwal di {{ $kelasAktif->kelas_nama }}</p>
            </div>

            {{-- Tampilan mingguan --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6">
                @foreach ($hariTampil as $hari)
                    @php $items = $jadwalPerHari->get($hari->value, collect()); @endphp
                    <section @class(['card flex flex-col', 'ring-2 ring-brand-400' => $hari === $hariIni])>
                        <header class="flex items-center justify-between border-b border-ink-100 px-4 py-3">
                            <div>
                                <h2 class="font-semibold text-ink-900">{{ $hari->value }}</h2>
                                <p class="text-xs text-ink-400">
                                    @if ($items->isNotEmpty())
                                        {{ $items->first()->jamMulai() }} – {{ $items->max('jam_selesai') ? substr($items->max('jam_selesai'), 0, 5) : '' }}
                                    @else
                                        Tidak ada pelajaran
                                    @endif
                                </p>
                            </div>
                            <button type="button" class="btn-icon size-8" title="Tambah jadwal {{ $hari->value }}"
                                    @click="create({ kelas_id: @js((string) $kelasAktif->kelas_id), hari: @js($hari->value) })">
                                <x-icon name="plus" size="16" />
                            </button>
                        </header>

                        <div class="flex-1 space-y-2 p-3">
                            @forelse ($items as $jadwal)
                                <div class="group rounded-xl border border-ink-100 bg-ink-50/60 p-3 transition hover:border-brand-300 hover:bg-brand-50/60">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-semibold text-brand-700 tabular-nums">{{ $jadwal->jamMulai() }} – {{ $jadwal->jamSelesai() }}</p>
                                        <div class="-mt-1 -mr-1 flex opacity-100 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
                                            <button type="button" class="btn-icon size-7" title="Ubah"
                                                    @click="edit({{ $jadwal->jadwal_id }}, @js([
                                                        'kelas_id' => (string) $jadwal->kelas_id,
                                                        'hari' => $jadwal->hari->value,
                                                        'mapel' => $jadwal->mapel,
                                                        'guru' => $jadwal->guru,
                                                        'jam_mulai' => $jadwal->jamMulai(),
                                                        'jam_selesai' => $jadwal->jamSelesai(),
                                                    ]))">
                                                <x-icon name="pencil" size="14" />
                                            </button>
                                            <x-delete-button :action="route('admin.jadwal.destroy', $jadwal)" class="size-7" title="Hapus jadwal?"
                                                             :message="$jadwal->mapel.' ('.$hari->value.', '.$jadwal->jamMulai().') akan dihapus.'" />
                                        </div>
                                    </div>
                                    <p class="mt-1 font-medium text-ink-900">{{ $jadwal->mapel }}</p>
                                    @if ($jadwal->guru)
                                        <p class="text-xs text-ink-500">{{ $jadwal->guru }}</p>
                                    @endif
                                </div>
                            @empty
                                <button type="button" class="flex w-full flex-col items-center justify-center gap-1 rounded-xl border border-dashed border-ink-200 py-6 text-xs text-ink-400 transition hover:border-brand-400 hover:text-brand-700"
                                        @click="create({ kelas_id: @js((string) $kelasAktif->kelas_id), hari: @js($hari->value) })">
                                    <x-icon name="plus" size="16" /> Tambah pelajaran
                                </button>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>

            <x-modal title="Tambah Jadwal" title-expr="isEditing ? 'Ubah Jadwal' : 'Tambah Jadwal'" max-width="lg">
                <form method="POST" :action="action" class="space-y-4">
                    <x-form.crud-meta form="jadwal-form" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form.select name="kelas_id" label="Kelas" x-model="form.kelas_id" :options="$kelasList->pluck('kelas_nama', 'kelas_id')" required />
                        <x-form.select name="hari" label="Hari" x-model="form.hari" :options="$hariOptions" required />
                    </div>
                    <x-form.input name="mapel" label="Mata pelajaran" x-model="form.mapel" placeholder="Mis. Matematika" required />
                    <x-form.input name="guru" label="Guru pengampu" x-model="form.guru" placeholder="Opsional" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form.input type="time" name="jam_mulai" label="Jam mulai" x-model="form.jam_mulai" required />
                        <x-form.input type="time" name="jam_selesai" label="Jam selesai" x-model="form.jam_selesai" ::min="form.jam_mulai" required />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="btn btn-secondary" @click="close()">Batal</button>
                        <button type="submit" class="btn btn-primary" x-text="isEditing ? 'Simpan Perubahan' : 'Tambah Jadwal'">Simpan</button>
                    </div>
                </form>
            </x-modal>
        </div>
    @endif
</x-layouts.admin>
