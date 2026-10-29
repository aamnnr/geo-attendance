@props(['user', 'logoutRoute', 'profileRoute' => null, 'placement' => 'right'])

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
    <button type="button" @click="open = !open" class="rounded-full focus-visible:ring-4 focus-visible:ring-brand-500/30 focus-visible:outline-none" aria-label="Menu akun" :aria-expanded="open">
        <x-avatar :initials="$user['inisial']" :seed="$user['nama']" size="md" />
    </button>

    <div x-cloak x-show="open" x-transition.origin.bottom.left
         @class([
             'absolute z-50 w-60 rounded-xl border border-ink-200 bg-white p-1.5 shadow-pop',
             'bottom-0 left-full ml-3' => $placement === 'right',
             'top-full right-0 mt-2' => $placement === 'bottom',
         ])>
        <div class="border-b border-ink-100 px-3 pt-2 pb-3">
            <p class="truncate text-sm font-semibold text-ink-900">{{ $user['nama'] }}</p>
            <p class="truncate text-xs text-ink-500">{{ $user['peran'] }}</p>
        </div>
        @if ($profileRoute)
            <a href="{{ route($profileRoute) }}" class="mt-1 flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-700 hover:bg-ink-100">
                <x-icon name="user" size="16" /> Profil saya
            </a>
        @endif
        <form method="POST" action="{{ route($logoutRoute) }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                <x-icon name="log-out" size="16" /> Keluar
            </button>
        </form>
    </div>
</div>
