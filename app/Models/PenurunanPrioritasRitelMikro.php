<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenurunanPrioritasRitelMikro extends Model
{
    use HasFactory;

    protected $table = 'penurunan_prioritas_ritel_mikro';

    protected $fillable = [
        'kode_cabang_induk',
        'cabang_induk',
        'kode_uker',
        'unit_kerja',
        'cifno',
        'no_rekening',
        'nama_nasabah',
        'segmentasi_bpr',
        'jenis_simpanan',
        'saldo_last_eom',
        'saldo_terupdate',
        'delta',
        'tanggal_posisi_data',
    ];

    protected $casts = [
        'saldo_last_eom' => 'decimal:2',
        'saldo_terupdate' => 'decimal:2',
        'delta' => 'decimal:2',
    ];
}
