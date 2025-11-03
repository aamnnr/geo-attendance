<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libur extends Model
{
    use HasFactory;

    protected $primaryKey = 'libur_id';
    protected $fillable = ['keterangan', 'libur_dari', 'libur_sampai'];
}
