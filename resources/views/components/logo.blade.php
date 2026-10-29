@props(['withText' => false])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }} aria-label="{{ config('app.name') }}">
    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-500 text-ink-900 shadow-sm shadow-brand-500/40">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a8 8 0 0 0-8 8c0 5.6 7.05 11.6 7.35 11.85a1 1 0 0 0 1.3 0C12.95 21.6 20 15.6 20 10a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg>
    </span>
    @if ($withText)
        <span class="font-serif text-lg font-semibold tracking-tight text-ink-900">{{ config('app.name') }}</span>
    @endif
</a>
