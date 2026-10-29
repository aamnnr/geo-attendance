@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'icon' => null, 'inputClass' => ''])

{{-- <x-form.input name="siswa_nama" label="Nama" :value="$siswa->siswa_nama" required />
     Atribut class = wrapper; input-class = elemen <input>. --}}
@php
    $id = $attributes->get('id', $name);
    $invalid = $errors->has($name);
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }} @if ($attributes->has('required'))<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <div class="relative">
        @if ($icon)
            <x-icon :name="$icon" size="17" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-ink-400" />
        @endif
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
               @if ($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif
               @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
               {{ $attributes->except(['class', 'id'])->merge(['class' => trim('form-input '.($icon ? 'pl-10 ' : '').$inputClass)]) }}>
    </div>
    @error($name)
        <p id="{{ $id }}-error" class="form-error">{{ $message }}</p>
    @elseif ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @enderror
</div>
