@extends('layouts.app')

@section('title', 'Import Non Debitur Vol Besar')
@section('page-title', 'Import Data Non Debitur Vol Besar')

@section('content')
<style>
    .import-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box h4 {
        margin: 0 0 10px 0;
        color: #1976D2;
    }

    .info-box ul {
        margin: 10px 0 0 20px;
        color: #555;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 2px dashed #ddd;
        border-radius: 6px;
        cursor: pointer;
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
    }

    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
</style>

<div class="import-container">
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="info-box">
        <h4>📋 Format CSV</h4>
        <p>File CSV harus memiliki format berikut (dengan delimiter semicolon <code>;</code>):</p>
        <ul>
            <li>KODE KANCA</li>
            <li>KANCA</li>
            <li>KODE UKER</li>
            <li>UKER</li>
            <li>CIFNO</li>
            <li>NOREK PINJAMAN</li>
            <li>NOREK SIMPANAN</li>
            <li>BALANCE</li>
            <li>VOLUME</li>
            <li>NAMA NASABAH</li>
            <li>KETERANGAN</li>
        </ul>
        <p style="margin-top: 10px;"><strong>Catatan:</strong> Baris pertama (header) akan diabaikan.</p>
    </div>

    <form action="{{ route('non-debitur-vol-besar.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="margin-bottom: 30px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">
                    📅 Tanggal Posisi Data <span style="color: red;">*</span>
                </label>
                <input type="date" name="tanggal_posisi_data" id="tanggal_posisi_data" 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;" 
                       value="{{ old('tanggal_posisi_data', date('Y-m-d')) }}" required>
                <small style="color: #666; font-size: 12px;">Tanggal posisi data yang akan ditampilkan di atas tabel</small>
                @error('tanggal_posisi_data')
                    <p style="color: #dc3545; margin-top: 5px; font-size: 13px;">{{ $message }}</p>
                @enderror
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">
                    📆 Tanggal Upload Data <span style="color: red;">*</span>
                </label>
                <input type="date" name="tanggal_upload_data" id="tanggal_upload_data" 
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;" 
                       value="{{ old('tanggal_upload_data', date('Y-m-d')) }}" required>
                <small style="color: #666; font-size: 12px;">Tanggal upload untuk filter bulan dan tahun (menggantikan created_at)</small>
                @error('tanggal_upload_data')
                    <p style="color: #dc3545; margin-top: 5px; font-size: 13px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="csv_file">Pilih File CSV</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">📁 Import Data</button>
            <a href="{{ route('non-debitur-vol-besar.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </form>
</div>
@endsection
