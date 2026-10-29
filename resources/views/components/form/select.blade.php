@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'hint' => null])

{{-- <x-form.select name="kelas_id" label="Kelas" :options="$kelasList->pluck('kelas_nama', 'kelas_id')" :value="$siswa->kelas_id" /> --}}
@php
    $id = $attributes->get('id', $name);
    $selected = (string) old($name, $value instanceof \BackedEnum ? $value->value : $value);
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }} @if ($attributes->has('required'))<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <div class="relative">
        <select id="{{ $id }}" name="{{ $name }}"
                @if ($errors->has($name)) aria-invalid="true" @endif
                {{ $attributes->except(['class', 'id'])->merge(['class' => 'form-input appearance-none pr-10']) }}>
            @if ($placeholder !== null)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected($selected === (string) $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
        <x-icon name="chevron-down" size="16" class="pointer-events-none absolute top-1/2 right-3.5 -translate-y-1/2 text-ink-400" />
    </div>
    @error($name)
        <p class="form-error">{{ $message }}</p>
    @elseif ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @enderror
</div>
