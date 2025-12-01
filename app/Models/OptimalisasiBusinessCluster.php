<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptimalisasiBusinessCluster extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang_induk',
        'cabang_induk',
        'kode_uker',
        'unit_kerja',
        'tag_zona_unggulan',
        'nomor_rekening',
        'nama_usaha_pusat_bisnis',
        'nama_tenaga_pemasar',
        'tanggal_posisi_data',
    ];
}
