@props(['label', 'value', 'icon' => null, 'tone' => 'ink', 'hint' => null, 'href' => null])

@php
    $tones = [
        'ink' => 'bg-ink-100 text-ink-700',
        'brand' => 'bg-brand-100 text-brand-800',
        'green' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'blue' => 'bg-sky-100 text-sky-700',
        'red' => 'bg-red-100 text-red-700',
        'violet' => 'bg-violet-100 text-violet-700',
    ];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'card group flex items-start gap-4 p-5 transition'.($href ? ' hover:-translate-y-0.5 hover:shadow-pop' : '')]) }}>
    @if ($icon)
        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $tones[$tone] }}">
            <x-icon :name="$icon" size="20" />
        </span>
    @endif
    <div class="min-w-0">
        <p class="text-sm text-ink-500">{{ $label }}</p>
        <p class="mt-0.5 text-2xl font-semibold tracking-tight text-ink-900 tabular-nums">{{ $value }}</p>
        @if ($hint)
            <p class="mt-0.5 text-xs text-ink-400">{{ $hint }}</p>
        @endif
    </div>
</{{ $tag }}>
