@extends('layouts.app')

@section('title', 'Edit Pipeline')
@section('page-title', 'Edit Pipeline')

@section('content')
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #0066CC;
    }

    .form-group input:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .btn {
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .section-header {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        margin: 24px -24px 20px -24px;
        font-size: 16px;
        font-weight: 600;
    }

    .section-header:first-child {
        margin-top: -24px;
    }
</style>

<div class="card">
    <div class="section-header">
       Edit Pipeline
    </div>

    @if ($errors->any())
    <div style="background-color: #fee; border: 1px solid #fcc; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #c33;">
        <strong>Error:</strong>
        <ul style="margin: 8px 0 0 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pipeline.update', $pipeline->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label>TANGGAL <span style="color: red;">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $pipeline->tanggal->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label>KODE KC</label>
                <input type="text" value="{{ $pipeline->kode_kc }}" disabled>
            </div>

            <div class="form-group">
                <label>NAMA KC</label>
                <input type="text" value="{{ $pipeline->nama_kc }}" disabled>
            </div>

            <div class="form-group">
                <label>PILIH UKER</label>
                <select name="uker_select" id="uker_select" onchange="handleUkerChange()">
                    <option value="">-- Pilih Unit Kerja --</option>
                    @foreach($listUker as $uker)
                        <option value="{{ $uker->kode_sub_kanca }}" 
                                data-nama="{{ $uker->sub_kanca }}"
                                {{ old('kode_uker', $pipeline->kode_uker) == $uker->kode_sub_kanca ? 'selected' : '' }}>
                            {{ $uker->sub_kanca }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>KODE UKER</label>
                <input type="text" name="kode_uker" id="kode_uker" value="{{ old('kode_uker', $pipeline->kode_uker) }}" readonly>
            </div>

            <div class="form-group">
                <label>NAMA UKER</label>
                <input type="text" name="nama_uker" id="nama_uker" value="{{ old('nama_uker', $pipeline->nama_uker) }}" readonly>
            </div>
        </div>

        <div class="form-group">
            <label>STRATEGY PULL OF PIPELINE <span style="color: red;">*</span></label>
            <select name="strategy_pipeline" id="strategy_pipeline" required onchange="handleStrategyChange()">
                <option value="">-- Pilih Strategi --</option>
                <option value="Strategi 1" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 1' ? 'selected' : '' }}>Strategi 1 - Optimalisasi Digital Channel</option>
                <option value="Strategi 2" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 2' ? 'selected' : '' }}>Strategi 2 - Rekening Debitur Transaksi</option>
                <option value="Strategi 3" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 3' ? 'selected' : '' }}>Strategi 3 - Optimalisasi Business Cluster</option>
                <option value="Strategi 4" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 4' ? 'selected' : '' }}>Strategi 4 - Peningkatan Payroll Berkualitas</option>
                <option value="Strategi 6" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 6' ? 'selected' : '' }}>Strategi 6 - Kolaborasi Perusahaan Anak</option>
                <option value="Strategi 7" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 7' ? 'selected' : '' }}>Strategi 7 - Optimalisasi Nasabah Prioritas & BOC BOD Nasabah Wholesale & Komersial</option>
                <option value="Strategi 8" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Strategi 8' ? 'selected' : '' }}>Strategi 8 - Penguatan Produk & Fungsi RM</option>
                <option value="Layering" {{ old('strategy_pipeline', $pipeline->strategy_pipeline) == 'Layering' ? 'selected' : '' }}>Layering</option>
            </select>
        </div>

        <div class="form-group" id="kategori_strategi_container" style="display: {{ $pipeline->kategori_strategi ? 'block' : 'none' }};">
            <label>KATEGORI</label>
            <select name="kategori_strategi" id="kategori_strategi">
                <option value="">Pilih Kategori</option>
            </select>
        </div>



        <div class="form-group">
            <label>TIPE NASABAH <span style="color: red;">*</span></label>
            <select name="tipe_nasabah" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="lama" {{ old('tipe_nasabah', $pipeline->tipe) == 'lama' ? 'selected' : '' }}>Di Dalam Pipeline</option>
                <option value="baru" {{ old('tipe_nasabah', $pipeline->tipe) == 'baru' ? 'selected' : '' }}>Di Luar Pipeline</option>
            </select>
        </div>

        <div class="form-group">
            <label>NAMA NASABAH <span style="color: red;">*</span></label>
            <input type="text" name="nama_nasabah" value="{{ old('nama_nasabah', $pipeline->nama_nasabah) }}" required>
        </div>

        <div class="form-group">
            <label>NOREK / CIF</label>
            <input type="text" name="norek" value="{{ old('norek', $pipeline->norek) }}">
        </div>

        <div class="form-group">
            <label>KETERANGAN</label>
            <textarea name="keterangan" rows="3">{{ old('keterangan', $pipeline->keterangan) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('pipeline.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
// Handle Strategy Change
function handleStrategyChange() {
    const strategy = document.getElementById('strategy_pipeline').value;
    const kategoriContainer = document.getElementById('kategori_strategi_container');
    const kategoriSelect = document.getElementById('kategori_strategi');
    
    const currentKategori = '{{ old("kategori_strategi", $pipeline->kategori_strategi) }}';
    
    kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
    
    const kategoriMap = {
        'Strategi 1': [
            'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)',
            'Qlola (Belum ada Qlola / ada namun nonaktif)',
            'Qlola Non Debitur'
        ],
        'Strategi 2': [
            'Non Debitur Vol Besar CASA Kecil'
        ],
        'Strategi 3': [
            'AUM>2M DPK<50 juta'
        ],
        'Strategi 4': [
            'User Aktif Casa Kecil',
            'Existing Payroll',
            'Potensi Payroll'
        ],
        'Strategi 6': [
            'List Perusahaan Anak'
        ],
        'Strategi 7': [
            'PENURUNAN CASA PRIORITAS RITEL MIKRO'
        ],
        'Strategi 8': [
            'PENURUNAN CASA BRILINK',
            'PENURUNAN CASA MERCHANT (QRIS & EDC)',
            'Winback'
        ]
    };
    
    if (kategoriMap[strategy]) {
        kategoriContainer.style.display = 'block';
        kategoriMap[strategy].forEach(kategori => {
            const option = document.createElement('option');
            option.value = kategori;
            option.textContent = kategori;
            if (kategori === currentKategori) {
                option.selected = true;
            }
            kategoriSelect.appendChild(option);
        });
    } else {
        kategoriContainer.style.display = 'none';
    }
}

// Handle Uker Change
function handleUkerChange() {
    const ukerSelect = document.getElementById('uker_select');
    const selectedOption = ukerSelect.options[ukerSelect.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('kode_uker').value = selectedOption.value;
        document.getElementById('nama_uker').value = selectedOption.dataset.nama;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    handleStrategyChange();
});
</script>

@endsection
