<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['tipe', 'pesan'];

    public static function record(string $tipe, string $pesan): self
    {
        return static::create(compact('tipe', 'pesan'));
    }
}
