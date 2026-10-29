@props(['paginator' => null])

{{--
    Kartu tabel seperti referensi: slot "header" (judul + tab + aksi),
    tabel bisa di-scroll horizontal di layar kecil, dan pagination di bawah.
--}}
<div {{ $attributes->merge(['class' => 'card overflow-hidden']) }}>
    @isset($header)
        <div class="flex flex-col gap-4 border-b border-ink-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            {{ $header }}
        </div>
    @endisset

    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    @if ($paginator?->hasPages())
        <div class="border-t border-ink-100 px-5 py-3">
            {{ $paginator->onEachSide(1)->links() }}
        </div>
    @endif
</div>
