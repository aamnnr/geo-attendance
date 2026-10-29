{{--
    Dialog konfirmasi global. Aktifkan pada form apa pun dengan atribut:
      <form method="POST" data-confirm="Hapus siswa ini?" data-confirm-title="Hapus siswa" data-confirm-action="Hapus">
--}}
<div x-data x-cloak x-show="$store.confirm.open" class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center"
     @keydown.escape.window="$store.confirm.open = false" role="alertdialog" aria-modal="true">
    <div x-show="$store.confirm.open" x-transition.opacity class="absolute inset-0 bg-ink-900/40 backdrop-blur-[2px]" @click="$store.confirm.open = false"></div>
    <div x-show="$store.confirm.open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95" x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-pop">
        <span class="mb-4 flex size-11 items-center justify-center rounded-full bg-red-50 text-red-600"><x-icon name="alert" size="22" /></span>
        <h2 class="text-lg font-semibold text-ink-900" x-text="$store.confirm.title"></h2>
        <p class="mt-1.5 text-sm text-ink-500" x-text="$store.confirm.message"></p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="btn btn-secondary" @click="$store.confirm.open = false">Batal</button>
            <button type="button" class="btn btn-danger" @click="$store.confirm.accept()" x-text="$store.confirm.action"></button>
        </div>
    </div>
</div>
