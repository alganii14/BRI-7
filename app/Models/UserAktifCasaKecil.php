<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAktifCasaKecil extends Model
{
    use HasFactory;

    protected $table = 'user_aktif_casa_kecils';

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
        'nama_debitur',
        'plafon',
        'pn_pengelola_1',
        'keterangan',
    ];
}
