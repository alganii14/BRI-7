@extends('layouts.app')

@section('title', 'Detail Debitur Belum Memiliki Qlola')

@section('page-title', 'Detail Data Debitur Belum Memiliki Qlola')

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

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        justify-content: flex-end;
    }

    .section-title {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .detail-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #0066CC;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .detail-value {
        color: #212529;
        font-size: 16px;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>

<div class="detail-container">
    <div class="action-buttons">
        <a href="{{ route('debitur-belum-memiliki-qlola.edit', $debiturBelumMemilikiQlola->id) }}" class="btn btn-warning">
            ✏️ Edit
        </a>
        <a href="{{ route('debitur-belum-memiliki-qlola.index') }}" class="btn btn-secondary">
            ↩️ Kembali
        </a>
    </div>

    <div class="section-title">
        📋 Informasi Debitur Belum Memiliki Qlola
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Kode Kanca</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->kode_kanca }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Kanca</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->kanca }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Kode Unit</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->kode_uker }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Unit</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->uker }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">CIFNO</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->cifno }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Nama Debitur</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->nama_debitur }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">No. Rekening Pinjaman</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->norek_pinjaman ?: '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">No. Rekening Simpanan</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->norek_simpanan ?: '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Balance</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->balance ?: '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Plafon</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->plafon ?: '-' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">PN Pengelola</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->pn_pengelola_1 ?: '-' }}</div>
        </div>

        <div class="detail-item full-width">
            <div class="detail-label">Keterangan</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->keterangan ?: '-' }}</div>
        </div>

        <div class="detail-item full-width">
            <div class="detail-label">Dibuat Pada</div>
            <div class="detail-value">{{ $debiturBelumMemilikiQlola->created_at->format('d M Y H:i') }}</div>
        </div>
    </div>
</div>
@endsection
