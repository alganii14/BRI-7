<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerusahaanAnak extends Model
{
    use HasFactory;

    protected $table = 'perusahaan_anaks';

    protected $fillable = [
        'nama_partner_vendor',
        'jenis_usaha',
        'alamat',
        'kode_cabang_induk',
        'cabang_induk_terdekat',
        'kode_uker',
        'nama_uker',
        'nama_pic_partner',
        'posisi_pic_partner',
        'hp_pic_partner',
        'nama_perusahaan_anak',
        'status_pipeline',
        'rekening_terbentuk',
        'cif_terbentuk',
        'tanggal_posisi_data',
    ];
}
