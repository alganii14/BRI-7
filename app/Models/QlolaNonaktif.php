<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QlolaNonaktif extends Model
{
    use HasFactory;

    protected $table = 'qlola_nonaktifs';

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
