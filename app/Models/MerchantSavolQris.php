<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantSavolQris extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kanca',
        'nama_kanca',
        'kode_uker',
        'nama_uker',
        'storeid',
        'nama_merchant',
        'alamat',
        'no_rek',
        'cif',
        'akumulasi_sv_total',
        'posisi_sv_total',
        'saldo_posisi',
        'tanggal_posisi_data',
    ];
}
