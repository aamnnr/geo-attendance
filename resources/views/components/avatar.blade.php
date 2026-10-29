@props(['initials', 'seed' => null, 'size' => 'md'])

{{-- Avatar inisial dengan warna stabil berdasarkan nama (seperti avatar huruf di referensi). --}}
@php
    $palette = ['bg-orange-500', 'bg-emerald-500', 'bg-sky-500', 'bg-violet-500', 'bg-rose-500', 'bg-teal-500', 'bg-indigo-500', 'bg-amber-500'];
    $color = $palette[crc32($seed ?? $initials) % count($palette)];
    $sizes = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-14 text-lg',
        'xl' => 'size-20 text-2xl',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center rounded-full font-semibold text-white {$color} {$sizes[$size]}"]) }}>
    {{ $initials }}
</span>
