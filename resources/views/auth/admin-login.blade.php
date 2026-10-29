<x-auth-shell title="Login Admin" heading="Masuk sebagai Admin" description="Kelola data siswa, jadwal, dan rekap presensi."
              switch-label="Login siswa" :switch-href="route('siswa.login')">
    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5" x-data="{ show: false }">
        @csrf
        <x-form.input name="username" label="Username" icon="user" autocomplete="username" autofocus required />

        <div>
            <label for="password" class="form-label">Password</label>
            <div class="relative">
                <x-icon name="lock" size="17" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-ink-400" />
                <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" class="form-input pr-11 pl-10" autocomplete="current-password" required>
                <button type="button" class="absolute top-1/2 right-2 -translate-y-1/2 rounded-md p-1.5 text-ink-400 hover:text-ink-700" @click="show = !show" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <x-icon name="eye" size="17" />
                </button>
            </div>
        </div>

        <label class="flex items-center gap-2.5 text-sm text-ink-600">
            <input type="checkbox" name="remember" class="size-4 rounded border-ink-300 accent-brand-500"> Ingat saya
        </label>

        <button type="submit" class="btn btn-dark w-full py-3">
            Masuk <x-icon name="arrow-right" size="16" />
        </button>
    </form>
</x-auth-shell>
