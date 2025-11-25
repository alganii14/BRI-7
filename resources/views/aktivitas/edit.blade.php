@extends('layouts.app')

@section('title', 'Edit Aktivitas')
@section('page-title', 'Edit Aktivitas')

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

    .info-box {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #0066CC;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #666;
    }

    .info-box strong {
        color: #333;
    }
</style>

<div class="card">
    <div class="section-header">
        Edit Aktivitas
    </div>

    <div class="info-box">
        <p><strong>RMFT:</strong> {{ $aktivitas->nama_rmft }}</p>
        <p><strong>PN:</strong> {{ $aktivitas->pn }}</p>
        <p><strong>Kanca:</strong> {{ $aktivitas->nama_kc }}</p>
    </div>

    <!-- Form Unit Kerja -->
    @php
        $currentUker = $aktivitas->nama_uker ?? '';
        $currentUkerUpper = strtoupper($currentUker);
        $isKCP = str_starts_with($currentUkerUpper, 'KCP ');
        $canChangeUker = !$isKCP; // KCP tidak bisa ganti unit
    @endphp
    
    <div class="form-row" style="margin-bottom: 20px;">
        <div class="form-group">
            <label>KODE UKER</label>
            <input type="text" id="kode_uker_display" value="{{ $aktivitas->kode_uker }}" readonly style="background-color: #f5f5f5;">
            <input type="hidden" id="kode_uker" name="kode_uker" value="{{ $aktivitas->kode_uker }}">
        </div>
        
        <div class="form-group">
            <label>NAMA UKER @if($canChangeUker)<span style="color: #0066CC; font-size: 12px;">(Klik untuk ganti)</span>@endif</label>
            @if($canChangeUker)
            <div style="position: relative;">
                <input type="text" id="nama_uker_display" value="{{ $aktivitas->nama_uker }}" readonly 
                       style="background-color: #f0f8ff; cursor: pointer;" 
                       onclick="openUnitModal()" title="Klik untuk memilih unit">
                <input type="hidden" id="nama_uker" name="nama_uker" value="{{ $aktivitas->nama_uker }}">
            </div>
            @else
            <input type="text" id="nama_uker_display" value="{{ $aktivitas->nama_uker }}" readonly style="background-color: #f5f5f5;">
            <input type="hidden" id="nama_uker" name="nama_uker" value="{{ $aktivitas->nama_uker }}">
            <small style="color: #666;">KCP tidak dapat diganti unit kerjanya</small>
            @endif
        </div>
    </div>
    
    <input type="hidden" id="kode_kc" value="{{ $aktivitas->kode_kc }}">
    <input type="hidden" id="nama_kc" value="{{ $aktivitas->nama_kc }}">

    <form action="{{ route('aktivitas.update', $aktivitas->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>TANGGAL <span style="color: red;">*</span></label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $aktivitas->tanggal->format('Y-m-d')) }}" required>
        </div>

        <div class="section-header">
            Data Aktivitas
        </div>

        <div class="form-group">
            <label>STRATEGY PULL OF PIPELINE</label>
            <select name="strategy_pipeline" id="strategy_pipeline">
                <option value="">Pilih Strategy Pull of Pipeline</option>
                <option value="Strategi 1" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 1' ? 'selected' : '' }}>Strategi 1 - Optimalisasi Digital Channel</option>
                <option value="Strategi 2" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 2' ? 'selected' : '' }}>Strategi 2 - Rekening Debitur Transaksi</option>
                <option value="Strategi 3" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 3' ? 'selected' : '' }}>Strategi 3 - Optimalisasi Business Cluster</option>
                <option value="Strategi 4" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 4' ? 'selected' : '' }}>Strategi 4 - Peningkatan Payroll Berkualitas</option>
                <option value="Strategi 6" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 6' ? 'selected' : '' }}>Strategi 6 - Kolaborasi Perusahaan Anak</option>
                <option value="Strategi 7" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 7' ? 'selected' : '' }}>Strategi 7 - Reaktivasi Rekening Dormant & Rekening Tidak Berkualitas</option>
                <option value="Strategi 8" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Strategi 8' ? 'selected' : '' }}>Strategi 8 - Penguatan Produk & Fungsi RM</option>
                <option value="Layering" {{ old('strategy_pipeline', $aktivitas->strategy_pipeline) == 'Layering' ? 'selected' : '' }}>Layering</option>
            </select>
        </div>

        <div class="form-group" id="kategori_group" style="{{ $aktivitas->kategori_strategi ? '' : 'display: none;' }}">
            <label>KATEGORI</label>
            <select name="kategori_strategi" id="kategori_strategi">
                <option value="">Pilih Kategori</option>
            </select>
        </div>

        <div class="form-group">
            <label>RENCANA AKTIVITAS <span style="color: red;">*</span></label>
            <select name="rencana_aktivitas_id" required>
                <option value="">Pilih Rencana Aktivitas</option>
                @foreach($rencanaAktivitas as $item)
                    <option value="{{ $item->id }}" 
                            data-nama="{{ $item->nama_rencana }}"
                            {{ old('rencana_aktivitas_id', $aktivitas->rencana_aktivitas_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_rencana }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="rencana_aktivitas" value="{{ old('rencana_aktivitas', $aktivitas->rencana_aktivitas) }}">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>SEGMEN NASABAH <span style="color: red;">*</span></label>
                <select name="segmen_nasabah" id="segmen_nasabah" required>
                    <option value="">Pilih Segmen</option>
                    <option value="Ritel Badan Usaha" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Ritel Badan Usaha' ? 'selected' : '' }}>Ritel Badan Usaha</option>
                    <option value="SME" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'SME' ? 'selected' : '' }}>SME</option>
                    <option value="Konsumer" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Konsumer' ? 'selected' : '' }}>Konsumer</option>
                    <option value="Prioritas" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Prioritas' ? 'selected' : '' }}>Prioritas</option>
                    <option value="Merchant" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Merchant' ? 'selected' : '' }}>Merchant</option>
                    <option value="Agen Brilink" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Agen Brilink' ? 'selected' : '' }}>Agen Brilink</option>
                    <option value="Mikro" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Mikro' ? 'selected' : '' }}>Mikro</option>
                    <option value="Komersial" {{ old('segmen_nasabah', $aktivitas->segmen_nasabah) == 'Komersial' ? 'selected' : '' }}>Komersial</option>
                </select>
            </div>

            <div class="form-group">
                <label>TIPE PIPELINE <span style="color: red;">*</span></label>
                <select name="tipe_nasabah" id="tipe_nasabah" required onchange="toggleNasabahForm()">
                    <option value="">Pilih Tipe</option>
                    <option value="lama" {{ old('tipe_nasabah', $aktivitas->tipe ?? 'lama') == 'lama' ? 'selected' : '' }}>Di dalam Pipeline</option>
                    <option value="baru" {{ old('tipe_nasabah', $aktivitas->tipe ?? 'lama') == 'baru' ? 'selected' : '' }}>Di luar Pipeline</option>
                </select>
            </div>
        </div>

        <!-- Form untuk Nasabah Lama (dari Pipeline) -->
        <div id="form_nasabah_lama" style="display: {{ old('tipe_nasabah', $aktivitas->tipe ?? 'lama') == 'lama' ? 'block' : 'none' }};">
            <input type="hidden" id="norek" name="norek" value="{{ old('norek', $aktivitas->norek) }}">
            <div class="form-row">
                <div class="form-group">
                    <label>NAMA NASABAH <span style="color: red;">*</span></label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="nama_nasabah" name="nama_nasabah" value="{{ old('nama_nasabah', $aktivitas->nama_nasabah) }}" placeholder="Masukkan nama nasabah" required style="flex: 1;">
                        <button type="button" onclick="openNasabahModal()" style="background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-size: 12px; white-space: nowrap;" title="Cari dari Pipeline">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>RP / JUMLAH <span style="color: red;">*</span></label>
                    <input type="text" id="rp_jumlah" name="rp_jumlah" value="{{ old('rp_jumlah', $aktivitas->rp_jumlah) }}" placeholder="Masukkan jumlah" required>
                </div>
            </div>
        </div>

        <!-- Form untuk Nasabah Baru (di luar Pipeline) -->
        <div id="form_nasabah_baru" style="display: {{ old('tipe_nasabah', $aktivitas->tipe ?? 'lama') == 'baru' ? 'block' : 'none' }};">
            <div class="form-row">
                <div class="form-group">
                    <label>NO. REKENING / CIFNO <span style="color: red;">*</span></label>
                    <input type="text" id="norek_baru" name="norek_baru" value="{{ old('norek_baru', $aktivitas->norek) }}" placeholder="Masukkan nomor rekening">
                </div>

                <div class="form-group">
                    <label>NAMA NASABAH <span style="color: red;">*</span></label>
                    <input type="text" id="nama_nasabah_baru" name="nama_nasabah_baru" value="{{ old('nama_nasabah_baru', $aktivitas->nama_nasabah) }}" placeholder="Masukkan nama nasabah">
                </div>

                <div class="form-group">
                    <label>RP / JUMLAH <span style="color: red;">*</span></label>
                    <input type="text" id="rp_jumlah_baru" name="rp_jumlah_baru" value="{{ old('rp_jumlah_baru', $aktivitas->rp_jumlah) }}" placeholder="Masukkan jumlah">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>KETERANGAN</label>
            <textarea name="keterangan" rows="3">{{ old('keterangan', $aktivitas->keterangan) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" onclick="console.log('Submit button clicked')">Update Aktivitas</button>
            <a href="{{ route('aktivitas.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
    // Debug form submission dan sync field values
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('Form is being submitted');
        
        const tipeNasabah = document.getElementById('tipe_nasabah').value;
        
        // Sync values based on tipe nasabah
        if (tipeNasabah === 'baru') {
            // Copy dari form baru ke form lama (yang akan di-submit)
            document.getElementById('norek').value = document.getElementById('norek_baru').value;
            document.getElementById('nama_nasabah').value = document.getElementById('nama_nasabah_baru').value;
            document.getElementById('rp_jumlah').value = document.getElementById('rp_jumlah_baru').value;
        }
        
        console.log('norek:', document.getElementById('norek').value);
        console.log('nama_nasabah:', document.getElementById('nama_nasabah').value);
        console.log('rp_jumlah:', document.getElementById('rp_jumlah').value);
    });

    // Event listener untuk update hidden field rencana_aktivitas
    document.querySelector('select[name="rencana_aktivitas_id"]').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const namaRencana = selectedOption.getAttribute('data-nama') || selectedOption.text;
        document.querySelector('input[name="rencana_aktivitas"]').value = namaRencana;
    });

    // Mapping kategori berdasarkan strategy
    const kategoriMap = {
        'Strategi 1': [
            'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)',
            'PENURUNAN CASA MERCHANT (QRIS & EDC)',
            'PENURUNAN CASA BRILINK',
            'Qlola Non Debitur',
            'Non Debitur Vol Besar CASA Kecil'
        ],
        'Strategi 2': [
            'Qlola (Belum ada Qlola / ada namun nonaktif)',
            'User Aktif Casa Kecil'
        ],
        'Strategi 3': ['Optimalisasi Business Cluster'],
        'Strategi 4': ['Existing Payroll', 'Potensi Payroll'],
        'Strategi 6': ['List Perusahaan Anak'],
        'Strategi 7': [
            'Penurunan Prioritas Ritel & Mikro',
            'AUM>2M DPK<50 juta'
        ],
        'Strategi 8': ['Wingback Penguatan Produk & Fungsi RM'],
        'Layering': ['Wingback']
    };

    const strategySelect = document.getElementById('strategy_pipeline');
    const kategoriSelect = document.getElementById('kategori_strategi');
    const kategoriGroup = document.getElementById('kategori_group');
    const currentKategori = '{{ old("kategori_strategi", $aktivitas->kategori_strategi) }}';

    // Update kategori saat strategy berubah
    strategySelect.addEventListener('change', function() {
        const strategy = this.value;
        
        // Reset kategori
        kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
        
        if (kategoriMap[strategy]) {
            kategoriGroup.style.display = 'block';
            kategoriMap[strategy].forEach(function(kategori) {
                const option = document.createElement('option');
                option.value = kategori;
                option.textContent = kategori;
                kategoriSelect.appendChild(option);
            });
        } else {
            kategoriGroup.style.display = 'none';
        }
    });

    // Set kategori saat halaman load
    function loadKategoriOptions() {
        if (strategySelect.value) {
            const strategy = strategySelect.value;
            
            // Reset kategori dropdown
            kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
            
            if (kategoriMap[strategy]) {
                kategoriGroup.style.display = 'block';
                kategoriMap[strategy].forEach(function(kategori) {
                    const option = document.createElement('option');
                    option.value = kategori;
                    option.textContent = kategori;
                    // Handle backward compatibility - "Wingback" lama untuk Strategi 8
                    if (kategori === currentKategori || 
                        (strategy === 'Strategi 8' && currentKategori === 'Wingback' && kategori === 'Wingback Penguatan Produk & Fungsi RM')) {
                        option.selected = true;
                    }
                    kategoriSelect.appendChild(option);
                });
            } else {
                kategoriGroup.style.display = 'none';
            }
        }
    }
    
    // Load kategori saat halaman pertama kali dibuka
    loadKategoriOptions();

    // Toggle between Nasabah Lama and Nasabah Baru forms
    function toggleNasabahForm() {
        const tipeNasabah = document.getElementById('tipe_nasabah').value;
        const formLama = document.getElementById('form_nasabah_lama');
        const formBaru = document.getElementById('form_nasabah_baru');
        
        if (tipeNasabah === 'lama') {
            formLama.style.display = 'block';
            formBaru.style.display = 'none';
        } else if (tipeNasabah === 'baru') {
            formLama.style.display = 'none';
            formBaru.style.display = 'block';
        } else {
            formLama.style.display = 'none';
            formBaru.style.display = 'none';
        }
    }

    // Open Nasabah Modal
    let allNasabahData = [];
    let filteredNasabahData = [];
    
    function openNasabahModal() {
        const strategy = document.getElementById('strategy_pipeline').value;
        const kategoriSelect = document.getElementById('kategori_strategi');
        const kategori = kategoriSelect ? kategoriSelect.value : '';
        
        if (!strategy) {
            alert('Pilih Strategy Pull of Pipeline terlebih dahulu');
            return;
        }
        
        // Strategi yang tidak memerlukan kategori (langsung bisa cari)
        const strategiTanpaKategori = ['Strategi 8', 'Layering'];
        
        // Jika strategi memerlukan kategori tapi belum dipilih
        if (!strategiTanpaKategori.includes(strategy) && !kategori) {
            alert('Pilih Kategori terlebih dahulu');
            return;
        }
        
        document.getElementById('nasabahModal').style.display = 'flex';
        loadNasabahData();
    }

    function closeNasabahModal() {
        document.getElementById('nasabahModal').style.display = 'none';
    }

    function loadNasabahData() {
        const strategy = document.getElementById('strategy_pipeline').value;
        const kategoriSelect = document.getElementById('kategori_strategi');
        let kategori = kategoriSelect ? kategoriSelect.value : '';
        // Gunakan kode_kc dari aktivitas, atau dari user jika kosong
        const kodeKC = '{{ $aktivitas->kode_kc ?? auth()->user()->kode_kanca }}';
        const kodeUker = '{{ $aktivitas->kode_uker ?? '' }}';
        const filterMonth = document.getElementById('filterMonth')?.value || '';
        const filterYear = document.getElementById('filterYear')?.value || '';
        
        // Untuk strategi yang tidak punya kategori dropdown, gunakan default kategori
        if (strategy === 'Strategi 8' && !kategori) {
            kategori = 'Wingback Penguatan Produk & Fungsi RM';
        } else if (strategy === 'Layering' && !kategori) {
            kategori = 'Wingback';
        }
        
        // Populate filter tahun jika belum ada
        populateYearFilter();
        
        const nasabahList = document.getElementById('nasabahList');
        nasabahList.innerHTML = '<div style="text-align: center; padding: 40px; color: #0066CC;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 16px;">Memuat data...</p></div>';
        
        // Build URL with filters
        let url = `/api/nasabah?strategy=${encodeURIComponent(strategy)}&kategori=${encodeURIComponent(kategori)}&kode_kc=${encodeURIComponent(kodeKC)}&load_all=1`;
        if (kodeUker) {
            url += `&kode_uker=${encodeURIComponent(kodeUker)}`;
        }
        if (filterYear) {
            url += `&year=${filterYear}`;
        }
        if (filterMonth) {
            url += `&month=${filterMonth}`;
        }
        
        console.log('Fetching URL:', url);
        console.log('Strategy:', strategy, 'Kategori:', kategori, 'Kode KC:', kodeKC, 'Year:', filterYear, 'Month:', filterMonth);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Data received:', result);
                // Handle both array and object with data property
                const dataArray = Array.isArray(result) ? result : (result.data || []);
                allNasabahData = dataArray;
                filteredNasabahData = dataArray;
                renderNasabahList(dataArray, result);
            })
            .catch(error => {
                console.error('Error details:', error);
                nasabahList.innerHTML = `<div style="text-align: center; padding: 40px; color: #dc3545;"><p>Terjadi kesalahan saat memuat data</p><p style="font-size: 12px; margin-top: 10px;">${error.message}</p></div>`;
            });
    }
    
    // Function to populate year filter
    function populateYearFilter() {
        const filterYear = document.getElementById('filterYear');
        if (filterYear && filterYear.options.length <= 1) {
            const currentYear = new Date().getFullYear();
            for (let year = currentYear; year >= currentYear - 5; year--) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                filterYear.appendChild(option);
            }
        }
    }
    
    // Function to apply filter changes (month/year)
    function applyPipelineFilter() {
        loadNasabahData();
    }

    function renderNasabahList(data, response = null) {
        const nasabahList = document.getElementById('nasabahList');
        
        if (data.length === 0) {
            nasabahList.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;"><p>Tidak ada data nasabah</p></div>';
            return;
        }
        
        const strategy = document.getElementById('strategy_pipeline').value;
        const kategoriSelect = document.getElementById('kategori_strategi');
        const kategori = kategoriSelect ? kategoriSelect.value : '';
        
        // Info header - jumlah data ditemukan
        const totalData = response?.total || data.length;
        let infoHtml = `<div style="margin-bottom: 10px; padding: 10px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-size: 13px; font-weight: 600;">`;
        infoHtml += `<span>Ditemukan ${totalData} nasabah</span>`;
        infoHtml += `</div>`;
        
        // Deteksi jenis strategi - SEMUA STRATEGI
        const isMerchantSavol = kategori === 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)';
        const isQlolaNonDebitur = kategori === 'Qlola Non Debitur';
        const isQlolaNonaktif = kategori === 'Qlola (Belum ada Qlola / ada namun nonaktif)';
        const isPotensiPayroll = kategori === 'Potensi Payroll';
        const isExistingPayroll = kategori === 'Existing Payroll';
        const isPerusahaanAnak = kategori === 'List Perusahaan Anak';
        const isPenurunanBrilink = kategori === 'PENURUNAN CASA BRILINK';
        const isPenurunanMerchant = kategori === 'PENURUNAN CASA MERCHANT (QRIS & EDC)';
        const isNonDebiturVolBesar = kategori === 'Non Debitur Vol Besar CASA Kecil';
        const isUserAktifCasaKecil = kategori === 'User Aktif Casa Kecil';
        const isPenurunanPrioritasRitelMikro = kategori === 'PENURUNAN PRIORITAS RITEL MIKRO' || kategori === 'Penurunan Prioritas Ritel & Mikro';
        const isAumDpk = kategori === 'AUM>2M DPK<50 juta';
        const isStrategi8 = kategori === 'Wingback Penguatan Produk & Fungsi RM' || strategy === 'Strategi 8';
        const isLayering = kategori === 'Wingback' || strategy === 'Layering';
        const isOptimalisasiBusinessCluster = kategori === 'Optimalisasi Business Cluster' || strategy === 'Strategi 3';
        
        let html = '<table style="width: 100%; border-collapse: collapse; font-size: 13px;"><thead><tr style="background: #f8f9fa; position: sticky; top: 0; z-index: 1;">';
        
        // Build header berdasarkan strategi - LENGKAP SEMUA STRATEGI
        if (isMerchantSavol) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Jenis Merchant</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">TID / Store ID</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Merchant</th>';
            @if(auth()->user()->isAdmin())
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">No. Rekening</th>';
            @endif
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIF</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Savol Bulan Lalu</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">CASA Akhir Bulan</th>';
        } else if (isPenurunanMerchant || isPenurunanBrilink) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Terupdate</th>';
        } else if (isQlolaNonDebitur) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px; border-bottom: 2px solid #dee2e6;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Segmentasi</th>';
        } else if (isNonDebiturVolBesar) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px; border-bottom: 2px solid #dee2e6;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo</th>';
        } else if (isQlolaNonaktif) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Debitur</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Plafon</th>';
        } else if (isUserAktifCasaKecil) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px; border-bottom: 2px solid #dee2e6;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Bulan Berjalan</th>';
        } else if (isPenurunanPrioritasRitelMikro) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px; border-bottom: 2px solid #dee2e6;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            @if(auth()->user()->isAdmin())
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">No. Rekening</th>';
            @endif
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Segmentasi BPR</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Delta</th>';
        } else if (isAumDpk) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIF</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">AUM</th>';
        } else if (isStrategi8 || isLayering) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px; border-bottom: 2px solid #dee2e6;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            @if(auth()->user()->isAdmin())
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">No. Rekening</th>';
            @endif
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Segmentasi</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Delta</th>';
        } else if (isOptimalisasiBusinessCluster) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px; border-bottom: 2px solid #dee2e6;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Tag Zona Unggulan</th>';
            @if(auth()->user()->isAdmin())
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Nomor Rekening</th>';
            @endif
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Usaha/Pusat Bisnis</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Nama Tenaga Pemasar</th>';
        } else if (isPotensiPayroll) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Perusahaan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Estimasi Pekerja</th>';
        } else if (isExistingPayroll) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Perusahaan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">Jumlah Rekening</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo Rekening</th>';
        } else if (isPerusahaanAnak) {
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Partner/Vendor</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Nama PIC</th>';
        } else {
            // Default columns untuk strategi lainnya
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px; border-bottom: 2px solid #dee2e6;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px; border-bottom: 2px solid #dee2e6;">CIFNO</th>';
            @if(auth()->user()->isAdmin())
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">No. Rekening</th>';
            @endif
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px; border-bottom: 2px solid #dee2e6;">Saldo</th>';
        }
        
        html += '<th style="padding: 10px; text-align: center; font-size: 13px; min-width: 80px; border-bottom: 2px solid #dee2e6;">Aksi</th>';
        html += '</tr></thead><tbody>';
        
        data.forEach((nasabah, index) => {
            html += '<tr style="border-bottom: 1px solid #f0f0f0;" onmouseover="this.style.background=\'#f8f9fa\'" onmouseout="this.style.background=\'white\'">';
            
            // Build body berdasarkan strategi - LENGKAP SEMUA STRATEGI
            if (isMerchantSavol) {
                html += `<td style="padding: 10px;">${nasabah.jenis_merchant || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.tid_storeid || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_merchant || '-'}</td>`;
                @if(auth()->user()->isAdmin())
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.norekening || '-'}</td>`;
                @endif
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cif || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.savol_bulan_lalu ? parseFloat(nasabah.savol_bulan_lalu).toLocaleString('id-ID') : '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.casa_akhir_bulan ? parseFloat(nasabah.casa_akhir_bulan).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isPenurunanMerchant || isPenurunanBrilink) {
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_last_eom ? parseFloat(nasabah.saldo_last_eom).toLocaleString('id-ID') : '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_terupdate ? parseFloat(nasabah.saldo_terupdate).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isQlolaNonDebitur) {
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi || '-'}</td>`;
            } else if (isNonDebiturVolBesar) {
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo ? parseFloat(nasabah.saldo).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isQlolaNonaktif) {
                html += `<td style="padding: 10px;">${nasabah.kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_debitur || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.plafon ? parseFloat(nasabah.plafon).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isUserAktifCasaKecil) {
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_bulan_berjalan ? parseFloat(nasabah.saldo_bulan_berjalan).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isPenurunanPrioritasRitelMikro) {
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                @if(auth()->user()->isAdmin())
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.no_rekening || '-'}</td>`;
                @endif
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi_bpr || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_last_eom ? parseFloat(nasabah.saldo_last_eom).toLocaleString('id-ID') : '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_terupdate ? parseFloat(nasabah.saldo_terupdate).toLocaleString('id-ID') : '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.delta ? parseFloat(nasabah.delta).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isAumDpk) {
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cif || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.aum ? parseFloat(nasabah.aum).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isStrategi8 || isLayering) {
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                @if(auth()->user()->isAdmin())
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.no_rekening || '-'}</td>`;
                @endif
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_last_eom ? parseFloat(nasabah.saldo_last_eom).toLocaleString('id-ID') : '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_terupdate ? parseFloat(nasabah.saldo_terupdate).toLocaleString('id-ID') : '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.delta ? parseFloat(nasabah.delta).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isOptimalisasiBusinessCluster) {
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.tag_zona_unggulan || '-'}</td>`;
                @if(auth()->user()->isAdmin())
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.no_rekening || nasabah.norek || '-'}</td>`;
                @endif
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.nama_tenaga_pemasar || '-'}</td>`;
            } else if (isPotensiPayroll) {
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.perusahaan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.estimasi_pekerja || '-'}</td>`;
            } else if (isExistingPayroll) {
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_perusahaan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.jumlah_rekening || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right;">Rp ${nasabah.saldo_rekening ? parseFloat(nasabah.saldo_rekening).toLocaleString('id-ID') : '0'}</td>`;
            } else if (isPerusahaanAnak) {
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_partner_vendor || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk_terdekat || nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.nama_pic_partner || nasabah.nama_pic || '-'}</td>`;
            } else {
                // Default display
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cifno || nasabah.norek || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.no_rekening || nasabah.norek || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.rp_jumlah || nasabah.saldo_terupdate || '-'}</td>`;
            }
            
            html += `<td style="padding: 10px; text-align: center;"><button type="button" onclick="selectNasabahByIndex(${index})" style="background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; padding: 6px 16px; border-radius: 4px; cursor: pointer; font-size: 12px;">Pilih</button></td>`;
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        nasabahList.innerHTML = infoHtml + html;
    }

    function searchNasabahList() {
        const searchValue = document.getElementById('searchNasabah').value.toLowerCase();
        
        if (searchValue === '') {
            filteredNasabahData = allNasabahData;
        } else {
            filteredNasabahData = allNasabahData.filter(nasabah => {
                return (nasabah.nama_nasabah && nasabah.nama_nasabah.toLowerCase().includes(searchValue)) ||
                       (nasabah.norek && nasabah.norek.toLowerCase().includes(searchValue));
            });
        }
        
        renderNasabahList(filteredNasabahData);
    }

    function selectNasabahByIndex(index) {
        const nasabah = filteredNasabahData[index];
        if (nasabah) {
            const kategoriSelect = document.getElementById('kategori_strategi');
            const kategori = kategoriSelect ? kategoriSelect.value : '';
            const isPerusahaanAnak = kategori === 'List Perusahaan Anak';
            const isPotensiPayroll = kategori === 'Potensi Payroll';
            const isExistingPayroll = kategori === 'Existing Payroll';
            const isQlolaNonaktif = kategori === 'Qlola (Belum ada Qlola / ada namun nonaktif)';
            
            // Set norek berdasarkan kategori
            if (isPerusahaanAnak) {
                document.getElementById('norek').value = '-';
            } else if (isPotensiPayroll) {
                document.getElementById('norek').value = nasabah.perusahaan || '';
            } else if (isExistingPayroll) {
                document.getElementById('norek').value = nasabah.cifno || '';
            } else {
                document.getElementById('norek').value = nasabah.norek || nasabah.cifno || '-';
            }
            
            // Set nama nasabah berdasarkan kategori
            if (isPerusahaanAnak) {
                document.getElementById('nama_nasabah').value = nasabah.nama_partner_vendor || '';
            } else if (isPotensiPayroll) {
                document.getElementById('nama_nasabah').value = nasabah.perusahaan || '';
            } else if (isExistingPayroll) {
                document.getElementById('nama_nasabah').value = nasabah.nama_perusahaan || nasabah.nama_nasabah || '';
            } else if (isQlolaNonaktif) {
                document.getElementById('nama_nasabah').value = nasabah.nama_debitur || nasabah.nama_nasabah || '';
            } else {
                document.getElementById('nama_nasabah').value = nasabah.nama_nasabah || nasabah.nama_merchant || '';
            }
            
            // Set rp_jumlah - biarkan kosong untuk user input
            document.getElementById('rp_jumlah').value = nasabah.rp_jumlah || '';
        }
        closeNasabahModal();
    }
    
    function selectNasabah(nasabah) {
        const kategoriSelect = document.getElementById('kategori_strategi');
        const kategori = kategoriSelect ? kategoriSelect.value : '';
        const isPerusahaanAnak = kategori === 'List Perusahaan Anak';
        const isPotensiPayroll = kategori === 'Potensi Payroll';
        const isExistingPayroll = kategori === 'Existing Payroll';
        const isQlolaNonaktif = kategori === 'Qlola (Belum ada Qlola / ada namun nonaktif)';
        
        // Set norek berdasarkan kategori
        if (isPerusahaanAnak) {
            document.getElementById('norek').value = '-';
        } else if (isPotensiPayroll) {
            document.getElementById('norek').value = nasabah.perusahaan || '';
        } else if (isExistingPayroll) {
            document.getElementById('norek').value = nasabah.cifno || '';
        } else {
            document.getElementById('norek').value = nasabah.norek || nasabah.cifno || '-';
        }
        
        // Set nama nasabah berdasarkan kategori
        if (isPerusahaanAnak) {
            document.getElementById('nama_nasabah').value = nasabah.nama_partner_vendor || '';
        } else if (isPotensiPayroll) {
            document.getElementById('nama_nasabah').value = nasabah.perusahaan || '';
        } else if (isExistingPayroll) {
            document.getElementById('nama_nasabah').value = nasabah.nama_perusahaan || nasabah.nama_nasabah || '';
        } else if (isQlolaNonaktif) {
            document.getElementById('nama_nasabah').value = nasabah.nama_debitur || nasabah.nama_nasabah || '';
        } else {
            document.getElementById('nama_nasabah').value = nasabah.nama_nasabah || nasabah.nama_merchant || '';
        }
        
        // Set rp_jumlah - biarkan kosong untuk user input
        document.getElementById('rp_jumlah').value = nasabah.rp_jumlah || '';
        
        closeNasabahModal();
    }
</script>

<!-- Modal Nasabah -->
<div id="nasabahModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 95%; max-width: 1400px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); flex-shrink: 0;">
            <h3 style="margin: 0; color: white;">Pilih Nasabah dari Pull of Pipeline</h3>
            <button onclick="closeNasabahModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px;">&times;</button>
        </div>
        
        <div style="padding: 20px; display: flex; flex-direction: column; overflow: hidden; flex: 1;">
            <!-- Filter Bulan dan Tahun -->
            <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-shrink: 0;">
                <select id="filterYear" style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background: white;" onchange="applyPipelineFilter()">
                    <option value="">Semua Tahun</option>
                    <!-- Tahun akan di-load secara dinamis -->
                </select>
                
                <select id="filterMonth" style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background: white;" onchange="applyPipelineFilter()">
                    <option value="">Semua Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            
            <input type="text" id="searchNasabah" placeholder="Cari nasabah berdasarkan nama, CIFNO, atau nomor rekening..." style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; flex-shrink: 0;" onkeyup="searchNasabahList()">
            
            <div id="nasabahList" style="flex: 1; overflow-y: auto; overflow-x: auto;">
                <div style="text-align: center; padding: 40px; color: #0066CC;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 16px;">Memuat data...</p>
                </div>
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

<!-- Modal Unit Selection -->
<div id="unitModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); flex-shrink: 0;">
            <h3 style="margin: 0; color: white;">Pilih Unit di <span id="modal_kc_name">{{ $aktivitas->nama_kc }}</span></h3>
            <button onclick="closeUnitModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px;">&times;</button>
        </div>
        
        <div style="padding: 20px; flex: 1; overflow-y: auto; display: flex; flex-direction: column;">
            <div style="margin-bottom: 15px; flex-shrink: 0;">
                <input type="text" id="searchUnit" placeholder="Cari nama unit..." style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;" onkeyup="filterUnitList()">
            </div>
            
            <div id="selected_count" style="margin-bottom: 10px; padding: 8px 12px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-size: 13px; font-weight: 600; flex-shrink: 0;">
                <span id="count_text">Unit saat ini: {{ $aktivitas->nama_uker }}</span>
            </div>
            
            <div id="unitList" style="flex: 1; overflow-y: auto; border: 1px solid #ddd; border-radius: 6px; padding: 10px; min-height: 200px; max-height: 350px;">
                <div style="text-align: center; padding: 40px; color: #666;">
                    <p>Memuat daftar unit...</p>
                </div>
            </div>
            
            <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end; flex-shrink: 0;">
                <button onclick="closeUnitModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
                <button onclick="applySelectedUnit()" style="padding: 10px 20px; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Terapkan Pilihan</button>
            </div>
        </div>
    </div>
</div>

<script>
// Unit Modal Functions
let allUnits = [];
let selectedUnit = null;

function openUnitModal() {
    const kodeKc = document.getElementById('kode_kc').value;
    const namaKc = document.getElementById('nama_kc').value;
    
    if (!kodeKc) {
        alert('Kode KC tidak tersedia');
        return;
    }
    
    document.getElementById('modal_kc_name').textContent = namaKc;
    document.getElementById('unitModal').style.display = 'flex';
    
    // Load units dari KC ini
    loadUnitsForKC(kodeKc);
}

function closeUnitModal() {
    document.getElementById('unitModal').style.display = 'none';
    document.getElementById('searchUnit').value = '';
}

function loadUnitsForKC(kodeKc) {
    document.getElementById('unitList').innerHTML = `
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Memuat daftar unit...</p>
        </div>
    `;
    
    fetch(`{{ route('api.uker.by-kc') }}?kode_kc=${kodeKc}`)
        .then(response => response.json())
        .then(units => {
            allUnits = units;
            displayUnits(units);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('unitList').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #d32f2f;">
                    <p>Terjadi kesalahan saat memuat data unit</p>
                </div>
            `;
        });
}

function displayUnits(units) {
    const currentUkerName = document.getElementById('nama_uker').value;
    
    if (units.length === 0) {
        document.getElementById('unitList').innerHTML = `
            <div style="text-align: center; padding: 40px; color: #666;">
                <p>Tidak ada unit ditemukan untuk KC ini</p>
            </div>
        `;
        return;
    }
    
    let html = '<div style="display: flex; flex-direction: column; gap: 4px;">';
    
    units.forEach(unit => {
        const isSelected = unit.sub_kanca === currentUkerName;
        if (isSelected) {
            selectedUnit = unit;
        }
        
        html += `
            <label style="padding: 12px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s; background: ${isSelected ? '#e3f2fd' : 'white'};"
                   onmouseenter="this.style.backgroundColor='#f0f8ff';"
                   onmouseleave="this.style.backgroundColor='${isSelected ? '#e3f2fd' : 'white'}';">
                <input type="radio" 
                       name="unit_selection"
                       value="${unit.kode_sub_kanca}" 
                       data-nama="${unit.sub_kanca}"
                       data-id="${unit.id}"
                       ${isSelected ? 'checked' : ''}
                       onchange="selectUnit(this)"
                       style="width: 18px; height: 18px; cursor: pointer;">
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #333;">${unit.sub_kanca}</div>
                    <div style="font-size: 12px; color: #666; margin-top: 2px;">Kode: ${unit.kode_sub_kanca}</div>
                </div>
            </label>
        `;
    });
    
    html += '</div>';
    document.getElementById('unitList').innerHTML = html;
}

function selectUnit(radio) {
    selectedUnit = {
        id: radio.dataset.id,
        kode_sub_kanca: radio.value,
        sub_kanca: radio.dataset.nama
    };
    document.getElementById('count_text').textContent = `Unit dipilih: ${selectedUnit.sub_kanca}`;
}

function filterUnitList() {
    const searchValue = document.getElementById('searchUnit').value.toLowerCase();
    const filteredUnits = allUnits.filter(unit => 
        unit.sub_kanca.toLowerCase().includes(searchValue) ||
        unit.kode_sub_kanca.toLowerCase().includes(searchValue)
    );
    displayUnits(filteredUnits);
}

function applySelectedUnit() {
    if (!selectedUnit) {
        alert('Harap pilih unit terlebih dahulu');
        return;
    }
    
    document.getElementById('nama_uker_display').value = selectedUnit.sub_kanca;
    document.getElementById('nama_uker').value = selectedUnit.sub_kanca;
    document.getElementById('kode_uker_display').value = selectedUnit.kode_sub_kanca;
    document.getElementById('kode_uker').value = selectedUnit.kode_sub_kanca;
    
    closeUnitModal();
    
    // Visual feedback
    const displayField = document.getElementById('nama_uker_display');
    displayField.style.borderColor = '#28a745';
    setTimeout(() => {
        displayField.style.borderColor = '#ddd';
    }, 1500);
}

// Close modal when clicking outside
document.getElementById('unitModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUnitModal();
    }
});
</script>

@endsection
