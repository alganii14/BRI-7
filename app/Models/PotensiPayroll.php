<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotensiPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang_induk',
        'cabang_induk',
        'perusahaan',
        'estimasi_pekerja',
    ];
}
