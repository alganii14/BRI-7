@extends('layouts.app')

@section('title', 'Tambah Potensi Payroll')
@section('page-title', 'Tambah Data Potensi Payroll')

@section('content')
<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto;
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

    input, select, textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #667eea;
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
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
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

    .error {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }
</style>

<div class="form-container">
    <form action="{{ route('potensi-payroll.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="kode_cabang_induk">Kode Cabang Induk</label>
            <input type="text" id="kode_cabang_induk" name="kode_cabang_induk" value="{{ old('kode_cabang_induk') }}">
            @error('kode_cabang_induk')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="cabang_induk">Cabang Induk</label>
            <input type="text" id="cabang_induk" name="cabang_induk" value="{{ old('cabang_induk') }}">
            @error('cabang_induk')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="perusahaan">Perusahaan</label>
            <input type="text" id="perusahaan" name="perusahaan" value="{{ old('perusahaan') }}">
            @error('perusahaan')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="jenis_pipeline">Jenis Pipeline</label>
            <input type="text" id="jenis_pipeline" name="jenis_pipeline" value="{{ old('jenis_pipeline') }}">
            @error('jenis_pipeline')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="estimasi_pekerja">Estimasi Pekerja</label>
            <input type="text" id="estimasi_pekerja" name="estimasi_pekerja" value="{{ old('estimasi_pekerja') }}">
            @error('estimasi_pekerja')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('potensi-payroll.index') }}" class="btn btn-secondary">↩️ Kembali</a>
        </div>
    </form>
</div>
@endsection
