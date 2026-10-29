<x-layouts.admin title="Kelas" subtitle="Daftar rombongan belajar beserta wali kelasnya.">
    <x-slot:actions>
        <button type="button" class="btn btn-primary" @click="$dispatch('kelas-create')"><x-icon name="plus" size="16" stroke="2.4" /> Tambah Kelas</button>
    </x-slot:actions>

    <div id="kelas-form" x-data="crudForm({
            storeUrl: @js(route('admin.kelas.store')),
            updateUrl: @js(route('admin.kelas.update', '__ID__')),
            defaults: { kelas_nama: '', wali_kelas: '' },
            old: @js(session()->getOldInput()),
            hasErrors: @js($errors->any()),
         })" @kelas-create.window="create()">

        @if ($kelas->isEmpty())
            <div class="card">
                <x-empty-state icon="school" title="Belum ada kelas" description="Tambahkan kelas terlebih dahulu sebelum menambahkan siswa dan jadwal.">
                    <button type="button" class="btn btn-primary" @click="create()"><x-icon name="plus" size="16" /> Tambah Kelas</button>
                </x-empty-state>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($kelas as $item)
                    <article class="card group flex flex-col p-5 transition hover:-translate-y-0.5 hover:shadow-pop">
                        <div class="flex items-start justify-between gap-3">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-brand-100 text-brand-800"><x-icon name="school" size="20" /></span>
                            <div class="flex items-center gap-0.5 opacity-100 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
                                <button type="button" class="btn-icon size-8" title="Ubah"
                                        @click="edit({{ $item->kelas_id }}, @js($item->only('kelas_nama', 'wali_kelas')))">
                                    <x-icon name="pencil" size="16" />
                                </button>
                                <x-delete-button :action="route('admin.kelas.destroy', $item)" title="Hapus kelas?"
                                                 :message="'Kelas '.$item->kelas_nama.' beserta jadwalnya akan dihapus.'" />
                            </div>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold text-ink-900">{{ $item->kelas_nama }}</h2>
                        <p class="text-sm text-ink-500">{{ $item->wali_kelas ? 'Wali kelas: '.$item->wali_kelas : 'Wali kelas belum diatur' }}</p>

                        <div class="mt-5 flex gap-2 border-t border-ink-100 pt-4 text-sm">
                            <a href="{{ route('admin.siswa.index', ['kelas_id' => $item->kelas_id]) }}" class="flex flex-1 items-center gap-2 rounded-lg px-2 py-1.5 text-ink-600 hover:bg-ink-50">
                                <x-icon name="users" size="16" class="text-ink-400" /> <span class="font-semibold text-ink-900">{{ $item->siswa_count }}</span> siswa
                            </a>
                            <a href="{{ route('admin.jadwal.index', ['kelas_id' => $item->kelas_id]) }}" class="flex flex-1 items-center gap-2 rounded-lg px-2 py-1.5 text-ink-600 hover:bg-ink-50">
                                <x-icon name="calendar-clock" size="16" class="text-ink-400" /> <span class="font-semibold text-ink-900">{{ $item->jadwal_count }}</span> jadwal
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <x-modal title="Tambah Kelas" title-expr="isEditing ? 'Ubah Kelas' : 'Tambah Kelas'" max-width="md">
            <form method="POST" :action="action" class="space-y-4">
                <x-form.crud-meta form="kelas-form" />
                <x-form.input name="kelas_nama" label="Nama kelas" x-model="form.kelas_nama" placeholder="Mis. X RPL 1" required />
                <x-form.input name="wali_kelas" label="Wali kelas" x-model="form.wali_kelas" placeholder="Nama guru wali kelas" />
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="btn btn-secondary" @click="close()">Batal</button>
                    <button type="submit" class="btn btn-primary" x-text="isEditing ? 'Simpan Perubahan' : 'Tambah Kelas'">Simpan</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-layouts.admin>
