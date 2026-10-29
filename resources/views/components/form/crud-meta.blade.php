@props(['form'])

{{--
    Field tersembunyi untuk form modal crudForm (lihat resources/js/components/crud-form.js):
    _form    : id root x-data, supaya modal yang benar dibuka kembali saat validasi gagal
    _editing : id data yang diubah (kosong = tambah)
    _method  : PUT saat mengubah
--}}
@csrf
<input type="hidden" name="_form" value="{{ $form }}">
<input type="hidden" name="_editing" :value="editingId ?? ''">
<template x-if="isEditing"><input type="hidden" name="_method" value="PUT"></template>
