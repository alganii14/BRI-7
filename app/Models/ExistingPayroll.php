<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExistingPayroll extends Model
{
    use HasFactory;

    protected $table = 'existing_payroll';

    protected $fillable = [
        'kode_cabang_induk',
        'cabang_induk',
        'corporate_code',
        'nama_perusahaan',
        'jumlah_rekening',
        'saldo_rekening',
        'tanggal_posisi_data',
    ];
}
