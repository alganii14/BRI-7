<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brilink extends Model
{
    use HasFactory;

    protected $fillable = [
        'kd_cabang',
        'cabang',
        'kd_uker',
        'uker',
        'nama_agen',
        'id_agen',
        'kelas',
        'no_telpon',
        'bidang_usaha',
        'norek',
        'casa',
        'tanggal_posisi_data',
        'tanggal_upload_data',
    ];
}
