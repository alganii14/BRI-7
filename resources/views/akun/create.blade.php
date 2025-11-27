@extends('layouts.app')

@section('title', 'Tambah Akun Baru')
@section('page-title', 'Tambah Akun Baru')

@section('content')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

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

    .form-group label .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #0066CC;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }

    .btn {
        padding: 10px 24px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
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

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }

    .card-header {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 8px 8px 0 0;
        font-size: 16px;
        font-weight: 600;
    }

    .card-body {
        padding: 24px;
    }

    .help-text {
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }

    #rmftFields {
        display: none;
        padding: 16px;
        background-color: #f8f9fa;
        border-radius: 6px;
        margin-top: 12px;
    }

    #managerFields {
        display: none;
        padding: 16px;
        background-color: #f8f9fa;
        border-radius: 6px;
        margin-top: 12px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.3s;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .close {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        line-height: 1;
    }

    .close:hover {
        opacity: 0.8;
    }

    .modal-body {
        padding: 20px;
    }

    .search-box {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 16px;
    }

    .search-box:focus {
        outline: none;
        border-color: #0066CC;
    }

    .kanca-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
    }

    .kanca-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .kanca-item:last-child {
        border-bottom: none;
    }

    .kanca-item:hover {
        background-color: #f8f9fa;
    }

    .kanca-item.selected {
        background-color: #e3f2fd;
    }

    .kanca-code {
        font-weight: 600;
        color: #0066CC;
        margin-right: 8px;
    }

    .kanca-name {
        color: #333;
    }

    .no-results {
        padding: 20px;
        text-align: center;
        color: #999;
    }

    .btn-select {
        background-color: #e0e0e0;
        color: #333;
        border: 1px solid #ccc;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-select:hover {
        background-color: #d0d0d0;
    }

    .btn-select i {
        font-size: 16px;
    }
</style>

<div class="form-container">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-person-plus"></i> Tambah Akun Baru
        </div>
        <div class="card-body">
            <form action="{{ route('akun.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                    <div class="help-text">Minimal 6 karakter</div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="role">Role <span class="required">*</span></label>
                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="rmft" {{ old('role') == 'rmft' ? 'selected' : '' }}>RMFT</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fields khusus RMFT -->
                <div id="rmftFields">
                    <div class="form-group">
                        <label for="pernr">PERNR</label>
                        <input type="text" name="pernr" id="pernr" class="form-control @error('pernr') is-invalid @enderror" 
                               value="{{ old('pernr') }}" readonly>
                        @error('pernr')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="rmft_display">Data RMFT</label>
                        <button type="button" class="btn-select" id="btnOpenRmftModal">
                            <i class="bi bi-search"></i>
                            <span id="rmft_display">Klik untuk memilih Data RMFT (Opsional)</span>
                        </button>
                        <div class="help-text">Pilih jika ingin menghubungkan dengan data RMFT yang sudah ada</div>
                        @error('rmft_id')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                        <input type="hidden" name="rmft_id" id="rmft_id" value="{{ old('rmft_id') }}">
                    </div>

                    <div class="form-group">
                        <label for="kode_kanca_rmft">Kode Kanca</label>
                        <input type="text" name="kode_kanca" id="kode_kanca_rmft" class="form-control" 
                               value="{{ old('kode_kanca') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nama_kanca_rmft">Nama Kanca</label>
                        <input type="text" name="nama_kanca" id="nama_kanca_rmft" class="form-control" 
                               value="{{ old('nama_kanca') }}" readonly>
                    </div>
                </div>

                <!-- Fields khusus Manager -->
                <div id="managerFields">
                    <div class="form-group">
                        <label for="kanca_display">Pilih Kanca <span class="required">*</span></label>
                        <button type="button" class="btn-select" id="btnOpenKancaModal">
                            <i class="bi bi-search"></i>
                            <span id="kanca_display">Klik untuk memilih Kanca</span>
                        </button>
                        @error('kode_kanca')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode_kanca">Kode Kanca</label>
                        <input type="text" name="kode_kanca" id="kode_kanca" class="form-control" 
                               value="{{ old('kode_kanca') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nama_kanca">Nama Kanca</label>
                        <input type="text" name="nama_kanca" id="nama_kanca" class="form-control" 
                               value="{{ old('nama_kanca') }}" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="{{ route('akun.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pilih Kanca -->
<div id="kancaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="bi bi-building"></i> Pilih Kanca</h3>
            <button class="close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="searchKanca" class="search-box" placeholder="Cari kode atau nama kanca...">
            <div class="kanca-list" id="kancaList">
                <div class="no-results">Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih RMFT -->
<div id="rmftModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="bi bi-person-badge"></i> Pilih Data RMFT</h3>
            <button class="close" id="closeRmftModal">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="searchRmft" class="search-box" placeholder="Cari PERNR, nama, atau kanca...">
            <div class="kanca-list" id="rmftList">
                <div class="no-results">Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<script>
let kancaData = [];
let selectedKanca = null;
let rmftData = @json($rmftList);
let selectedRmft = null;

// Load list kanca dari API
async function loadKancaList() {
    try {
        const response = await fetch('{{ route("api.uker.by-kc") }}?all=1');
        const data = await response.json();
        
        // Get unique kanca
        const kancaMap = new Map();
        data.forEach(uker => {
            if (uker.kode_kanca && uker.kanca) {
                kancaMap.set(uker.kode_kanca, uker.kanca);
            }
        });
        
        // Sort by kode_kanca
        kancaData = Array.from(kancaMap.entries())
            .map(([kode, nama]) => ({ kode, nama }))
            .sort((a, b) => a.kode.localeCompare(b.kode));
        
        renderKancaList(kancaData);
    } catch (error) {
        console.error('Error loading kanca list:', error);
        document.getElementById('kancaList').innerHTML = '<div class="no-results">Gagal memuat data</div>';
    }
}

// Render kanca list
function renderKancaList(data) {
    const kancaList = document.getElementById('kancaList');
    
    if (data.length === 0) {
        kancaList.innerHTML = '<div class="no-results">Tidak ada data ditemukan</div>';
        return;
    }
    
    kancaList.innerHTML = '';
    data.forEach(kanca => {
        const item = document.createElement('div');
        item.className = 'kanca-item';
        if (selectedKanca && selectedKanca.kode === kanca.kode) {
            item.classList.add('selected');
        }
        item.innerHTML = '<span class="kanca-code">' + kanca.kode + '</span><span class="kanca-name">' + kanca.nama + '</span>';
        item.onclick = function() {
            selectKanca(kanca);
        };
        kancaList.appendChild(item);
    });
}

// Select kanca
function selectKanca(kanca) {
    selectedKanca = kanca;
    document.getElementById('kode_kanca').value = kanca.kode;
    document.getElementById('nama_kanca').value = kanca.nama;
    document.getElementById('kanca_display').textContent = kanca.kode + ' - ' + kanca.nama;
    closeKancaModal();
}

// Search kanca
document.getElementById('searchKanca').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const filtered = kancaData.filter(kanca => 
        kanca.kode.toLowerCase().includes(searchTerm) || 
        kanca.nama.toLowerCase().includes(searchTerm)
    );
    renderKancaList(filtered);
});

// Open modal
document.getElementById('btnOpenKancaModal').addEventListener('click', function() {
    document.getElementById('kancaModal').style.display = 'block';
    document.getElementById('searchKanca').value = '';
    document.getElementById('searchKanca').focus();
    renderKancaList(kancaData);
});

// Close modal
function closeKancaModal() {
    document.getElementById('kancaModal').style.display = 'none';
}

document.getElementById('closeModal').addEventListener('click', closeKancaModal);

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const kancaModal = document.getElementById('kancaModal');
    const rmftModal = document.getElementById('rmftModal');
    if (event.target === kancaModal) {
        closeKancaModal();
    }
    if (event.target === rmftModal) {
        closeRmftModal();
    }
});

// ===== RMFT Modal Functions =====

// Render RMFT list
function renderRmftList(data) {
    const rmftList = document.getElementById('rmftList');
    
    if (data.length === 0) {
        rmftList.innerHTML = '<div class="no-results">Tidak ada data ditemukan</div>';
        return;
    }
    
    rmftList.innerHTML = '';
    data.forEach(rmft => {
        const item = document.createElement('div');
        item.className = 'kanca-item';
        if (selectedRmft && selectedRmft.id === rmft.id) {
            item.classList.add('selected');
        }
        
        const pernr = rmft.pernr || '-';
        const nama = rmft.completename || '-';
        const kanca = rmft.kanca || '-';
        
        item.innerHTML = '<div><span class="kanca-code">' + pernr + '</span><span class="kanca-name">' + nama + '</span></div><div style="font-size: 12px; color: #666; margin-top: 4px;">Kanca: ' + kanca + '</div>';
        item.onclick = function() {
            selectRmft(rmft);
        };
        rmftList.appendChild(item);
    });
}

// Select RMFT
function selectRmft(rmft) {
    selectedRmft = rmft;
    document.getElementById('rmft_id').value = rmft.id;
    document.getElementById('pernr').value = rmft.pernr || '';
    
    // Get kode_kanca from uker_relation or fallback to kanca field
    let kodeKanca = '';
    let namaKanca = rmft.kanca || '';
    
    if (rmft.uker_relation && rmft.uker_relation.kode_kanca) {
        kodeKanca = rmft.uker_relation.kode_kanca;
        namaKanca = rmft.uker_relation.kanca || namaKanca;
    }
    
    document.getElementById('kode_kanca_rmft').value = kodeKanca;
    document.getElementById('nama_kanca_rmft').value = namaKanca;
    
    const displayText = (rmft.pernr || '') + ' - ' + (rmft.completename || '') + ' (' + namaKanca + ')';
    document.getElementById('rmft_display').textContent = displayText;
    closeRmftModal();
}

// Search RMFT
document.getElementById('searchRmft').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const filtered = rmftData.filter(rmft => {
        const pernr = (rmft.pernr || '').toLowerCase();
        const nama = (rmft.completename || '').toLowerCase();
        const kanca = (rmft.kanca || '').toLowerCase();
        return pernr.includes(searchTerm) || nama.includes(searchTerm) || kanca.includes(searchTerm);
    });
    renderRmftList(filtered);
});

// Open RMFT modal
document.getElementById('btnOpenRmftModal').addEventListener('click', function() {
    document.getElementById('rmftModal').style.display = 'block';
    document.getElementById('searchRmft').value = '';
    document.getElementById('searchRmft').focus();
    renderRmftList(rmftData);
});

// Close RMFT modal
function closeRmftModal() {
    document.getElementById('rmftModal').style.display = 'none';
}

document.getElementById('closeRmftModal').addEventListener('click', closeRmftModal);

// Role change handler
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const rmftFields = document.getElementById('rmftFields');
    const managerFields = document.getElementById('managerFields');
    
    // Reset display
    rmftFields.style.display = 'none';
    managerFields.style.display = 'none';
    
    // Show relevant fields
    if (role === 'rmft') {
        rmftFields.style.display = 'block';
    } else if (role === 'manager') {
        managerFields.style.display = 'block';
        // Load kanca list when manager is selected
        if (kancaData.length === 0) {
            loadKancaList();
        }
    }
});

// Trigger on page load if old value exists
window.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection
