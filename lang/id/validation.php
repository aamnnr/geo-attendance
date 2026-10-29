<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pesan validasi Bahasa Indonesia
    |--------------------------------------------------------------------------
    */

    'accepted' => ':Attribute harus disetujui.',
    'after' => ':Attribute harus setelah :date.',
    'after_or_equal' => ':Attribute harus sama dengan atau setelah :date.',
    'alpha_num' => ':Attribute hanya boleh berisi huruf dan angka.',
    'array' => ':Attribute harus berupa array.',
    'before' => ':Attribute harus sebelum :date.',
    'before_or_equal' => ':Attribute tidak boleh melebihi :date.',
    'between' => [
        'numeric' => ':Attribute harus di antara :min dan :max.',
        'string' => ':Attribute harus terdiri dari :min sampai :max karakter.',
        'file' => 'Ukuran :attribute harus di antara :min dan :max KB.',
    ],
    'boolean' => ':Attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password saat ini salah.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'date_format' => 'Format :attribute harus :format.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'enum' => ':Attribute yang dipilih tidak valid.',
    'exists' => ':Attribute yang dipilih tidak ditemukan.',
    'file' => ':Attribute harus berupa file.',
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
        'file' => 'Ukuran :attribute tidak boleh lebih dari :max KB.',
    ],
    'mimes' => ':Attribute harus berupa file bertipe: :values.',
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
        'file' => 'Ukuran :attribute minimal :min KB.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'password' => [
        'letters' => ':Attribute harus mengandung minimal satu huruf.',
        'mixed' => ':Attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers' => ':Attribute harus mengandung minimal satu angka.',
        'symbols' => ':Attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':Attribute ini pernah bocor. Gunakan :attribute lain.',
    ],
    'required' => ':Attribute wajib diisi.',
    'required_if' => ':Attribute wajib diisi.',
    'required_with' => ':Attribute wajib diisi bila :values diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',
    'uploaded' => ':Attribute gagal diunggah.',

    'attributes' => [
        'username' => 'username',
        'password' => 'password',
        'nis' => 'NIS',
        'tanggal' => 'tanggal',
        'status' => 'status',
        'jenis' => 'jenis izin',
        'keterangan' => 'keterangan',
        'hari' => 'hari',
        'guru' => 'guru',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'catatan_admin' => 'catatan',
    ],

];
