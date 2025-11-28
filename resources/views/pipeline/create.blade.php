@extends('layouts.app')

@section('title', 'Tambah Pipeline')
@section('page-title', 'Tambah Pipeline')

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
       Data Pipeline
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

    <form action="{{ route('pipeline.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>TANGGAL <span style="color: red;">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="form-group">
                <label>PILIH KC <span style="color: red;">*</span></label>
                <select name="kc_select" id="kc_select" required onchange="handleKCChange()">
                    <option value="">-- Pilih KC --</option>
                    @foreach($listKC as $kc)
                        <option value="{{ $kc->kode_kanca }}" data-nama="{{ $kc->kanca }}">
                            {{ $kc->kanca }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group">
                <label>KODE KC <span style="color: red;">*</span></label>
                <input type="text" name="kode_kc" id="kode_kc" value="{{ old('kode_kc', auth()->user()->kode_kanca ?? '') }}" readonly required>
            </div>

            <div class="form-group">
                <label>NAMA KC <span style="color: red;">*</span></label>
                <input type="text" name="nama_kc" id="nama_kc" value="{{ old('nama_kc', auth()->user()->nama_kanca ?? '') }}" readonly required>
            </div>

            <div class="form-group">
                <label>PILIH UKER <span style="color: red;">*</span></label>
                <select name="uker_select" id="uker_select" required onchange="handleUkerChange()" {{ auth()->user()->isManager() ? '' : 'disabled' }}>
                    <option value="">-- Pilih Unit Kerja --</option>
                    @if(auth()->user()->isManager())
                        @foreach($listUker as $uker)
                            <option value="{{ $uker->kode_sub_kanca }}" data-nama="{{ $uker->sub_kanca }}">
                                {{ $uker->sub_kanca }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group">
                <label>KODE UKER <span style="color: red;">*</span></label>
                <input type="text" name="kode_uker" id="kode_uker" value="{{ old('kode_uker') }}" readonly required>
            </div>

            <div class="form-group">
                <label>NAMA UKER <span style="color: red;">*</span></label>
                <input type="text" name="nama_uker" id="nama_uker" value="{{ old('nama_uker') }}" readonly required>
            </div>
        </div>

        <div class="form-group">
            <label>STRATEGY PULL OF PIPELINE <span style="color: red;">*</span></label>
            <select name="strategy_pipeline" id="strategy_pipeline" required onchange="handleStrategyChange()">
                <option value="">-- Pilih Strategi --</option>
                <option value="Strategi 1">Strategi 1 - Optimalisasi Digital Channel</option>
                <option value="Strategi 2">Strategi 2 - Rekening Debitur Transaksi</option>
                <option value="Strategi 3">Strategi 3 - Optimalisasi Business Cluster</option>
                <option value="Strategi 4">Strategi 4 - Peningkatan Payroll Berkualitas</option>
                <option value="Strategi 6">Strategi 6 - Kolaborasi Perusahaan Anak</option>
                <option value="Strategi 7">Strategi 7 - Optimalisasi Nasabah Prioritas & BOC BOD Nasabah Wholesale & Komersial</option>
                <option value="Strategi 8">Strategi 8 - Penguatan Produk & Fungsi RM</option>
                <option value="Layering">Layering</option>
            </select>
        </div>

        <div class="form-group" id="kategori_strategi_container" style="display: none;">
            <label>KATEGORI <span style="color: red;">*</span></label>
            <select name="kategori_strategi" id="kategori_strategi">
                <option value="">Pilih Kategori</option>
            </select>
        </div>



        <div class="form-group">
            <label>TIPE NASABAH <span style="color: red;">*</span></label>
            <select name="tipe_nasabah" id="tipe_nasabah" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="lama">Di Dalam Pipeline</option>
                <option value="baru">Di Luar Pipeline</option>
            </select>
        </div>

        <!-- Form untuk Nasabah Lama (Di Dalam Pipeline) -->
        <div id="form_nasabah_lama" style="display: none;">
            <div class="form-group">
                <label>CARI NASABAH <span style="color: red;">*</span></label>
                <div style="position: relative;">
                    <input type="text" id="norek" name="norek" value="{{ old('norek') }}" placeholder="Klik tombol search untuk mencari nasabah" autocomplete="off" style="padding-right: 45px;" readonly>
                    <button type="button" id="btn_search_nasabah" onclick="openNasabahModal()" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
                <small style="color: #666;">Bisa pilih multiple nasabah sekaligus</small>
            </div>

            <div class="form-group">
                <label>NAMA NASABAH <span style="color: red;">*</span></label>
                <textarea id="nama_nasabah" name="nama_nasabah" readonly style="min-height: 80px; resize: vertical;">{{ old('nama_nasabah') }}</textarea>
                <small style="color: #666;">Daftar nasabah yang dipilih</small>
            </div>
            
            <!-- Hidden field untuk menyimpan data nasabah dalam JSON -->
            <input type="hidden" id="nasabah_data_json" name="nasabah_data_json" value="">
        </div>

        <!-- Form untuk Nasabah Baru (Di Luar Pipeline) -->
        <div id="form_nasabah_baru" style="display: none;">
            <div class="form-group">
                <label>NO. REKENING / CIF</label>
                <input type="text" id="norek_baru" name="norek_baru" value="{{ old('norek_baru') }}" placeholder="Masukkan nomor rekening (opsional)">
            </div>

            <div class="form-group">
                <label>NAMA NASABAH <span style="color: red;">*</span></label>
                <input type="text" id="nama_nasabah_baru" name="nama_nasabah_baru" value="{{ old('nama_nasabah_baru') }}" placeholder="Masukkan nama nasabah">
            </div>
        </div>

        <div class="form-group">
            <label>KETERANGAN</label>
            <textarea name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('pipeline.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<!-- Modal Nasabah -->
<div id="nasabahModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 900px; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); flex-shrink: 0;">
            <h3 style="margin: 0; color: white;">Pilih Nasabah dari Pull of Pipeline</h3>
            <button onclick="closeNasabahModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px;">&times;</button>
        </div>
        
        <div style="padding: 20px; display: flex; flex-direction: column; overflow: hidden; flex: 1;">
            <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: center;">
                <input type="text" id="searchNasabah" placeholder="Cari nasabah berdasarkan nama, CIFNO, atau nomor rekening..." style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;" onkeyup="searchNasabahList()">
                <div id="selectedCount" style="padding: 8px 16px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-weight: 600; font-size: 13px; white-space: nowrap;">
                    0 dipilih
                </div>
            </div>
            
            <div id="nasabahList" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <div style="text-align: center; padding: 40px; color: #0066CC;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 16px;">Memuat data...</p>
                </div>
            </div>
            
            <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 2px solid #f0f0f0;">
                <button onclick="closeNasabahModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
                <button onclick="applySelectedNasabah()" style="padding: 10px 20px; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Terapkan Pilihan</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// Toggle Nasabah Form based on Tipe
function toggleNasabahForm() {
    const tipeNasabah = document.getElementById('tipe_nasabah').value;
    const formLama = document.getElementById('form_nasabah_lama');
    const formBaru = document.getElementById('form_nasabah_baru');
    
    if (tipeNasabah === 'lama') {
        // Di Dalam Pipeline - show search form
        formLama.style.display = 'block';
        formBaru.style.display = 'none';
        
        // Enable fields for lama
        document.getElementById('norek').disabled = false;
        document.getElementById('nama_nasabah').disabled = false;
        document.getElementById('btn_search_nasabah').disabled = false;
        
        // Disable fields for baru
        document.getElementById('norek_baru').disabled = true;
        document.getElementById('nama_nasabah_baru').disabled = true;
        
        // Clear baru fields
        document.getElementById('norek_baru').value = '';
        document.getElementById('nama_nasabah_baru').value = '';
        
    } else if (tipeNasabah === 'baru') {
        // Di Luar Pipeline - show manual input form
        formLama.style.display = 'none';
        formBaru.style.display = 'block';
        
        // Disable fields for lama
        document.getElementById('norek').disabled = true;
        document.getElementById('nama_nasabah').disabled = true;
        document.getElementById('btn_search_nasabah').disabled = true;
        
        // Enable fields for baru
        document.getElementById('norek_baru').disabled = false;
        document.getElementById('nama_nasabah_baru').disabled = false;
        
        // Clear lama fields
        document.getElementById('norek').value = '';
        document.getElementById('nama_nasabah').value = '';
        
    } else {
        // No selection - hide both
        formLama.style.display = 'none';
        formBaru.style.display = 'none';
    }
}



// Handle KC Change (Admin only)
function handleKCChange() {
    const kcSelect = document.getElementById('kc_select');
    const selectedOption = kcSelect.options[kcSelect.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('kode_kc').value = selectedOption.value;
        document.getElementById('nama_kc').value = selectedOption.dataset.nama;
        
        // Load uker berdasarkan KC
        loadUkerByKC(selectedOption.value);
        
        // Reset uker selection
        document.getElementById('kode_uker').value = '';
        document.getElementById('nama_uker').value = '';
    }
}

// Load Uker by KC
function loadUkerByKC(kodeKC) {
    const ukerSelect = document.getElementById('uker_select');
    ukerSelect.innerHTML = '<option value="">-- Loading... --</option>';
    ukerSelect.disabled = true;
    
    fetch(`{{ route('api.uker.by-kc') }}?kode_kc=${kodeKC}`)
        .then(response => response.json())
        .then(data => {
            ukerSelect.innerHTML = '<option value="">-- Pilih Unit Kerja --</option>';
            data.forEach(uker => {
                const option = document.createElement('option');
                option.value = uker.kode_sub_kanca;
                option.dataset.nama = uker.sub_kanca;
                option.textContent = uker.sub_kanca;
                ukerSelect.appendChild(option);
            });
            ukerSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading uker:', error);
            ukerSelect.innerHTML = '<option value="">-- Error loading data --</option>';
            ukerSelect.disabled = false;
        });
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

// Handle Strategy Change
function handleStrategyChange() {
    const strategy = document.getElementById('strategy_pipeline').value;
    const kategoriContainer = document.getElementById('kategori_strategi_container');
    const kategoriSelect = document.getElementById('kategori_strategi');
    
    kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
    
    const kategoriMap = {
        'Strategi 1': [
            'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)',
            'PENURUNAN CASA MERCHANT (QRIS & EDC)',
            'PENURUNAN CASA BRILINK',
            'BRILINK SALDO < 10 JUTA',
            'Qlola Non Debitur',
            'Non Debitur Vol Besar CASA Kecil'
        ],
        'Strategi 2': [
            'Qlola (Belum ada Qlola / ada namun nonaktif)',
            'User Aktif Casa Kecil'
        ],
        'Strategi 3': [
            'Optimalisasi Business Cluster'
        ],
        'Strategi 4': [
            'Existing Payroll',
            'Potensi Payroll'
        ],
        'Strategi 6': [
            'List Perusahaan Anak'
        ],
        'Strategi 7': [
            'Penurunan Prioritas Ritel & Mikro',
            'AUM>2M DPK<50 juta',
            'Nasabah Downgrade'
        ],
        'Strategi 8': [
            'Wingback Penguatan Produk & Fungsi RM'
        ],
        'Layering': [
            'Winback'
        ]
    };
    
    if (kategoriMap[strategy]) {
        kategoriContainer.style.display = 'block';
        kategoriMap[strategy].forEach(kategori => {
            const option = document.createElement('option');
            option.value = kategori;
            option.textContent = kategori;
            kategoriSelect.appendChild(option);
        });
    } else {
        kategoriContainer.style.display = 'none';
    }
}

// Modal Functions
let currentPage = 1;
let isLoadingMore = false;
let selectedNasabahList = [];

function openNasabahModal() {
    const kodeKC = document.getElementById('kode_kc').value;
    const kodeUker = document.getElementById('kode_uker').value;
    const strategy = document.getElementById('strategy_pipeline').value;
    const kategori = document.getElementById('kategori_strategi')?.value || '';
    
    if (!kodeKC || !kodeUker) {
        alert('Pilih KC dan Uker terlebih dahulu');
        return;
    }
    
    if (!strategy) {
        alert('Pilih Strategi terlebih dahulu');
        return;
    }
    
    document.getElementById('nasabahModal').style.display = 'flex';
    currentPage = 1;
    loadNasabahList();
}

function closeNasabahModal() {
    document.getElementById('nasabahModal').style.display = 'none';
    document.getElementById('searchNasabah').value = '';
    selectedNasabahList = [];
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = selectedNasabahList.length;
    document.getElementById('selectedCount').textContent = count + ' dipilih';
}

function toggleNasabahSelection(nasabah) {
    const index = selectedNasabahList.findIndex(n => n.norek === nasabah.norek && n.nama === nasabah.nama);
    
    if (index > -1) {
        // Remove from selection
        selectedNasabahList.splice(index, 1);
    } else {
        // Add to selection
        selectedNasabahList.push(nasabah);
    }
    
    updateSelectedCount();
}

function isNasabahSelected(nasabah) {
    return selectedNasabahList.some(n => n.norek === nasabah.norek && n.nama === nasabah.nama);
}

function applySelectedNasabah() {
    if (selectedNasabahList.length === 0) {
        alert('Pilih minimal 1 nasabah');
        return;
    }
    
    // Tampilkan hanya nama nasabah (norek di-hide)
    const namaList = selectedNasabahList.map(n => n.nama).join('\n');
    
    document.getElementById('nama_nasabah').value = namaList;
    
    // Hide norek field - hanya untuk keperluan validasi
    document.getElementById('norek').value = 'hidden';
    
    // Simpan data nasabah ke hidden field untuk diproses di backend
    const nasabahDataInput = document.getElementById('nasabah_data_json');
    if (nasabahDataInput) {
        nasabahDataInput.value = JSON.stringify(selectedNasabahList);
    }
    
    closeNasabahModal();
    
    // Show info berapa nasabah dipilih
    if (selectedNasabahList.length > 1) {
        alert(`${selectedNasabahList.length} nasabah dipilih. Sistem akan membuat ${selectedNasabahList.length} pipeline sekaligus.`);
    }
}

function loadNasabahList(append = false) {
    const kodeKC = document.getElementById('kode_kc').value;
    const kodeUker = document.getElementById('kode_uker').value;
    const strategy = document.getElementById('strategy_pipeline').value;
    const kategori = document.getElementById('kategori_strategi')?.value || '';
    const search = document.getElementById('searchNasabah').value;
    
    if (!append) {
        document.getElementById('nasabahList').innerHTML = '<div style="text-align: center; padding: 40px; color: #0066CC;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 16px;">Memuat data...</p></div>';
    }
    
    let url = `{{ route('api.pipeline.search') }}?search=${encodeURIComponent(search)}&kode_kc=${kodeKC}&kode_uker=${kodeUker}&strategy=${encodeURIComponent(strategy)}&load_all=1&page=${currentPage}`;
    if (kategori) {
        url += `&kategori=${encodeURIComponent(kategori)}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.data && data.data.length > 0) {
                displayNasabahList(data.data, data);
                
                // Add load more button if there are more pages
                if (data.current_page < data.last_page) {
                    const loadMoreBtn = `
                        <div style="text-align: center; padding: 15px;">
                            <button onclick="loadMoreNasabah()" style="padding: 10px 20px; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; border-radius: 6px; cursor: pointer;">
                                Muat Lebih Banyak
                            </button>
                        </div>
                    `;
                    document.getElementById('nasabahList').innerHTML += loadMoreBtn;
                }
            } else {
                document.getElementById('nasabahList').innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">Tidak ada data ditemukan</div>';
            }
            isLoadingMore = false;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('nasabahList').innerHTML = '<div style="text-align: center; padding: 40px; color: #d32f2f;">Error saat memuat data</div>';
            isLoadingMore = false;
        });
}

// Function to display nasabah list based on kategori - TAMPILKAN SEMUA KOLOM
function displayNasabahList(nasabahs, paginationData) {
    const strategy = document.getElementById('strategy_pipeline').value;
    const kategori = document.getElementById('kategori_strategi')?.value || '';
    
    if (nasabahs.length === 0) {
        document.getElementById('nasabahList').innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">Tidak ada data ditemukan</div>';
        return;
    }
    
    let html = '<div style="display: flex; flex-direction: column; height: 100%;">';
    
    // Info header
    html += '<div style="margin-bottom: 10px; padding: 10px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-size: 13px; font-weight: 600; flex-shrink: 0;">';
    html += `<span>Ditemukan ${paginationData.total || nasabahs.length} nasabah`;
    if (paginationData && paginationData.last_page > 1) {
        html += ` - Halaman ${paginationData.current_page} dari ${paginationData.last_page}`;
    }
    html += '</span>';
    html += '</div>';
    
    // Get all column names from first item (exclude hidden columns)
    const firstItem = nasabahs[0];
    const hiddenColumns = ['id', 'created_at', 'updated_at', 'no_rekening', 'norekening', 'nomor_rekening', 'norek'];
    const allColumns = Object.keys(firstItem).filter(col => !hiddenColumns.includes(col));
    
    // Scrollable table container
    html += '<div style="flex: 1; overflow-y: auto; overflow-x: auto; margin-bottom: 10px;">';
    html += '<table style="width: 100%; border-collapse: collapse; min-width: 800px;">';
    html += '<thead><tr style="background: #f5f5f5; border-bottom: 2px solid #ddd; position: sticky; top: 0; z-index: 10;">';
    html += '<th style="padding: 10px; text-align: center; font-size: 13px; width: 50px;">Pilih</th>';
    
    // Display all columns dynamically
    allColumns.forEach(column => {
        const columnName = column.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        html += `<th style="padding: 10px; text-align: left; font-size: 13px; white-space: nowrap;">${columnName}</th>`;
    });
    
    html += '</tr></thead><tbody>';
    
    // Data rows
    nasabahs.forEach(item => {
        const displayName = item.nama_nasabah || item.nama_partner_vendor || item.nama_perusahaan || item.perusahaan || item.nama_agen || item.nama_debitur || '-';
        const displayNorek = item.norek || item.no_rekening || item.cifno || item.corporate_code || '-';
        const displaySegmen = item.segmen_nasabah || item.segmentasi || item.segmentasi_bpr || '-';
        
        const nasabahObj = {
            nama: displayName,
            norek: displayNorek,
            segmen: displaySegmen
        };
        
        const isSelected = isNasabahSelected(nasabahObj);
        
        html += `<tr style="border-bottom: 1px solid #eee; cursor: pointer; background: ${isSelected ? '#e3f2fd' : 'white'};" 
                     onmouseover="if(!${isSelected}) this.style.background='#f5f5f5'" 
                     onmouseout="this.style.background='${isSelected ? '#e3f2fd' : 'white'}'"
                     onclick="toggleNasabahSelection(${JSON.stringify(nasabahObj).replace(/"/g, '&quot;')}); loadNasabahList(false);">`;
        
        html += `<td style="padding: 10px; text-align: center;">
                    <input type="checkbox" ${isSelected ? 'checked' : ''} style="width: 18px; height: 18px; cursor: pointer;" onclick="event.stopPropagation();">
                 </td>`;
        
        // Display all column values dynamically
        allColumns.forEach(column => {
            let value = item[column] || '-';
            
            // Format numeric values
            const numericColumns = ['saldo', 'nominal', 'amount', 'dpk', 'aum', 'saldo_terupdate', 'saldo_last_eom', 'delta', 'savol_bulan_lalu', 'casa_akhir_bulan', 'jumlah_rekening', 'saldo_rekening'];
            if (numericColumns.includes(column) && !isNaN(value) && value !== '-') {
                value = parseFloat(value).toLocaleString('id-ID');
            }
            
            // Bold for name columns
            const nameColumns = ['nama_nasabah', 'nama_partner_vendor', 'nama_perusahaan', 'perusahaan', 'nama_agen', 'nama_debitur', 'nama_usaha_pusat_bisnis', 'nama_merchant'];
            const fontWeight = nameColumns.includes(column) ? '600' : 'normal';
            
            html += `<td style="padding: 10px; font-weight: ${fontWeight};">${value}</td>`;
        });
        
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    html += '</div>'; // Close scrollable container
    html += '</div>'; // Close wrapper
    
    document.getElementById('nasabahList').innerHTML = html;
}

function loadMoreNasabah() {
    if (isLoadingMore) return;
    isLoadingMore = true;
    currentPage++;
    loadNasabahList(true);
}

function searchNasabahList() {
    currentPage = 1;
    loadNasabahList();
}

// Form Validation before submit
function validateForm() {
    const tipeNasabah = document.getElementById('tipe_nasabah').value;
    
    if (!tipeNasabah) {
        alert('Pilih Tipe Nasabah terlebih dahulu');
        return false;
    }
    
    if (tipeNasabah === 'lama') {
        // Validate lama form
        const nama = document.getElementById('nama_nasabah').value;
        
        if (!nama) {
            alert('Silakan pilih nasabah dari modal terlebih dahulu');
            return false;
        }
        
    } else if (tipeNasabah === 'baru') {
        // Validate baru form
        const nama = document.getElementById('nama_nasabah_baru').value;
        
        if (!nama) {
            alert('Nama Nasabah harus diisi');
            return false;
        }
        
        // Copy values from baru fields to main fields
        document.getElementById('norek').value = document.getElementById('norek_baru').value;
        document.getElementById('nama_nasabah').value = nama;
        
        // Enable main fields temporarily for submission
        document.getElementById('norek').disabled = false;
        document.getElementById('nama_nasabah').disabled = false;
    }
    
    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()->isManager())
        // Manager: KC sudah terisi otomatis, uker sudah di-load dari server
        // Tidak perlu load lagi
    @endif
    
    @if(auth()->user()->isAdmin())
        // Admin: Perlu pilih KC dulu
        document.getElementById('uker_select').disabled = true;
    @endif
    
    // Add event listener for tipe nasabah
    document.getElementById('tipe_nasabah').addEventListener('change', toggleNasabahForm);
});

// Form Validation before submit
function validateForm() {
    const tipeNasabah = document.getElementById('tipe_nasabah').value;
    
    if (!tipeNasabah) {
        alert('Pilih Tipe Nasabah terlebih dahulu');
        return false;
    }
    
    if (tipeNasabah === 'lama') {
        // Validate lama form
        const nama = document.getElementById('nama_nasabah').value;
        
        if (!nama) {
            alert('Silakan pilih nasabah dari modal terlebih dahulu');
            return false;
        }
        
    } else if (tipeNasabah === 'baru') {
        // Validate baru form
        const nama = document.getElementById('nama_nasabah_baru').value;
        
        if (!nama) {
            alert('Nama Nasabah harus diisi');
            return false;
        }
        
        // Copy values from baru fields to main fields
        document.getElementById('norek').value = document.getElementById('norek_baru').value;
        document.getElementById('nama_nasabah').value = nama;
        
        // Enable main fields temporarily for submission
        document.getElementById('norek').disabled = false;
        document.getElementById('nama_nasabah').disabled = false;
    }
    
    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()->isManager())
        // Manager: KC sudah terisi otomatis, uker sudah di-load dari server
        // Tidak perlu load lagi
    @endif
    
    @if(auth()->user()->isAdmin())
        // Admin: Perlu pilih KC dulu
        document.getElementById('uker_select').disabled = true;
    @endif
    
    // Add event listener for tipe nasabah
    document.getElementById('tipe_nasabah').addEventListener('change', toggleNasabahForm);
});
</script>


@endsection
