<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'pn_rmft',
        'nama_rmft',
        'kode_kc',
        'nama_kc',
        'kode_uker',
        'nama_uker',
        'strategy_pipeline',
        'kategori_strategi',
        'tipe',
        'jenis_simpanan',
        'jenis_usaha',
        'nominal',
        'tingkat_keyakinan',
        'nama_nasabah',
        'norek',
        'keterangan',
        'nasabah_id',
        'assigned_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }


}
