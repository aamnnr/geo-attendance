<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Menunjukkan bahwa primary key BUKAN auto-incrementing integer.
     */
    public $incrementing = false;

    /**
     * Tipe data dari primary key.
     */
    protected $keyType = 'string';

    /**
     * Nama dari primary key.
     */
    protected $primaryKey = 'key';

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'key',
        'value',
    ];
}

