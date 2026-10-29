<x-layouts.admin title="Data Siswa" subtitle="Kelola akun siswa yang dapat melakukan presensi.">
    <x-slot:actions>
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary"><x-icon name="plus" size="16" stroke="2.4" /> Tambah Siswa</a>
    </x-slot:actions>

    <x-table-card :paginator="$siswas">
        <x-slot:header>
            <h2 class="text-xl font-semibold tracking-tight text-ink-900">{{ $siswas->total() }} <span class="font-medium">Siswa</span></h2>

            <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center" x-data>
                <div class="relative">
                    <x-icon name="search" size="16" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS…" class="form-input py-2 pl-9 sm:w-60">
                </div>
                <div class="relative">
                    <select name="kelas_id" class="form-input appearance-none py-2 pr-9 sm:w-44" @change="$el.form.submit()">
                        <option value="">Semua kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->kelas_id }}" @selected(request('kelas_id') == $kelas->kelas_id)>{{ $kelas->kelas_nama }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" size="16" class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-ink-400" />
                </div>
                @if (request()->hasAny(['q', 'kelas_id']) && (request('q') || request('kelas_id')))
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
        </x-slot:header>

        @if ($siswas->isEmpty())
            <x-empty-state icon="users" title="Siswa tidak ditemukan" description="Coba ubah kata kunci atau filter kelas, atau tambahkan siswa baru.">
                <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary"><x-icon name="plus" size="16" /> Tambah Siswa</a>
            </x-empty-state>
        @else
            <table class="table">
                <thead>
                    <tr><th>Nama</th><th>NIS</th><th>Kelas</th><th>Email</th><th>No. HP</th><th class="w-32 text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($siswas as $siswa)
                        <tr>
                            <td>
                                <a href="{{ route('admin.siswa.show', $siswa) }}" class="flex items-center gap-3">
                                    <x-avatar :initials="$siswa->inisial" :seed="$siswa->siswa_nama" />
                                    <span class="font-medium text-ink-900 hover:underline">{{ $siswa->siswa_nama }}</span>
                                </a>
                            </td>
                            <td class="tabular-nums">{{ $siswa->siswa_nis }}</td>
                            <td><x-badge color="gray" :dot="false">{{ $siswa->kelas->kelas_nama }}</x-badge></td>
                            <td class="text-ink-500">{{ $siswa->siswa_email ?? '—' }}</td>
                            <td class="text-ink-500 tabular-nums">{{ $siswa->siswa_hp ?? '—' }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-icon size-8" title="Detail"><x-icon name="eye" size="16" /></a>
                                    <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn-icon size-8" title="Ubah"><x-icon name="pencil" size="16" /></a>
                                    <x-delete-button :action="route('admin.siswa.destroy', $siswa)" title="Hapus siswa?"
                                                     :message="'Data '.$siswa->siswa_nama.' beserta seluruh rekap dan izinnya akan dihapus permanen.'" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-table-card>
</x-layouts.admin>
