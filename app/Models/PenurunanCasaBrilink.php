<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenurunanCasaBrilink extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang_induk',
        'cabang_induk',
        'kode_uker',
        'unit_kerja',
        'cifno',
        'no_rekening',
        'nama_nasabah',
        'jenis_simpanan',
        'saldo_last_eom',
        'saldo_terupdate',
        'delta',
        'tanggal_posisi_data',
    ];
}
