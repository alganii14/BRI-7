@extends('layouts.app')

@section('title', 'Edit User Aktif Casa Kecil')
@section('page-title', 'Edit Data User Aktif Casa Kecil')

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
    <form action="{{ route('user-aktif-casa-kecil.update', $userAktifCasaKecil->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group">
                <label for="kode_kanca">Kode Kanca <span style="color:red;">*</span></label>
                <input type="text" id="kode_kanca" name="kode_kanca" value="{{ old('kode_kanca', $userAktifCasaKecil->kode_kanca) }}" required>
                @error('kode_kanca')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kanca">Kanca <span style="color:red;">*</span></label>
                <input type="text" id="kanca" name="kanca" value="{{ old('kanca', $userAktifCasaKecil->kanca) }}" required>
                @error('kanca')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kode_uker">Kode Uker <span style="color:red;">*</span></label>
                <input type="text" id="kode_uker" name="kode_uker" value="{{ old('kode_uker', $userAktifCasaKecil->kode_uker) }}" required>
                @error('kode_uker')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="uker">Uker <span style="color:red;">*</span></label>
                <input type="text" id="uker" name="uker" value="{{ old('uker', $userAktifCasaKecil->uker) }}" required>
                @error('uker')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cifno">CIFNO <span style="color:red;">*</span></label>
                <input type="text" id="cifno" name="cifno" value="{{ old('cifno', $userAktifCasaKecil->cifno) }}" required>
                @error('cifno')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="norek_pinjaman">Norek Pinjaman</label>
                <input type="text" id="norek_pinjaman" name="norek_pinjaman" value="{{ old('norek_pinjaman', $userAktifCasaKecil->norek_pinjaman) }}">
                @error('norek_pinjaman')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="norek_simpanan">Norek Simpanan</label>
                <input type="text" id="norek_simpanan" name="norek_simpanan" value="{{ old('norek_simpanan', $userAktifCasaKecil->norek_simpanan) }}">
                @error('norek_simpanan')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="balance">Balance</label>
                <input type="text" id="balance" name="balance" value="{{ old('balance', $userAktifCasaKecil->balance) }}">
                @error('balance')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="volume">Volume</label>
                <input type="text" id="volume" name="volume" value="{{ old('volume', $userAktifCasaKecil->volume) }}">
                @error('volume')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_debitur">Nama Debitur <span style="color:red;">*</span></label>
                <input type="text" id="nama_debitur" name="nama_debitur" value="{{ old('nama_debitur', $userAktifCasaKecil->nama_debitur) }}" required>
                @error('nama_debitur')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="plafon">Plafon</label>
                <input type="text" id="plafon" name="plafon" value="{{ old('plafon', $userAktifCasaKecil->plafon) }}">
                @error('plafon')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="pn_pengelola_1">PN Pengelola</label>
                <input type="text" id="pn_pengelola_1" name="pn_pengelola_1" value="{{ old('pn_pengelola_1', $userAktifCasaKecil->pn_pengelola_1) }}">
                @error('pn_pengelola_1')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group full-width">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $userAktifCasaKecil->keterangan) }}</textarea>
                @error('keterangan')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update</button>
            <a href="{{ route('user-aktif-casa-kecil.index') }}" class="btn btn-secondary">↩️ Kembali</a>
        </div>
    </form>
</div>
@endsection
