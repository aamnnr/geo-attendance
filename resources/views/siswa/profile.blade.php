<x-layouts.siswa title="Profil Saya" subtitle="Perbarui kontak dan password akunmu.">
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card relative overflow-hidden p-6 text-center">
            <div class="absolute inset-x-0 top-0 h-24 bg-brand-500"></div>
            <div class="relative">
                <x-avatar :initials="$siswa->inisial" :seed="$siswa->siswa_nama" size="xl" class="mt-6 ring-4 ring-white" />
                <h2 class="mt-4 font-serif text-2xl font-medium text-ink-900">{{ $siswa->siswa_nama }}</h2>
                <p class="text-sm text-ink-500">Siswa · {{ $siswa->kelas->kelas_nama }}</p>
            </div>
            <dl class="relative mt-6 space-y-3 border-t border-ink-100 pt-5 text-left text-sm">
                <div class="flex justify-between gap-4"><dt class="text-ink-500">NIS</dt><dd class="font-medium text-ink-900">{{ $siswa->siswa_nis }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-ink-500">Kelas</dt><dd class="font-medium text-ink-900">{{ $siswa->kelas->kelas_nama }}</dd></div>
                @if ($siswa->kelas->wali_kelas)
                    <div class="flex justify-between gap-4"><dt class="text-ink-500">Wali kelas</dt><dd class="text-right font-medium text-ink-900">{{ $siswa->kelas->wali_kelas }}</dd></div>
                @endif
            </dl>
            <p class="relative mt-5 rounded-lg bg-ink-50 px-3 py-2 text-xs text-ink-500">Nama, NIS, dan kelas hanya dapat diubah oleh admin.</p>
        </section>

        <form method="POST" action="{{ route('siswa.profile.update') }}" class="card lg:col-span-2">
            @csrf
            @method('PUT')

            <div class="border-b border-ink-100 px-6 py-4">
                <h2 class="font-semibold text-ink-900">Informasi kontak</h2>
                <p class="text-sm text-ink-500">Dipakai sekolah untuk menghubungimu.</p>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-form.input type="email" name="siswa_email" label="Email" icon="mail" :value="$siswa->siswa_email" autocomplete="email" />
                <x-form.input type="tel" name="siswa_hp" label="Nomor HP" icon="phone" :value="$siswa->siswa_hp" autocomplete="tel" />
                <x-form.textarea name="siswa_alamat" label="Alamat" :value="$siswa->siswa_alamat" class="sm:col-span-2" />
            </div>

            <div class="border-y border-ink-100 px-6 py-4">
                <h2 class="font-semibold text-ink-900">Ganti password</h2>
                <p class="text-sm text-ink-500">Kosongkan jika tidak ingin mengganti password.</p>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-form.input type="password" name="current_password" label="Password saat ini" icon="lock" autocomplete="current-password" class="sm:col-span-2" />
                <x-form.input type="password" name="password" label="Password baru" icon="lock" autocomplete="new-password" hint="Minimal 6 karakter." />
                <x-form.input type="password" name="password_confirmation" label="Ulangi password baru" icon="lock" autocomplete="new-password" />
            </div>

            <div class="flex justify-end gap-2 border-t border-ink-100 bg-ink-50/60 px-6 py-4 sm:rounded-b-2xl">
                <button type="submit" class="btn btn-primary"><x-icon name="check" size="16" stroke="2.4" /> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layouts.siswa>
