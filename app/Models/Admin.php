<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'admin_id';

    /** Kolom password custom, dipakai oleh guard & rehash otomatis Laravel. */
    protected $authPasswordName = 'admin_password';

    protected $fillable = [
        'admin_username',
        'admin_password',
        'admin_nama',
    ];

    protected $hidden = [
        'admin_password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'admin_password' => 'hashed',
        ];
    }
}
