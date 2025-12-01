<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QlolaUserTidakAktif extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kanca',
        'kanca',
        'kode_uker',
        'uker',
        'cifno',
        'norek_simpanan',
        'norek_pinjaman',
        'balance',
        'nama_nasabah',
        'keterangan',
        'tanggal_posisi_data',
    ];
}
