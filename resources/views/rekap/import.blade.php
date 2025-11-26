@extends('layouts.app')

@section('title', 'Import Rekap')
@section('page-title', 'Import Rekap')

@section('content')
<style>
    .import-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        padding: 24px 32px;
        border: none;
    }

    .card-header h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .card-body {
        padding: 32px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }

    .btn {
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }

    .btn-secondary {
        background: white;
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-secondary:hover {
        background: #6c757d;
        color: white;
    }

    .info-box {
        background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
        border-left: 4px solid #2196F3;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .info-box-title {
        font-weight: 700;
        color: #1565C0;
        margin-bottom: 12px;
        font-size: 15px;
    }

    .info-box ul {
        margin: 0;
        padding-left: 20px;
        color: #1565C0;
    }

    .info-box li {
        margin-bottom: 6px;
        font-size: 14px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .alert-error {
        background: #FFEBEE;
        color: #C62828;
        border-left: 4px solid #EF5350;
    }
</style>

<div class="import-container">
    @if(session('error'))
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>📥 Import Data Rekap</h3>
        </div>

        <div class="card-body">
            <div style="margin-bottom: 20px; padding: 15px; background: #E8F5E9; border-left: 4px solid #28a745; border-radius: 4px;">
                <a href="{{ route('rekap.template') }}" class="btn btn-success" style="padding: 8px 16px; font-size: 14px;">
                    📥 Contoh Template CSV
                </a>
            </div>

            <form action="{{ route('rekap.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="dari_tanggal" class="form-label">
                            Dari Tanggal <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="date" 
                               class="form-control @error('dari_tanggal') is-invalid @enderror" 
                               id="dari_tanggal" 
                               name="dari_tanggal" 
                               value="{{ old('dari_tanggal', date('Y-m-d')) }}"
                               required>
                        @error('dari_tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="sampai_tanggal" class="form-label">
                            Sampai Tanggal <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="date" 
                               class="form-control @error('sampai_tanggal') is-invalid @enderror" 
                               id="sampai_tanggal" 
                               name="sampai_tanggal" 
                               value="{{ old('sampai_tanggal', date('Y-m-d')) }}"
                               required>
                        @error('sampai_tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <small style="display: block; margin-bottom: 24px; color: #666; font-size: 13px;">
                    Range tanggal untuk data rekap yang akan diimport
                </small>
                
                <div class="form-group">
                    <label for="file" class="form-label">
                        File Excel/CSV <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="file" 
                           class="form-control @error('file') is-invalid @enderror" 
                           id="file" 
                           name="file" 
                           accept=".xlsx,.xls,.csv"
                           required>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small style="display: block; margin-top: 6px; color: #666; font-size: 13px;">
                        Format: Excel (.xlsx, .xls) atau CSV (.csv). Maksimal 10MB
                    </small>
                </div>

                <div class="info-box">
                    <div class="info-box-title">📋 Format File Import</div>
                    <p style="margin-bottom: 10px; color: #1565C0; font-weight: 600;">
                        ⚠️ Jika file Excel/CSV memiliki kolom NO di awal, kolom tersebut akan otomatis di-skip.
                    </p>
                    <p style="margin-bottom: 10px; color: #1565C0;">
                        <strong>Urutan Kolom (tanpa NO):</strong>
                    </p>
                    <ul>
                        <li>Kolom 1: Nama KC</li>
                        <li>Kolom 2: PN</li>
                        <li>Kolom 3: Nama RMFT</li>
                        <li>Kolom 4: Nama Pemilik</li>
                        <li>Kolom 5: No Rekening</li>
                        <li>Kolom 6: Pipeline (angka)</li>
                        <li>Kolom 7: Realisasi (angka)</li>
                        <li>Kolom 8: Keterangan</li>
                        <li>Kolom 9: Validasi</li>
                    </ul>
                    <p style="margin-top: 10px; color: #1565C0; font-size: 13px;">
                        <strong>Atau dengan kolom NO:</strong> NO, Nama KC, PN, Nama RMFT, Nama Pemilik, No Rekening, Pipeline, Realisasi, Keterangan, Validasi
                    </p>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">
                        📥 Import Data
                    </button>
                    <a href="{{ route('rekap.index') }}" class="btn btn-secondary">
                        ← Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
