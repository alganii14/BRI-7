@extends('layouts.app')

@section('title', 'Import Qlola Nonaktif')

@section('page-title', 'Import Data Qlola Nonaktif')

@section('content')
<style>
    .import-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .import-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .card-title {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin: -30px -30px 25px -30px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .upload-area {
        border: 2px dashed #0066CC;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: linear-gradient(135deg, rgba(0,102,204,0.05) 0%, rgba(0,61,130,0.05) 100%);
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        border-color: #003D82;
        background: linear-gradient(135deg, rgba(0,102,204,0.1) 0%, rgba(0,61,130,0.1) 100%);
    }

    .upload-icon {
        font-size: 48px;
        color: #0066CC;
        margin-bottom: 15px;
    }

    .file-input-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
    }

    .file-input-label {
        display: inline-block;
        padding: 12px 24px;
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-input-label:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .file-name {
        margin-top: 15px;
        font-size: 14px;
        color: #495057;
    }

    .format-info {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .format-info ul {
        margin: 10px 0 0 20px;
        padding: 0;
    }

    .format-info li {
        margin: 5px 0;
        color: #495057;
    }

    .info-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-box ul {
        margin: 0 0 0 20px;
        padding: 0;
    }

    .info-box li {
        margin: 8px 0;
        color: #856404;
    }

    .danger-box {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        padding: 20px;
        border-radius: 8px;
    }

    .danger-box p {
        margin-bottom: 15px;
        color: #721c24;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,102,204,0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        font-size: 16px;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220,53,69,0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    @media (max-width: 968px) {
        .import-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<a href="{{ route('qlola-nonaktif.index') }}" class="btn btn-secondary">
    ↩️ Kembali
</a>

@if(session('success'))
    <div class="alert alert-success">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        ❌ {{ session('error') }}
    </div>
@endif

<div class="import-grid">
    <div class="import-card">
        <div class="card-title">
            📤 Upload File CSV
        </div>

        <form action="{{ route('qlola-nonaktif.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="upload-area">
                <div class="upload-icon">📁</div>
                <div class="file-input-wrapper">
                    <input type="file" id="file" name="file" accept=".csv" required onchange="updateFileName(this)">
                    <label for="file" class="file-input-label">
                        📂 Pilih File CSV
                    </label>
                </div>
                <div class="file-name" id="fileName">Belum ada file dipilih</div>
                @error('file')
                    <div style="color: #dc3545; margin-top: 10px;">{{ $message }}</div>
                @enderror
                <small style="display: block; margin-top: 15px; color: #6c757d;">
                    Format: CSV dengan delimiter comma (,) atau semicolon (;). Max 10MB.
                </small>
            </div>

            <div class="format-info">
                <strong style="color: #1976d2;">📋 Format CSV yang diharapkan:</strong>
                <ul>
                    <li>Kolom 1: KODE KANCA</li>
                    <li>Kolom 2: KANCA</li>
                    <li>Kolom 3: KODE UKER</li>
                    <li>Kolom 4: UKER</li>
                    <li>Kolom 5: CIFNO</li>
                    <li>Kolom 6: NOREK PINJAMAN</li>
                    <li>Kolom 7: NOREK SIMPANAN</li>
                    <li>Kolom 8: BALANCE</li>
                    <li>Kolom 9: NAMA DEBITUR</li>
                    <li>Kolom 10: PLAFON</li>
                    <li>Kolom 11: PN PENGELOLA</li>
                    <li>Kolom 12: KETERANGAN</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-primary">
                📤 Import Data
            </button>
        </form>
    </div>

    <div>
        <div class="import-card" style="margin-bottom: 20px;">
            <div class="card-title" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                ⚠️ Perhatian!
            </div>

            <div class="info-box">
                <ul>
                    <li>Pastikan format CSV sesuai dengan template</li>
                    <li>Gunakan delimiter <strong>comma (,)</strong> atau <strong>semicolon (;)</strong></li>
                    <li>Sistem akan otomatis mendeteksi delimiter</li>
                    <li>Baris pertama adalah header (akan di-skip)</li>
                    <li>Data akan ditambahkan ke database</li>
                    <li>Proses import mungkin memakan waktu untuk file besar</li>
                </ul>
            </div>
        </div>

        <div class="import-card">
            <div class="card-title" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                🗑️ Hapus Semua Data
            </div>

            <div class="danger-box">
                <p><strong>Gunakan dengan hati-hati!</strong></p>
                <p>Tombol ini akan menghapus semua data sebelum import data baru.</p>
                
                <form action="{{ route('qlola-nonaktif.delete-all') }}" method="POST" 
                      onsubmit="return confirm('PERHATIAN: Ini akan menghapus SEMUA data Qlola Nonaktif. Yakin ingin melanjutkan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        🗑️ Hapus Semua Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileNameDiv = document.getElementById('fileName');
    
    if (input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
        fileNameDiv.textContent = `${fileName} (${fileSize} MB)`;
        fileNameDiv.style.color = '#28a745';
        fileNameDiv.style.fontWeight = '600';
    } else {
        fileNameDiv.textContent = 'Belum ada file dipilih';
        fileNameDiv.style.color = '#6c757d';
        fileNameDiv.style.fontWeight = 'normal';
    }
}
</script>
@endsection
