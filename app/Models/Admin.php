<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable 
{
    use HasFactory, HasApiTokens;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';
    
    protected $fillable = [
        'admin_username',
        'admin_password', 
        'admin_nama',
    ];

    protected $hidden = [
        'admin_password',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->admin_password;
    }
}

