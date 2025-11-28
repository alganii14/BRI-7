<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;

    protected $table = 'aktivitas';

    protected $fillable = [
        'rmft_id',
        'assigned_by',
        'tipe',
        'jenis_usaha',
        'jenis_simpanan',
        'tingkat_keyakinan',
        'nasabah_id',
        'tanggal',
        'nama_rmft',
        'pn',
        'kode_kc',
        'nama_kc',
        'kode_uker',
        'nama_uker',
        'kelompok',
        'strategy_pipeline',
        'kategori_strategi',
        'rencana_aktivitas',
        'rencana_aktivitas_id',
        'segmen_nasabah',
        'nama_nasabah',
        'norek',
        'rp_jumlah',
        'keterangan',
        'status_realisasi',
        'nominal_realisasi',
        'keterangan_realisasi',
        'tanggal_feedback',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function rmft()
    {
        return $this->belongsTo(RMFT::class);
    }

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function rencanaAktivitasRelasi()
    {
        return $this->belongsTo(RencanaAktivitas::class, 'rencana_aktivitas_id');
    }
}
