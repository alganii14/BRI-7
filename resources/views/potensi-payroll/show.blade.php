@extends('layouts.app')

@section('title', 'Detail Potensi Payroll')
@section('page-title', 'Detail Data Potensi Payroll')

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

    .detail-grid {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 15px;
        margin-bottom: 20px;
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
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        margin-right: 10px;
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
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }
</style>

<div class="detail-container">
    <div class="detail-grid">
        <div class="detail-label">Kode Cabang Induk:</div>
        <div class="detail-value">{{ $potensiPayroll->kode_cabang_induk ?? '-' }}</div>

        <div class="detail-label">Cabang Induk:</div>
        <div class="detail-value">{{ $potensiPayroll->cabang_induk ?? '-' }}</div>

        <div class="detail-label">Perusahaan:</div>
        <div class="detail-value">{{ $potensiPayroll->perusahaan ?? '-' }}</div>

        <div class="detail-label">Estimasi Pekerja:</div>
        <div class="detail-value">{{ $potensiPayroll->estimasi_pekerja ?? '-' }}</div>

        <div class="detail-label">Dibuat:</div>
        <div class="detail-value">{{ $potensiPayroll->created_at->format('d M Y H:i') }}</div>

        <div class="detail-label">Diupdate:</div>
        <div class="detail-value">{{ $potensiPayroll->updated_at->format('d M Y H:i') }}</div>
    </div>

    <div class="actions">
        <a href="{{ route('potensi-payroll.edit', $potensiPayroll->id) }}" class="btn btn-warning">✏️ Edit</a>
        <a href="{{ route('potensi-payroll.index') }}" class="btn btn-secondary">↩️ Kembali</a>
    </div>
</div>
@endsection
