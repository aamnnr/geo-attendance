@php
    use App\Enums\StatusPresensi;

    $statusAktif = $filters['status'] ?? null;
    $filterLain = collect($filters)->except('status')->filter()->all();
    $total = array_sum($ringkasan);
@endphp

<x-layouts.admin title="Kehadiran & Izin" subtitle="Rekap presensi seluruh siswa. Filter, koreksi, dan ekspor laporan.">
    <x-slot:tabs>@include('admin.partials.attendance-tabs')</x-slot:tabs>

    <div id="rekap-form" x-data="crudForm({
            storeUrl: @js(route('admin.rekap.store')),
            updateUrl: @js(route('admin.rekap.update', '__ID__')),
            defaults: { siswa_id: '', tanggal: @js(today()->toDateString()), status: 'hadir', jam_masuk: '', jam_keluar: '', keterangan: '' },
            old: @js(session()->getOldInput()),
            hasErrors: @js($errors->any()),
         })">

        {{-- Filter --}}
        <form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr_auto]" x-data @change="$el.requestSubmit()">
            @if ($statusAktif)
                <input type="hidden" name="status" value="{{ $statusAktif }}">
            @endif
            <div class="relative">
                <x-icon name="search" size="16" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama / NIS…" class="form-input pl-9" @change.stop>
            </div>
            <x-form.select name="kelas_id" :options="$kelasList->pluck('kelas_nama', 'kelas_id')" :value="$filters['kelas_id'] ?? null" placeholder="Semua kelas" />
            <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="form-input" aria-label="Dari tanggal">
            <input type="date" name="tanggal_sampai" value="{{ $filters['tanggal_sampai'] ?? '' }}" class="form-input" aria-label="Sampai tanggal">
            <div class="flex gap-2">
                <button type="submit" class="btn btn-dark flex-1"><x-icon name="filter" size="15" /> Terapkan</button>
                @if ($filterLain)
                    <a href="{{ route('admin.rekap.index', array_filter(['status' => $statusAktif])) }}" class="btn btn-ghost" title="Reset filter"><x-icon name="x" size="16" /></a>
                @endif
            </div>
        </form>

        <x-table-card :paginator="$rekaps">
            <x-slot:header>
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:gap-6">
                    <h2 class="text-xl font-semibold tracking-tight text-ink-900">{{ $rekaps->total() }} <span class="font-medium">Rekap</span></h2>
                    <nav class="segmented max-w-full overflow-x-auto" aria-label="Filter status">
                        <a href="{{ route('admin.rekap.index', $filterLain) }}" class="segmented-item" @if (! $statusAktif) aria-current="page" @endif>
                            Semua <span class="segmented-count">{{ $total }}</span>
                        </a>
                        @foreach (StatusPresensi::cases() as $status)
                            <a href="{{ route('admin.rekap.index', [...$filterLain, 'status' => $status->value]) }}" class="segmented-item"
                               @if ($statusAktif === $status->value) aria-current="page" @endif>
                                {{ $status->label() }} <span class="segmented-count">{{ $ringkasan[$status->value] }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" class="btn btn-secondary" @click="open = !open"><x-icon name="download" size="16" /> Ekspor <x-icon name="chevron-down" size="14" /></button>
                        <div x-cloak x-show="open" x-transition.origin.top.right class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-ink-200 bg-white p-1.5 shadow-pop">
                            <a href="{{ route('admin.rekap.export.excel', $filters) }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-700 hover:bg-ink-100">
                                <x-icon name="file-spreadsheet" size="16" class="text-emerald-600" /> Excel (.xlsx)
                            </a>
                            <a href="{{ route('admin.rekap.export.pdf', $filters) }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-700 hover:bg-ink-100">
                                <x-icon name="file-text" size="16" class="text-red-500" /> PDF
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" @click="create()"><x-icon name="plus" size="16" stroke="2.4" /> Tambah Rekap</button>
                </div>
            </x-slot:header>

            @if ($rekaps->isEmpty())
                <x-empty-state icon="clipboard" title="Tidak ada rekap" description="Belum ada data presensi yang cocok dengan filter ini." />
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Jarak</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th class="w-20"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekaps as $rekap)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.siswa.show', $rekap->siswa) }}" class="flex items-center gap-3">
                                        <x-avatar :initials="$rekap->siswa->inisial" :seed="$rekap->siswa->siswa_nama" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-ink-900">{{ $rekap->siswa->siswa_nama }}</p>
                                            <p class="truncate text-xs text-ink-400">{{ $rekap->siswa->kelas->kelas_nama }} · {{ $rekap->siswa->siswa_nis }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td class="whitespace-nowrap">{{ $rekap->tanggal->translatedFormat('D, j M Y') }}</td>
                                <td class="tabular-nums">{{ $rekap->jamMasuk() ?? '—' }}</td>
                                <td class="tabular-nums">{{ $rekap->jamKeluar() ?? '—' }}</td>
                                <td class="whitespace-nowrap text-ink-500 tabular-nums">{{ $rekap->jarak !== null ? $rekap->jarak.' m' : '—' }}</td>
                                <td><x-badge :status="$rekap->status" /></td>
                                <td class="max-w-56 truncate text-ink-500" title="{{ $rekap->keterangan }}">{{ $rekap->keterangan ?? '—' }}</td>
                                <td class="text-right whitespace-nowrap">
                                    <button type="button" class="btn-icon size-8" title="Ubah" aria-label="Ubah rekap"
                                            @click="edit({{ $rekap->rekap_id }}, @js([
                                                'siswa_id' => (string) $rekap->siswa_id,
                                                'tanggal' => $rekap->tanggal->toDateString(),
                                                'status' => $rekap->status->value,
                                                'jam_masuk' => $rekap->jamMasuk() ?? '',
                                                'jam_keluar' => $rekap->jamKeluar() ?? '',
                                                'keterangan' => $rekap->keterangan ?? '',
                                            ]))">
                                        <x-icon name="pencil" size="16" />
                                    </button>
                                    <x-delete-button :action="route('admin.rekap.destroy', $rekap)" title="Hapus rekap?"
                                                     message="Rekap {{ $rekap->siswa->siswa_nama }} tanggal {{ $rekap->tanggal->translatedFormat('j M Y') }} akan dihapus." />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-table-card>

        <x-modal title="Tambah Rekap" title-expr="isEditing ? 'Ubah Rekap' : 'Tambah Rekap'" description="Koreksi manual data presensi siswa." max-width="lg">
            <form method="POST" :action="action" class="space-y-4">
                <x-form.crud-meta form="rekap-form" />
                <x-form.select name="siswa_id" label="Siswa" x-model="form.siswa_id" placeholder="Pilih siswa…" required
                               :options="$siswaList->mapWithKeys(fn ($s) => [$s->siswa_id => $s->siswa_nama.' — '.$s->kelas->kelas_nama])" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form.input type="date" name="tanggal" label="Tanggal" x-model="form.tanggal" :max="today()->toDateString()" required />
                    <x-form.select name="status" label="Status" x-model="form.status" :options="StatusPresensi::options()" required />
                    <x-form.input type="time" name="jam_masuk" label="Jam masuk" x-model="form.jam_masuk" />
                    <x-form.input type="time" name="jam_keluar" label="Jam pulang" x-model="form.jam_keluar" />
                </div>
                <x-form.input name="keterangan" label="Keterangan" x-model="form.keterangan" placeholder="Opsional" />
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn btn-secondary" @click="close()">Batal</button>
                    <button type="submit" class="btn btn-primary" x-text="isEditing ? 'Simpan Perubahan' : 'Simpan Rekap'">Simpan</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-layouts.admin>
