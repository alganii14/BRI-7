@extends('layouts.app')

@section('title', 'Detail Data')
@section('page-title', 'Detail Data Non Debitur Memiliki Qlola Namun User Tdk Aktif')

@section('content')
<style>
    .detail-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto;
    }

    .detail-group {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-group:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #666;
        font-size: 13px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 16px;
        color: #333;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        font-weight: 600;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #333;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
</style>

<div class="detail-container">
    <div class="detail-group">
        <div class="detail-label">Kode Kanca</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->kode_kanca ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Kanca</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->kanca ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Kode Uker</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->kode_uker ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Uker</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->uker ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">CIFNO</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->cifno ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Norek Simpanan</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->norek_simpanan ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Norek Pinjaman</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->norek_pinjaman ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Balance</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->balance ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Nama Nasabah</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->nama_nasabah ?? '-' }}</div>
    </div>

    <div class="detail-group">
        <div class="detail-label">Keterangan</div>
        <div class="detail-value">{{ $qlolaUserTidakAktif->keterangan ?? '-' }}</div>
    </div>

    <div class="actions">
        <a href="{{ route('qlola-user-tidak-aktif.edit', $qlolaUserTidakAktif->id) }}" class="btn btn-warning">✏️ Edit</a>
        <a href="{{ route('qlola-user-tidak-aktif.index') }}" class="btn btn-secondary">↩️ Kembali</a>
    </div>
</div>
@endsection
