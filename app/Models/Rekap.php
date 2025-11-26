<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekap extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kc',
        'tanggal',
        'pn',
        'nama_rmft',
        'nama_pemilik',
        'no_rekening',
        'pipeline',
        'realisasi',
        'keterangan',
        'validasi',
    ];

    protected $casts = [
        'pipeline' => 'decimal:2',
        'realisasi' => 'decimal:2',
    ];
}
