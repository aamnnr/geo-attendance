@props(['name', 'label' => null, 'value' => null, 'hint' => null, 'rows' => 3])

@php $id = $attributes->get('id', $name); @endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }} @if ($attributes->has('required'))<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
              @if ($errors->has($name)) aria-invalid="true" @endif
              {{ $attributes->except(['class', 'id'])->merge(['class' => 'form-input resize-y']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="form-error">{{ $message }}</p>
    @elseif ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @enderror
</div>
