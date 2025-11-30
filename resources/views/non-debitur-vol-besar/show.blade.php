@extends('layouts.app')

@section('title', 'Detail Non Debitur Vol Besar')
@section('page-title', 'Detail Data Non Debitur Vol Besar CASA Kecil')

@section('content')
<style>
    .detail-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 1000px;
        margin: 0 auto;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .detail-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
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
        margin-right: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
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
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #0066CC;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #0066CC;
    }
</style>

<div class="detail-container">
    <div class="section-title">Informasi Umum</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Kode Kanca</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->kode_kanca ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Kanca</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->kanca ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Kode Uker</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->kode_uker ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Uker</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->uker ?? '-' }}</div>
        </div>
    </div>

    <div class="section-title">Informasi Nasabah</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">CIFNO</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->cifno ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Norek Pinjaman</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->norek_pinjaman ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Norek Simpanan</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->norek_simpanan ?? '-' }}</div>
        </div>

        <div class="detail-item full-width">
            <div class="detail-label">Nama Nasabah</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->nama_nasabah ?? '-' }}</div>
        </div>
    </div>

    <div class="section-title">Informasi Volume & Balance</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Balance</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->balance ?? '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Volume</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->volume ?? '-' }}</div>
        </div>

        <div class="detail-item full-width">
            <div class="detail-label">Keterangan</div>
            <div class="detail-value">{{ $nonDebiturVolBesar->keterangan ?? '-' }}</div>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('non-debitur-vol-besar.edit', $nonDebiturVolBesar->id) }}" class="btn btn-warning">✏️ Edit</a>
        <a href="{{ route('non-debitur-vol-besar.index') }}" class="btn btn-secondary">↩️ Kembali</a>
    </div>
</div>
@endsection
