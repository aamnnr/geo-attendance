@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center justify-between gap-3">
        <p class="hidden text-sm text-ink-500 sm:block">
            Menampilkan <span class="font-medium text-ink-800">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-ink-800">{{ $paginator->lastItem() }}</span>
            dari <span class="font-medium text-ink-800">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-1 items-center justify-between gap-1 sm:flex-none sm:justify-end">
            @if ($paginator->onFirstPage())
                <span class="btn-icon size-8 opacity-40"><x-icon name="chevron-left" size="16" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-icon size-8" aria-label="Sebelumnya"><x-icon name="chevron-left" size="16" /></a>
            @endif

            <div class="flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-1.5 text-sm text-ink-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="flex size-8 items-center justify-center rounded-full bg-ink-900 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="flex size-8 items-center justify-center rounded-full text-sm text-ink-600 transition hover:bg-ink-100">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-icon size-8" aria-label="Berikutnya"><x-icon name="chevron-right" size="16" /></a>
            @else
                <span class="btn-icon size-8 opacity-40"><x-icon name="chevron-right" size="16" /></span>
            @endif
        </div>
    </nav>
@endif
