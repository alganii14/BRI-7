@extends('layouts.app')

@section('title', 'Tambah Qlola Non Debitur')
@section('page-title', 'Tambah Data Qlola Non Debitur')

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

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
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
        font-weight: 600;
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
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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

<div class="form-container">
    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('qlola-non-debitur.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="kode_kanca">Kode Kanca</label>
            <input type="text" name="kode_kanca" id="kode_kanca" value="{{ old('kode_kanca') }}">
        </div>

        <div class="form-group">
            <label for="kanca">Kanca</label>
            <input type="text" name="kanca" id="kanca" value="{{ old('kanca') }}">
        </div>

        <div class="form-group">
            <label for="kode_uker">Kode Uker</label>
            <input type="text" name="kode_uker" id="kode_uker" value="{{ old('kode_uker') }}">
        </div>

        <div class="form-group">
            <label for="uker">Uker</label>
            <input type="text" name="uker" id="uker" value="{{ old('uker') }}">
        </div>

        <div class="form-group">
            <label for="cifno">CIFNO</label>
            <input type="text" name="cifno" id="cifno" value="{{ old('cifno') }}">
        </div>

        <div class="form-group">
            <label for="norek_simpanan">Norek Simpanan</label>
            <input type="text" name="norek_simpanan" id="norek_simpanan" value="{{ old('norek_simpanan') }}">
        </div>

        <div class="form-group">
            <label for="norek_pinjaman">Norek Pinjaman</label>
            <input type="text" name="norek_pinjaman" id="norek_pinjaman" value="{{ old('norek_pinjaman') }}">
        </div>

        <div class="form-group">
            <label for="balance">Balance</label>
            <input type="text" name="balance" id="balance" value="{{ old('balance') }}">
        </div>

        <div class="form-group">
            <label for="nama_nasabah">Nama Nasabah</label>
            <input type="text" name="nama_nasabah" id="nama_nasabah" value="{{ old('nama_nasabah') }}">
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('qlola-non-debitur.index') }}" class="btn btn-secondary">↩️ Kembali</a>
        </div>
    </form>
</div>
@endsection
