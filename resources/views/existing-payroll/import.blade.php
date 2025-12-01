@extends('layouts.app')

@section('title', 'Import Existing Payroll')
@section('page-title', 'Import Data Existing Payroll')

@section('content')
<style>
    .import-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 2px dashed #667eea;
        border-radius: 6px;
        background: #f8f9ff;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
    }

    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
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

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box h4 {
        margin-top: 0;
        color: #2196F3;
    }

    .info-box ul {
        margin: 10px 0;
        padding-left: 20px;
    }
</style>

<div class="import-container">
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="info-box">
        <h4>📋 Informasi Format CSV</h4>
        <p>File CSV harus menggunakan <strong>semicolon (;)</strong> sebagai pemisah dan memiliki <strong>6 kolom</strong>:</p>
        <ul>
            <li><strong>Kode Cabang Induk</strong> - Kode identifikasi cabang induk</li>
            <li><strong>Cabang Induk</strong> - Nama cabang induk</li>
            <li><strong>Corporate Code</strong> - Kode perusahaan</li>
            <li><strong>Nama Perusahaan</strong> - Nama perusahaan payroll</li>
            <li><strong>Jumlah Rekening</strong> - Total jumlah rekening</li>
            <li><strong>Saldo Rekening</strong> - Total saldo rekening</li>
        </ul>
        <p><strong>Contoh format:</strong></p>
        <code style="background: white; padding: 10px; display: block; border-radius: 4px; overflow-x: auto;">
            Kode Cabang Induk;Cabang Induk;Corporate Code;Nama Perusahaan;Jumlah rekening;Saldo Rekening<br>
            354;KC Bandung A.H. Nasution;32;TNI AD - Pusat Pendidikan Armed;3.094;4.782.464.190
        </code>
    </div>

    <form action="{{ route('existing-payroll.import') }}" method="POST" enctype="multipart/form-data">
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
            <label for="csv_file">📁 Pilih File CSV <span style="color:red;">*</span></label>
            <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required>
            @error('csv_file')
                <span style="color: red; font-size: 13px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-success">
                ⬆️ Upload & Import
            </button>
            <a href="{{ route('existing-payroll.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>
    </form>
</div>

@endsection
