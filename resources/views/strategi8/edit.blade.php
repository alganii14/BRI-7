@extends('layouts.app')

@section('title', 'Edit Strategi 8')
@section('page-title', 'Edit Data Strategi 8 - Wingback Penguatan Produk & Fungsi RM')

@section('content')
<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
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
    <form action="{{ route('strategi8.update', $strategi8->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group">
                <label for="kode_cabang_induk">Kode Cabang Induk</label>
                <input type="text" id="kode_cabang_induk" name="kode_cabang_induk" value="{{ old('kode_cabang_induk', $strategi8->kode_cabang_induk) }}">
                @error('kode_cabang_induk')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cabang_induk">Cabang Induk</label>
                <input type="text" id="cabang_induk" name="cabang_induk" value="{{ old('cabang_induk', $strategi8->cabang_induk) }}">
                @error('cabang_induk')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kode_uker">Kode Uker</label>
                <input type="text" id="kode_uker" name="kode_uker" value="{{ old('kode_uker', $strategi8->kode_uker) }}">
                @error('kode_uker')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="unit_kerja">Unit Kerja</label>
                <input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja', $strategi8->unit_kerja) }}">
                @error('unit_kerja')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cifno">CIFNO</label>
                <input type="text" id="cifno" name="cifno" value="{{ old('cifno', $strategi8->cifno) }}">
                @error('cifno')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="no_rekening">No Rekening</label>
                <input type="text" id="no_rekening" name="no_rekening" value="{{ old('no_rekening', $strategi8->no_rekening) }}">
                @error('no_rekening')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_nasabah">Nama Nasabah</label>
                <input type="text" id="nama_nasabah" name="nama_nasabah" value="{{ old('nama_nasabah', $strategi8->nama_nasabah) }}">
                @error('nama_nasabah')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="segmentasi">Segmentasi</label>
                <input type="text" id="segmentasi" name="segmentasi" value="{{ old('segmentasi', $strategi8->segmentasi) }}">
                @error('segmentasi')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jenis_simpanan">Jenis Simpanan</label>
                <input type="text" id="jenis_simpanan" name="jenis_simpanan" value="{{ old('jenis_simpanan', $strategi8->jenis_simpanan) }}">
                @error('jenis_simpanan')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="saldo_last_eom">Saldo Last EOM</label>
                <input type="text" id="saldo_last_eom" name="saldo_last_eom" value="{{ old('saldo_last_eom', $strategi8->saldo_last_eom) }}">
                @error('saldo_last_eom')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="saldo_terupdate">Saldo Terupdate</label>
                <input type="text" id="saldo_terupdate" name="saldo_terupdate" value="{{ old('saldo_terupdate', $strategi8->saldo_terupdate) }}">
                @error('saldo_terupdate')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="delta">Delta</label>
                <input type="text" id="delta" name="delta" value="{{ old('delta', $strategi8->delta) }}">
                @error('delta')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update</button>
            <a href="{{ route('strategi8.index') }}" class="btn btn-secondary">↩️ Kembali</a>
        </div>
    </form>
</div>
@endsection
