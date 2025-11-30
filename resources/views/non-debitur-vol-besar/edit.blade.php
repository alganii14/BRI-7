@extends('layouts.app')

@section('title', 'Edit Non Debitur Vol Besar')
@section('page-title', 'Edit Data Non Debitur Vol Besar')

@section('content')
<style>
    .form-container {
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

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 10px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
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

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
</style>

<div class="form-container">
    <form action="{{ route('non-debitur-vol-besar.update', $nonDebiturVolBesar->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="kode_kanca">Kode Kanca</label>
            <input type="text" id="kode_kanca" name="kode_kanca" value="{{ old('kode_kanca', $nonDebiturVolBesar->kode_kanca) }}">
        </div>

        <div class="form-group">
            <label for="kanca">Kanca</label>
            <input type="text" id="kanca" name="kanca" value="{{ old('kanca', $nonDebiturVolBesar->kanca) }}">
        </div>

        <div class="form-group">
            <label for="kode_uker">Kode Uker</label>
            <input type="text" id="kode_uker" name="kode_uker" value="{{ old('kode_uker', $nonDebiturVolBesar->kode_uker) }}">
        </div>

        <div class="form-group">
            <label for="uker">Uker</label>
            <input type="text" id="uker" name="uker" value="{{ old('uker', $nonDebiturVolBesar->uker) }}">
        </div>

        <div class="form-group">
            <label for="cifno">CIFNO</label>
            <input type="text" id="cifno" name="cifno" value="{{ old('cifno', $nonDebiturVolBesar->cifno) }}">
        </div>

        <div class="form-group">
            <label for="norek_pinjaman">Norek Pinjaman</label>
            <input type="text" id="norek_pinjaman" name="norek_pinjaman" value="{{ old('norek_pinjaman', $nonDebiturVolBesar->norek_pinjaman) }}">
        </div>

        <div class="form-group">
            <label for="norek_simpanan">Norek Simpanan</label>
            <input type="text" id="norek_simpanan" name="norek_simpanan" value="{{ old('norek_simpanan', $nonDebiturVolBesar->norek_simpanan) }}">
        </div>

        <div class="form-group">
            <label for="balance">Balance</label>
            <input type="text" id="balance" name="balance" value="{{ old('balance', $nonDebiturVolBesar->balance) }}">
        </div>

        <div class="form-group">
            <label for="volume">Volume</label>
            <input type="text" id="volume" name="volume" value="{{ old('volume', $nonDebiturVolBesar->volume) }}">
        </div>

        <div class="form-group">
            <label for="nama_nasabah">Nama Nasabah</label>
            <input type="text" id="nama_nasabah" name="nama_nasabah" value="{{ old('nama_nasabah', $nonDebiturVolBesar->nama_nasabah) }}">
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $nonDebiturVolBesar->keterangan) }}">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update</button>
            <a href="{{ route('non-debitur-vol-besar.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </form>
</div>
@endsection
