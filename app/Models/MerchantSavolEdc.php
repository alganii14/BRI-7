<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantSavolEdc extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kanca',
        'nama_kanca',
        'kode_uker',
        'nama_uker',
        'nama_merchant',
        'norek',
        'cifno',
        'jumlah_tid',
        'jumlah_trx',
        'sales_volume',
        'saldo_posisi',
        'tanggal_posisi_data',
    ];
}
