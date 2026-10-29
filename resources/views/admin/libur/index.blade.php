<x-layouts.admin title="Kehadiran & Izin" subtitle="Pada hari libur presensi ditutup dan siswa tidak ditandai Alpa.">
    <x-slot:tabs>@include('admin.partials.attendance-tabs')</x-slot:tabs>

    <div id="libur-form" x-data="crudForm({
            storeUrl: @js(route('admin.libur.store')),
            updateUrl: @js(route('admin.libur.update', '__ID__')),
            defaults: { keterangan: '', libur_dari: @js(today()->toDateString()), libur_sampai: @js(today()->toDateString()) },
            old: @js(session()->getOldInput()),
            hasErrors: @js($errors->any()),
         })">
        <x-table-card :paginator="$liburs">
            <x-slot:header>
                <h2 class="text-xl font-semibold tracking-tight text-ink-900">{{ $liburs->total() }} <span class="font-medium">Hari Libur</span></h2>
                <button type="button" class="btn btn-primary" @click="create()"><x-icon name="plus" size="16" stroke="2.4" /> Tambah Libur</button>
            </x-slot:header>

            @if ($liburs->isEmpty())
                <x-empty-state icon="calendar-off" title="Belum ada hari libur" description="Tambahkan libur nasional atau libur sekolah agar presensi otomatis ditutup." />
            @else
                <table class="table">
                    <thead><tr><th>Keterangan</th><th>Tanggal</th><th>Durasi</th><th>Status</th><th class="w-24 text-right">Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($liburs as $libur)
                            @php
                                $durasi = (int) $libur->libur_dari->diffInDays($libur->libur_sampai) + 1;
                                [$label, $warna] = match (true) {
                                    $libur->libur_sampai->lt(today()) => ['Selesai', 'gray'],
                                    $libur->libur_dari->gt(today()) => ['Akan datang', 'blue'],
                                    default => ['Berlangsung', 'amber'],
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-10 items-center justify-center rounded-xl bg-brand-100 text-brand-800"><x-icon name="calendar-off" size="18" /></span>
                                        <span class="font-medium text-ink-900">{{ $libur->keterangan }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">
                                    {{ $libur->libur_dari->translatedFormat('j M Y') }}
                                    @unless ($libur->libur_dari->isSameDay($libur->libur_sampai)) – {{ $libur->libur_sampai->translatedFormat('j M Y') }} @endunless
                                </td>
                                <td class="whitespace-nowrap">{{ $durasi }} hari</td>
                                <td><x-badge :color="$warna">{{ $label }}</x-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-0.5">
                                        <button type="button" class="btn-icon size-8" title="Ubah"
                                                @click="edit({{ $libur->libur_id }}, @js([
                                                    'keterangan' => $libur->keterangan,
                                                    'libur_dari' => $libur->libur_dari->toDateString(),
                                                    'libur_sampai' => $libur->libur_sampai->toDateString(),
                                                ]))">
                                            <x-icon name="pencil" size="16" />
                                        </button>
                                        <x-delete-button :action="route('admin.libur.destroy', $libur)" title="Hapus hari libur?" :message="$libur->keterangan.' akan dihapus.'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-table-card>

        <x-modal title="Tambah Hari Libur" title-expr="isEditing ? 'Ubah Hari Libur' : 'Tambah Hari Libur'" max-width="md">
            <form method="POST" :action="action" class="space-y-4">
                <x-form.crud-meta form="libur-form" />
                <x-form.input name="keterangan" label="Keterangan" x-model="form.keterangan" placeholder="Mis. Libur Hari Raya" required />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form.input type="date" name="libur_dari" label="Dari tanggal" x-model="form.libur_dari" required />
                    <x-form.input type="date" name="libur_sampai" label="Sampai tanggal" x-model="form.libur_sampai" ::min="form.libur_dari" required />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn btn-secondary" @click="close()">Batal</button>
                    <button type="submit" class="btn btn-primary" x-text="isEditing ? 'Simpan Perubahan' : 'Tambah Libur'">Simpan</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-layouts.admin>
