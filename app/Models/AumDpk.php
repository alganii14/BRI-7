<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AumDpk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang_induk',
        'cabang_induk',
        'kode_uker',
        'unit_kerja',
        'slp',
        'pbo',
        'cif',
        'id_prioritas',
        'nama_nasabah',
        'nomor_rekening',
        'aum',
        'tanggal_posisi_data',
    ];
}
