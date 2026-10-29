@props(['action', 'message' => 'Data yang dihapus tidak dapat dikembalikan.', 'title' => 'Hapus data ini?', 'label' => 'Hapus'])

{{-- Tombol hapus ber-ikon dengan dialog konfirmasi global. --}}
<form method="POST" action="{{ $action }}" class="inline" data-confirm="{{ $message }}" data-confirm-title="{{ $title }}" data-confirm-action="{{ $label }}">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'btn-icon size-8 hover:bg-red-50 hover:text-red-600']) }} title="{{ $label }}" aria-label="{{ $label }}">
        <x-icon name="trash" size="16" />
    </button>
</form>
