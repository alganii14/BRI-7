<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonDebiturVolBesar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kanca',
        'kanca',
        'kode_uker',
        'uker',
        'cifno',
        'norek_pinjaman',
        'norek_simpanan',
        'balance',
        'volume',
        'nama_nasabah',
        'keterangan',
    ];
}
