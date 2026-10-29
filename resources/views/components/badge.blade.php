@props(['status' => null, 'color' => null, 'dot' => true])

{{--
    Badge status. Bisa menerima enum (StatusPresensi / StatusIzin) langsung:
      <x-badge :status="$rekap->status" />
    atau warna manual:
      <x-badge color="blue">Teks</x-badge>
--}}
@php
    $color ??= $status?->color() ?? 'gray';
    $styles = [
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'blue' => 'bg-sky-50 text-sky-700 ring-sky-600/15',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/15',
        'red' => 'bg-red-50 text-red-700 ring-red-600/15',
        'gray' => 'bg-ink-100 text-ink-600 ring-ink-500/15',
    ];
    $dots = [
        'green' => 'bg-emerald-500', 'amber' => 'bg-amber-500', 'blue' => 'bg-sky-500',
        'violet' => 'bg-violet-500', 'red' => 'bg-red-500', 'gray' => 'bg-ink-400',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset '.$styles[$color]]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full {{ $dots[$color] }}"></span>
    @endif
    {{ $slot->isEmpty() ? $status?->label() : $slot }}
</span>
