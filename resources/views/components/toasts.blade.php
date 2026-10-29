{{-- Toast stack. Flash session 'success' / 'error' dan error validasi umum otomatis ditampilkan. --}}
@php
    $flash = collect([
        ['type' => 'success', 'message' => session('success')],
        ['type' => 'error', 'message' => session('error')],
        ['type' => 'error', 'message' => $errors->first('izin')],
    ])->filter(fn ($t) => filled($t['message']))->values();
@endphp

<div x-data x-init="@js($flash).forEach(t => $store.toast.push(t.message, t.type))"
     class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex flex-col items-center gap-2 px-4 sm:top-6 sm:right-6 sm:left-auto sm:items-end"
     aria-live="polite">
    <template x-for="toast in $store.toast.items" :key="toast.id">
        <div x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border bg-white p-3.5 pr-2.5 shadow-pop"
             :class="toast.type === 'error' ? 'border-red-200' : 'border-emerald-200'">
            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full"
                  :class="toast.type === 'error' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'">
                <template x-if="toast.type === 'error'"><x-icon name="alert" size="14" stroke="2.2" /></template>
                <template x-if="toast.type !== 'error'"><x-icon name="check" size="14" stroke="2.6" /></template>
            </span>
            <p class="flex-1 pt-0.5 text-sm text-ink-800" x-text="toast.message"></p>
            <button type="button" class="btn-icon size-7" @click="$store.toast.dismiss(toast.id)" aria-label="Tutup"><x-icon name="x" size="15" /></button>
        </div>
    </template>
</div>
