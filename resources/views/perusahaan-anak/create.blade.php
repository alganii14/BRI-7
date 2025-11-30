@extends('layouts.app')

@section('title', 'Tambah Perusahaan Anak')
@section('page-title', 'Tambah Data Perusahaan Anak')

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

    textarea {
        min-height: 100px;
        resize: vertical;
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
    <form action="{{ route('perusahaan-anak.store') }}" method="POST">
        @csrf
        
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="nama_partner_vendor">Nama Partner/Vendor/Mitra/Distributor</label>
                <input type="text" id="nama_partner_vendor" name="nama_partner_vendor" value="{{ old('nama_partner_vendor') }}">
                @error('nama_partner_vendor')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jenis_usaha">Jenis Usaha</label>
                <input type="text" id="jenis_usaha" name="jenis_usaha" value="{{ old('jenis_usaha') }}">
                @error('jenis_usaha')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_perusahaan_anak">Nama Perusahaan Anak</label>
                <select id="nama_perusahaan_anak" name="nama_perusahaan_anak">
                    <option value="">-- Pilih Perusahaan Anak --</option>
                    <option value="BRIF" {{ old('nama_perusahaan_anak') == 'BRIF' ? 'selected' : '' }}>BRIF</option>
                    <option value="Pegadaian" {{ old('nama_perusahaan_anak') == 'Pegadaian' ? 'selected' : '' }}>Pegadaian</option>
                    <option value="PNM" {{ old('nama_perusahaan_anak') == 'PNM' ? 'selected' : '' }}>PNM</option>
                    <option value="BRI Life" {{ old('nama_perusahaan_anak') == 'BRI Life' ? 'selected' : '' }}>BRI Life</option>
                    <option value="BRI Danareksa" {{ old('nama_perusahaan_anak') == 'BRI Danareksa' ? 'selected' : '' }}>BRI Danareksa</option>
                </select>
                @error('nama_perusahaan_anak')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group full-width">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kode_cabang_induk">Kode Cabang Induk</label>
                <input type="text" id="kode_cabang_induk" name="kode_cabang_induk" value="{{ old('kode_cabang_induk') }}">
                @error('kode_cabang_induk')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cabang_induk_terdekat">Cabang Induk Terdekat</label>
                <input type="text" id="cabang_induk_terdekat" name="cabang_induk_terdekat" value="{{ old('cabang_induk_terdekat') }}">
                @error('cabang_induk_terdekat')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kode_uker">Kode Uker</label>
                <input type="text" id="kode_uker" name="kode_uker" value="{{ old('kode_uker') }}">
                @error('kode_uker')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_uker">Nama Uker</label>
                <input type="text" id="nama_uker" name="nama_uker" value="{{ old('nama_uker') }}">
                @error('nama_uker')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama_pic_partner">Nama PIC Partner</label>
                <input type="text" id="nama_pic_partner" name="nama_pic_partner" value="{{ old('nama_pic_partner') }}">
                @error('nama_pic_partner')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="posisi_pic_partner">Posisi PIC Partner</label>
                <input type="text" id="posisi_pic_partner" name="posisi_pic_partner" value="{{ old('posisi_pic_partner') }}">
                @error('posisi_pic_partner')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="hp_pic_partner">HP PIC Partner</label>
                <input type="text" id="hp_pic_partner" name="hp_pic_partner" value="{{ old('hp_pic_partner') }}">
                @error('hp_pic_partner')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status_pipeline">Status Pipeline</label>
                <select id="status_pipeline" name="status_pipeline">
                    <option value="">-- Pilih Status --</option>
                    <option value="Sudah Terakuisisi" {{ old('status_pipeline') == 'Sudah Terakuisisi' ? 'selected' : '' }}>Sudah Terakuisisi</option>
                    <option value="Belum Terakuisisi/Rekening Belum Ada" {{ old('status_pipeline') == 'Belum Terakuisisi/Rekening Belum Ada' ? 'selected' : '' }}>Belum Terakuisisi/Rekening Belum Ada</option>
                </select>
                @error('status_pipeline')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="rekening_terbentuk">Rekening Terbentuk</label>
                <input type="text" id="rekening_terbentuk" name="rekening_terbentuk" value="{{ old('rekening_terbentuk') }}">
                @error('rekening_terbentuk')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cif_terbentuk">CIF Terbentuk</label>
                <input type="text" id="cif_terbentuk" name="cif_terbentuk" value="{{ old('cif_terbentuk') }}">
                @error('cif_terbentuk')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('perusahaan-anak.index') }}" class="btn btn-secondary">↩️ Kembali</a>
        </div>
    </form>
</div>
@endsection
