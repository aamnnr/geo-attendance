@props(['show' => 'open', 'title' => '', 'titleExpr' => null, 'description' => null, 'maxWidth' => 'lg'])

{{--
    Modal yang dikendalikan variabel Alpine milik parent (default: "open").
      <div x-data="{ open: false }"> <x-modal title="Tambah"> ... </x-modal> </div>
    Judul dinamis (ekspresi Alpine): title-expr="isEditing ? 'Ubah' : 'Tambah'".
--}}
@php
    $widths = ['sm' => 'sm:max-w-sm', 'md' => 'sm:max-w-md', 'lg' => 'sm:max-w-lg', 'xl' => 'sm:max-w-xl', '2xl' => 'sm:max-w-2xl'];
@endphp

<template x-teleport="body">
    <div x-cloak x-show="{{ $show }}" class="fixed inset-0 z-[55] flex items-end justify-center sm:items-center sm:p-4"
         @keydown.escape.window="{{ $show }} = false" role="dialog" aria-modal="true">
        <div x-show="{{ $show }}" x-transition.opacity class="absolute inset-0 bg-ink-900/40 backdrop-blur-[2px]" @click="{{ $show }} = false"></div>

        <div x-show="{{ $show }}"
             x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95" x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95"
             x-trap.noscroll="{{ $show }}"
             {{ $attributes->merge(['class' => "relative flex max-h-[92vh] w-full flex-col rounded-t-2xl bg-white shadow-pop sm:rounded-2xl {$widths[$maxWidth]}"]) }}>
            <div class="flex items-start justify-between gap-4 border-b border-ink-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-ink-900" @if ($titleExpr) x-text="{{ $titleExpr }}" @endif>{{ $title }}</h2>
                    @if ($description)
                        <p class="mt-0.5 text-sm text-ink-500">{{ $description }}</p>
                    @endif
                </div>
                <button type="button" class="btn-icon -mr-2 size-8" @click="{{ $show }} = false" aria-label="Tutup"><x-icon name="x" size="17" /></button>
            </div>
            <div class="overflow-y-auto px-6 py-5">
                {{ $slot }}
            </div>
            @isset($footer)
                <div class="flex justify-end gap-2 border-t border-ink-100 bg-ink-50/60 px-6 py-4 sm:rounded-b-2xl">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</template>
