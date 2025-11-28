@extends('layouts.app')

@section('title', 'Detail Pipeline')
@section('page-title', 'Detail Pipeline')

@section('content')
<style>
    .detail-card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .detail-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
    }

    .detail-value {
        color: #333;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-warning {
        background-color: #ff9800;
        color: white;
    }

    .section-header {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: 600;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #333;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
</style>

<div class="detail-card">
    <div class="section-header">
        Detail Pipeline
    </div>

    <div class="detail-row">
        <div class="detail-label">Tanggal:</div>
        <div class="detail-value">{{ $pipeline->tanggal->format('d/m/Y') }}</div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Kode KC:</div>
        <div class="detail-value">{{ $pipeline->kode_kc }}</div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Nama KC:</div>
        <div class="detail-value">{{ $pipeline->nama_kc }}</div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Kode Unit:</div>
        <div class="detail-value">{{ $pipeline->kode_uker }}</div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Nama Unit:</div>
        <div class="detail-value">{{ $pipeline->nama_uker }}</div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Strategi:</div>
        <div class="detail-value">{{ $pipeline->strategy_pipeline }}</div>
    </div>

    @if($pipeline->kategori_strategi)
    <div class="detail-row">
        <div class="detail-label">Kategori:</div>
        <div class="detail-value">{{ $pipeline->kategori_strategi }}</div>
    </div>
    @endif

    <div class="detail-row">
        <div class="detail-label">Tipe:</div>
        <div class="detail-value">
            @if($pipeline->tipe == 'lama')
                <span class="badge badge-info">Di Dalam Pipeline</span>
            @else
                <span class="badge badge-warning">Di Luar Pipeline</span>
            @endif
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Nama Nasabah:</div>
        <div class="detail-value">{{ $pipeline->nama_nasabah }}</div>
    </div>

    @if($pipeline->norek)
    <div class="detail-row">
        <div class="detail-label">Norek / CIF:</div>
        <div class="detail-value">{{ $pipeline->norek }}</div>
    </div>
    @endif

    <div class="detail-row">
        <div class="detail-label">Jumlah:</div>
        <div class="detail-value">{{ $pipeline->rp_jumlah }}</div>
    </div>

    @if($pipeline->keterangan)
    <div class="detail-row">
        <div class="detail-label">Keterangan:</div>
        <div class="detail-value">{{ $pipeline->keterangan }}</div>
    </div>
    @endif

    @if($pipeline->assignedBy)
    <div class="detail-row">
        <div class="detail-label">Dibuat Oleh:</div>
        <div class="detail-value">{{ $pipeline->assignedBy->name }}</div>
    </div>
    @endif

    <div style="margin-top: 24px;">
        <a href="{{ route('pipeline.edit', $pipeline->id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('pipeline.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

@endsection
