<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebiturBelumMemilikiQlola extends Model
{
    use HasFactory;

    protected $table = 'debitur_belum_memiliki_qlola';

    protected $fillable = [
        'kode_kanca',
        'kanca',
        'kode_uker',
        'uker',
        'cifno',
        'norek_pinjaman',
        'norek_simpanan',
        'balance',
        'nama_debitur',
        'plafon',
        'pn_pengelola_1',
        'keterangan',
        'tanggal_posisi_data',
    ];
}
