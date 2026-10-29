@php $isEdit = $siswa->exists; @endphp

<x-layouts.admin :title="$isEdit ? 'Ubah Siswa' : 'Tambah Siswa'"
                 :subtitle="$isEdit ? 'Perbarui data '.$siswa->siswa_nama.'.' : 'Buat akun siswa baru untuk presensi.'">
    <x-slot:actions>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-ghost"><x-icon name="arrow-left" size="16" /> Kembali</a>
    </x-slot:actions>

    <form method="POST" action="{{ $isEdit ? route('admin.siswa.update', $siswa) : route('admin.siswa.store') }}" class="grid gap-6 lg:grid-cols-3">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="space-y-6 lg:col-span-2">
            <section class="card p-6">
                <h2 class="mb-1 font-semibold text-ink-900">Data akademik</h2>
                <p class="mb-5 text-sm text-ink-500">NIS dipakai siswa untuk login.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form.input name="siswa_nama" label="Nama lengkap" :value="$siswa->siswa_nama" class="sm:col-span-2" required />
                    <x-form.input name="siswa_nis" label="NIS" :value="$siswa->siswa_nis" inputmode="numeric" required />
                    <x-form.select name="kelas_id" label="Kelas" :value="$siswa->kelas_id" placeholder="Pilih kelas…"
                                   :options="$kelasList->pluck('kelas_nama', 'kelas_id')" required />
                </div>
            </section>

            <section class="card p-6">
                <h2 class="mb-1 font-semibold text-ink-900">Kontak</h2>
                <p class="mb-5 text-sm text-ink-500">Opsional, dapat dilengkapi siswa sendiri dari halaman profil.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form.input type="email" name="siswa_email" label="Email" icon="mail" :value="$siswa->siswa_email" />
                    <x-form.input name="siswa_hp" label="No. HP" icon="phone" :value="$siswa->siswa_hp" inputmode="tel" />
                    <x-form.textarea name="siswa_alamat" label="Alamat" :value="$siswa->siswa_alamat" class="sm:col-span-2" />
                </div>
            </section>
        </div>

        <div class="space-y-6">
            <section class="card p-6">
                <h2 class="mb-1 font-semibold text-ink-900">Password</h2>
                <p class="mb-5 text-sm text-ink-500">{{ $isEdit ? 'Kosongkan jika tidak ingin mengganti password.' : 'Minimal 6 karakter.' }}</p>
                @if ($isEdit)
                    <x-form.input type="password" name="siswa_password" label="Password baru" icon="lock" autocomplete="new-password" />
                @else
                    <x-form.input type="password" name="siswa_password" label="Password" icon="lock" autocomplete="new-password" required />
                @endif
            </section>

            <div class="flex gap-2">
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary flex-1">Batal</a>
                <button type="submit" class="btn btn-primary flex-1">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Siswa' }}</button>
            </div>
        </div>
    </form>
</x-layouts.admin>
