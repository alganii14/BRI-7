@extends('layouts.app')

@section('title', 'Edit RMFT')
@section('page-title', 'Edit RMFT')

@section('content')
<div class="page-header">
    <h2>Edit Data RMFT</h2>
    <p>Update informasi RMFT</p>
</div>

<div class="card">
    <form action="{{ route('rmft.update', $rmft->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-row">
            <div class="form-group">
                <label for="pernr">PERNR</label>
                <input type="text" id="pernr" name="pernr" value="{{ old('pernr', $rmft->pernr) }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="completename">Nama Lengkap <span class="required">*</span></label>
                <input type="text" id="completename" name="completename" value="{{ old('completename', $rmft->completename) }}" required class="form-control @error('completename') is-invalid @enderror">
                @error('completename')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="jg">JG (Job Grade)</label>
                <input type="text" id="jg" name="jg" value="{{ old('jg', $rmft->jg) }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="esgdesc">Status</label>
                <select id="esgdesc" name="esgdesc" class="form-control">
                    <option value="">Pilih Status</option>
                    <option value="PT (peserta pens.)" {{ old('esgdesc', $rmft->esgdesc) == 'PT (peserta pens.)' ? 'selected' : '' }}>PT (peserta pens.)</option>
                    <option value="Pekerja Kontrak" {{ old('esgdesc', $rmft->esgdesc) == 'Pekerja Kontrak' ? 'selected' : '' }}>Pekerja Kontrak</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kode_kanca">Kode KC <span class="required">*</span></label>
                <select id="kode_kanca" name="kode_kanca" class="form-control" onchange="loadUkerByKC(this.value)">
                    <option value="">Pilih KC</option>
                    @foreach($kcList as $kc)
                        <option value="{{ $kc->kode_kanca }}" data-nama="{{ $kc->kanca }}" {{ old('kode_kanca', $currentKodeKanca) == $kc->kode_kanca ? 'selected' : '' }}>
                            {{ $kc->kode_kanca }} - {{ $kc->kanca }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" id="kanca" name="kanca" value="{{ old('kanca', $rmft->kanca) }}">
            </div>

            <div class="form-group">
                <label for="uker_id">Unit Kerja</label>
                <select id="uker_id" name="uker_id" class="form-control" onchange="setUkerName(this)">
                    <option value="">Pilih KC terlebih dahulu</option>
                </select>
                <input type="hidden" id="uker" name="uker" value="{{ old('uker', $rmft->uker) }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kelompok_jabatan">Kelompok Jabatan <span class="required">*</span></label>
                <select id="kelompok_jabatan" name="kelompok_jabatan" class="form-control" required>
                    <option value="">Pilih Kelompok Jabatan</option>
                    <option value="RMFT Individu Branch" {{ old('kelompok_jabatan', $rmft->kelompok_jabatan) == 'RMFT Individu Branch' ? 'selected' : '' }}>RMFT Individu Branch</option>
                    <option value="RMFT Individu Unit" {{ old('kelompok_jabatan', $rmft->kelompok_jabatan) == 'RMFT Individu Unit' ? 'selected' : '' }}>RMFT Individu Unit</option>
                    <option value="RMFT Generalis" {{ old('kelompok_jabatan', $rmft->kelompok_jabatan) == 'RMFT Generalis' ? 'selected' : '' }}>RMFT Generalis</option>
                    <option value="RMFT Business" {{ old('kelompok_jabatan', $rmft->kelompok_jabatan) == 'RMFT Business' ? 'selected' : '' }}>RMFT Business</option>
                </select>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $rmft->keterangan) }}" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('rmft.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>

<script>
const currentUkerId = '{{ $rmft->uker_id }}';
const currentUkerName = '{{ $rmft->uker }}';

function loadUkerByKC(kodeKanca, selectCurrentUker = false) {
    const ukerSelect = document.getElementById('uker_id');
    const kancaInput = document.getElementById('kanca');
    const ukerInput = document.getElementById('uker');
    const kodeKancaSelect = document.getElementById('kode_kanca');
    
    // Set nama kanca dari data attribute
    const selectedOption = kodeKancaSelect.options[kodeKancaSelect.selectedIndex];
    kancaInput.value = selectedOption.dataset.nama || '';
    
    // Reset uker
    ukerSelect.innerHTML = '<option value="">Memuat unit...</option>';
    
    if (!kodeKanca) {
        ukerSelect.innerHTML = '<option value="">Pilih KC terlebih dahulu</option>';
        ukerInput.value = '';
        return;
    }
    
    // Untuk dropdown, tampilkan pilihan sederhana: KC atau Unit
    ukerSelect.innerHTML = '<option value="">Pilih Unit Kerja</option>';
    
    // Option 1: KC (Branch) - nama KC
    const branchSelected = selectCurrentUker && currentUkerName === kancaInput.value;
    ukerSelect.innerHTML += `<option value="kc" data-nama="${kancaInput.value}" ${branchSelected ? 'selected' : ''}>${kancaInput.value}</option>`;
    
    // Option 2: Unit
    const unitSelected = selectCurrentUker && currentUkerName === 'Unit';
    ukerSelect.innerHTML += `<option value="unit" data-nama="Unit" ${unitSelected ? 'selected' : ''}>Unit</option>`;
}

function setUkerName(select) {
    const ukerInput = document.getElementById('uker');
    const selectedOption = select.options[select.selectedIndex];
    ukerInput.value = selectedOption.dataset.nama || '';
}

// Load uker on page load
document.addEventListener('DOMContentLoaded', function() {
    const kodeKanca = document.getElementById('kode_kanca').value;
    if (kodeKanca) {
        loadUkerByKC(kodeKanca, true);
    }
});
</script>
@endsection

@push('styles')
<style>
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    .required {
        color: #f44336;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #0066CC;
    }

    .form-control.is-invalid {
        border-color: #f44336;
    }

    .error-message {
        color: #f44336;
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }

    .btn {
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
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
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
