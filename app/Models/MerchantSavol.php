<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantSavol extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kanca',
        'kanca',
        'kode_uker',
        'uker',
        'jenis_merchant',
        'tid_store_id',
        'nama_merchant',
        'alamat_merchant',
        'norekening',
        'cif',
        'savol_bulan_lalu',
        'casa_akhir_bulan',
        'tanggal_posisi_data',
    ];
}
