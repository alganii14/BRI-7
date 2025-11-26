@extends('layouts.app')

@section('title', 'Tambah Uker')
@section('page-title', 'Tambah Unit Kerja')

@section('content')
<div class="page-header">
    <h2>Tambah Unit Kerja Baru</h2>
    <p>Isi form di bawah untuk menambah data Uker</p>
</div>

<div class="card">
    <form action="{{ route('uker.store') }}" method="POST" id="ukerForm">
        @csrf
        
        <!-- STEP 1: Pilih Kanca (WAJIB) -->
        <div class="form-section">
            <h3 class="section-title">📍 Pilih Kanca Terlebih Dahulu</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="select_kanca">Kanca <span class="required">*</span></label>
                    <select id="select_kanca" class="form-control" required>
                        <option value="">-- Pilih Kanca --</option>
                        @foreach($kancaList as $kanca)
                        <option value="{{ $kanca->kode_kanca }}" 
                                data-kanca="{{ $kanca->kanca }}"
                                data-kanwil="{{ $kanca->kanwil }}"
                                data-kode-kanwil="{{ $kanca->kode_kanwil }}">
                            {{ $kanca->kode_kanca }} - {{ $kanca->kanca }}
                        </option>
                        @endforeach
                    </select>
                    <small class="form-hint">Pilih Kanca untuk melanjutkan pengisian form</small>
                </div>
            </div>
        </div>

        <!-- STEP 2: Form Detail (Disabled sampai Kanca dipilih) -->
        <div class="form-section" id="detailSection" style="opacity: 0.5; pointer-events: none;">
            <h3 class="section-title">📝 Detail Unit Kerja</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="kode_sub_kanca">Kode Sub Kanca <span class="required">*</span></label>
                    <input type="text" id="kode_sub_kanca" name="kode_sub_kanca" value="{{ old('kode_sub_kanca') }}" required class="form-control @error('kode_sub_kanca') is-invalid @enderror" disabled>
                    @error('kode_sub_kanca')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sub_kanca">Sub Kanca <span class="required">*</span></label>
                    <input type="text" id="sub_kanca" name="sub_kanca" value="{{ old('sub_kanca') }}" required class="form-control @error('sub_kanca') is-invalid @enderror" disabled>
                    @error('sub_kanca')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="segment">Segment</label>
                    <select id="segment" name="segment" class="form-control" disabled>
                        <option value="">Pilih Segment</option>
                        <option value="MIKRO" {{ old('segment') == 'MIKRO' ? 'selected' : '' }}>MIKRO</option>
                        <option value="RETAIL" {{ old('segment') == 'RETAIL' ? 'selected' : '' }}>RETAIL</option>
                        <option value="KOMERSIAL" {{ old('segment') == 'KOMERSIAL' ? 'selected' : '' }}>KOMERSIAL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kode_kanca">Kode Kanca <span class="required">*</span></label>
                    <input type="text" id="kode_kanca" name="kode_kanca" value="{{ old('kode_kanca') }}" class="form-control" readonly required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kanca">Kanca <span class="required">*</span></label>
                    <input type="text" id="kanca" name="kanca" value="{{ old('kanca') }}" class="form-control" readonly required>
                </div>

                <div class="form-group">
                    <label for="kanwil">Kanwil</label>
                    <input type="text" id="kanwil" name="kanwil" value="{{ old('kanwil') }}" class="form-control" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="kode_kanwil">Kode Kanwil</label>
                <input type="text" id="kode_kanwil" name="kode_kanwil" value="{{ old('kode_kanwil') }}" class="form-control" readonly>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('uker.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Simpan</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .form-section.active {
        background: white;
        border: 2px solid #0066CC;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }

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

    .form-control:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }

    .form-control[readonly] {
        background-color: #f0f0f0;
        cursor: default;
    }

    .form-control.is-invalid {
        border-color: #f44336;
    }

    .form-hint {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #666;
        font-style: italic;
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

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectKanca = document.getElementById('select_kanca');
    const detailSection = document.getElementById('detailSection');
    const submitBtn = document.getElementById('submitBtn');
    
    // Field yang akan di-enable/disable
    const detailFields = [
        'kode_sub_kanca',
        'sub_kanca',
        'segment'
    ];
    
    // Field yang akan di-autofill dari Kanca
    const kodeKancaInput = document.getElementById('kode_kanca');
    const kancaInput = document.getElementById('kanca');
    const kanwilInput = document.getElementById('kanwil');
    const kodeKanwilInput = document.getElementById('kode_kanwil');
    
    selectKanca.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Enable detail section
            detailSection.style.opacity = '1';
            detailSection.style.pointerEvents = 'auto';
            detailSection.classList.add('active');
            
            // Enable input fields
            detailFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) field.disabled = false;
            });
            
            // Auto-fill data Kanca
            kodeKancaInput.value = this.value;
            kancaInput.value = selectedOption.dataset.kanca || '';
            kanwilInput.value = selectedOption.dataset.kanwil || '';
            kodeKanwilInput.value = selectedOption.dataset.kodeKanwil || '';
            
            // Enable submit button
            submitBtn.disabled = false;
            
            // Focus ke field pertama
            document.getElementById('kode_sub_kanca').focus();
        } else {
            // Disable detail section
            detailSection.style.opacity = '0.5';
            detailSection.style.pointerEvents = 'none';
            detailSection.classList.remove('active');
            
            // Disable input fields
            detailFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = true;
                    field.value = '';
                }
            });
            
            // Clear auto-filled data
            kodeKancaInput.value = '';
            kancaInput.value = '';
            kanwilInput.value = '';
            kodeKanwilInput.value = '';
            
            // Disable submit button
            submitBtn.disabled = true;
        }
    });
});
</script>
@endpush
