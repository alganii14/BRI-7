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
       Data RMFT 
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

    <form action="{{ route('aktivitas.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf

        @if(auth()->user()->isManager() || auth()->user()->isAdmin())
        <!-- Manager dan Admin memilih RMFT -->
        <div class="form-group">
            <label>PILIH RMFT <span style="color: red;">*</span></label>
            @if(auth()->user()->isManager())
                <small style="display: block; color: #666; margin-bottom: 5px;">
                    KC Anda: {{ auth()->user()->nama_kanca ?? auth()->user()->kode_kanca ?? 'Tidak tersedia' }} 
                    @if($rmftList->isEmpty())
                        <span style="color: #d32f2f; font-weight: 600;">⚠️ Tidak ada RMFT ditemukan di KC Anda</span>
                    @else
                        <span style="color: #2e7d32;">✓ {{ $rmftList->count() }} RMFT tersedia</span>
                    @endif
                </small>
            @endif
            <select name="rmft_select" id="rmft_select" required onchange="fillRMFTData(this.value)">
                <option value="">-- Pilih RMFT --</option>
                @foreach($rmftList as $rmft)
                    @php
                        $rmftRecord = $rmft->rmftData;
                        $ukerRecord = null;
                        $kodeUkerValue = '';
                        
                        if ($rmftRecord) {
                            // Cari uker berdasarkan nama uker yang EXACT dari field 'uker' di rmfts
                            if ($rmftRecord->uker) {
                                $ukerRecord = \App\Models\Uker::where('sub_kanca', $rmftRecord->uker)->first();
                            }
                            
                            // Jika tidak ketemu, coba cari berdasarkan kanca
                            if (!$ukerRecord && $rmftRecord->kanca) {
                                $ukerRecord = \App\Models\Uker::where('kanca', $rmftRecord->kanca)->first();
                            }
                            
                            // Fallback ke relasi jika ada
                            if (!$ukerRecord) {
                                $ukerRecord = $rmftRecord->ukerRelation;
                            }
                            
                            // Set kode uker - prioritaskan dari uker yang ketemu berdasarkan nama
                            if ($ukerRecord) {
                                $kodeUkerValue = $ukerRecord->kode_sub_kanca;
                            }
                        }
                    @endphp
                <option value="{{ $rmft->id }}" 
                        data-rmft-id="{{ $rmftRecord ? $rmftRecord->id : '' }}"
                        data-name="{{ $rmft->name }}"
                        data-pernr="{{ $rmft->pernr }}"
                        data-kode-kc="{{ $ukerRecord ? $ukerRecord->kode_kanca : '' }}"
                        data-kanca="{{ $rmftRecord ? $rmftRecord->kanca : '' }}"
                        data-kode-uker="{{ $kodeUkerValue }}"
                        data-uker="{{ $rmftRecord ? $rmftRecord->uker : '' }}"
                        data-kelompok="{{ $rmftRecord ? $rmftRecord->kelompok_jabatan : '' }}">
                    {{ $rmft->name }} ({{ $rmft->pernr }}) - {{ $rmftRecord ? $rmftRecord->kanca : 'N/A' }}
                </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="rmft_id" id="rmft_id_input" required>
        @elseif(auth()->user()->isRMFT())
        <!-- RMFT otomatis terisi -->
        <input type="hidden" name="rmft_id" value="{{ optional($rmftData)->id }}">
        @endif

        <div class="form-row">
            <div class="form-group">
                <label>NO</label>
                <input type="text" value="Auto" disabled>
            </div>

            <div class="form-group">
                <label>TANGGAL DANA MASUK <span style="color: red;">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label>NAMA RMFT</label>
                <input type="text" id="nama_rmft" name="nama_rmft" value="{{ old('nama_rmft', optional($rmftData)->completename ?? '') }}" readonly required>
            </div>

            <div class="form-group">
                <label>PN</label>
                <input type="text" id="pn" name="pn" value="{{ old('pn', optional($rmftData)->pernr ?? '') }}" readonly required>
            </div>

            <div class="form-group">
                <label>KODE KC</label>
                @php
                    $kodeKcValue = old('kode_kc');
                    if (!$kodeKcValue && $rmftData) {
                        // Cari uker berdasarkan nama uker yang EXACT
                        $ukerData = null;
                        if ($rmftData->uker) {
                            $ukerData = \App\Models\Uker::where('sub_kanca', $rmftData->uker)->first();
                        }
                        // Fallback ke kanca jika tidak ketemu
                        if (!$ukerData && $rmftData->kanca) {
                            $ukerData = \App\Models\Uker::where('kanca', $rmftData->kanca)->first();
                        }
                        $kodeKcValue = $ukerData ? $ukerData->kode_kanca : '';
                    }
                @endphp
                <input type="text" id="kode_kc" name="kode_kc" value="{{ $kodeKcValue }}" readonly required>
            </div>

            <div class="form-group">
                <label>NAMA KC</label>
                <input type="text" id="nama_kc" name="nama_kc" value="{{ old('nama_kc', optional($rmftData)->kanca ?? '') }}" readonly required>
            </div>

            <div class="form-group">
                <label>KODE UKER</label>
                @php
                    $kodeUkerValue = old('kode_uker');
                    if (!$kodeUkerValue && $rmftData) {
                        // Cari uker berdasarkan nama uker yang EXACT
                        $ukerData = null;
                        if ($rmftData->uker) {
                            $ukerData = \App\Models\Uker::where('sub_kanca', $rmftData->uker)->first();
                        }
                        // Fallback ke kanca jika tidak ketemu
                        if (!$ukerData && $rmftData->kanca) {
                            $ukerData = \App\Models\Uker::where('kanca', $rmftData->kanca)->first();
                        }
                        $kodeUkerValue = $ukerData ? $ukerData->kode_sub_kanca : '';
                    }
                @endphp
                <textarea id="kode_uker_display" readonly style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; min-height: 60px; resize: vertical; background-color: #f5f5f5; font-family: inherit;" placeholder="Kode unit yang dipilih akan muncul di sini">{{ $kodeUkerValue }}</textarea>
                <input type="hidden" id="kode_uker" name="kode_uker" value="{{ $kodeUkerValue }}" required>
            </div>

            <div class="form-group" id="nama_uker_group">
                <label>NAMA UKER <span id="unit_selector_label" style="color: #0066CC; display: none;">(Klik untuk pilih unit)</span></label>
                <div style="position: relative;">
                    <textarea id="nama_uker_display" readonly style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; min-height: 60px; resize: vertical; background-color: #f5f5f5; font-family: inherit;" placeholder="Unit yang dipilih akan muncul di sini">{{ old('nama_uker', optional($rmftData)->uker ?? '') }}</textarea>
                    <input type="hidden" id="nama_uker" name="nama_uker" value="{{ old('nama_uker', optional($rmftData)->uker ?? '') }}" required>
                    <input type="hidden" id="kode_uker_list" name="kode_uker_list" value="">
                    <input type="hidden" id="nama_uker_list" name="nama_uker_list" value="">
                </div>
                <input type="hidden" id="is_unit_rmft" value="0">
                <input type="hidden" id="rmft_kode_kc" value="">
            </div>

            <div class="form-group">
                <label>KELOMPOK</label>
                <input type="text" id="kelompok" name="kelompok" value="{{ old('kelompok', optional($rmftData)->kelompok_jabatan ?? '') }}" readonly required>
            </div>
        </div>

        <div class="section-header">
            Data Aktivitas
        </div>

        <div class="form-group">
            <label>STRATEGY PULL OF PIPELINE <span style="color: red;">*</span></label>
            <select name="strategy_pipeline" id="strategy_pipeline" required onchange="handleStrategyChange()" disabled>
                <option value="">Pilih RMFT terlebih dahulu</option>
                <option value="Strategi 1">Strategi 1 - Optimalisasi Digital Channel</option>
                <option value="Strategi 2">Strategi 2 - Rekening Debitur Transaksi</option>
                <option value="Strategi 3">Strategi 3 - Optimalisasi Business Cluster</option>
                <option value="Strategi 4">Strategi 4 - Peningkatan Payroll Berkualitas</option>
                <option value="Strategi 6">Strategi 6 - Kolaborasi Perusahaan Anak</option>
                <option value="Strategi 7">Strategi 7 - Reaktivasi Rekening Dormant & Rekening Tidak Berkualitas</option>
                <option value="Strategi 8">Strategi 8 - Penguatan Produk & Fungsi RM</option>
                <option value="Layering">Layering</option>
            </select>
        </div>

        <div class="form-group" id="kategori_strategi_container" style="display: none;">
            <label>KATEGORI <span style="color: red;">*</span></label>
            <select name="kategori_strategi" id="kategori_strategi" disabled>
                <option value="">Pilih Kategori</option>
            </select>
        </div>

        <div class="form-group">
            <label>RENCANA AKTIVITAS <span style="color: red;">*</span></label>
            <select name="rencana_aktivitas_id" id="rencana_aktivitas" required disabled>
                <option value="">Pilih Rencana Aktivitas</option>
                @foreach($rencanaAktivitas as $item)
                    <option value="{{ $item->id }}" 
                            data-nama="{{ $item->nama_rencana }}"
                            {{ old('rencana_aktivitas_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_rencana }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="rencana_aktivitas" id="rencana_aktivitas_text" value="{{ old('rencana_aktivitas') }}">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>SEGMEN NASABAH <span style="color: red;">*</span></label>
                <select name="segmen_nasabah" id="segmen_nasabah" required disabled>
                    <option value="">Pilih RMFT terlebih dahulu</option>
                    <option value="Ritel Badan Usaha" {{ old('segmen_nasabah') == 'Ritel Badan Usaha' ? 'selected' : '' }}>Ritel Badan Usaha</option>
                    <option value="SME" {{ old('segmen_nasabah') == 'SME' ? 'selected' : '' }}>SME</option>
                    <option value="Konsumer" {{ old('segmen_nasabah') == 'Konsumer' ? 'selected' : '' }}>Konsumer</option>
                    <option value="Prioritas" {{ old('segmen_nasabah') == 'Prioritas' ? 'selected' : '' }}>Prioritas</option>
                    <option value="Merchant" {{ old('segmen_nasabah') == 'Merchant' ? 'selected' : '' }}>Merchant</option>
                    <option value="Agen Brilink" {{ old('segmen_nasabah') == 'Agen Brilink' ? 'selected' : '' }}>Agen Brilink</option>
                    <option value="Mikro" {{ old('segmen_nasabah') == 'Mikro' ? 'selected' : '' }}>Mikro</option>
                    <option value="Komersial" {{ old('segmen_nasabah') == 'Komersial' ? 'selected' : '' }}>Komersial</option>
                </select>
            </div>

            <div class="form-group" id="tipe_nasabah_container">
                <label>TIPE PIPELINE <span style="color: red;">*</span></label>
                <select name="tipe_nasabah" id="tipe_nasabah" required disabled onchange="toggleNasabahForm()">
                    <option value="">Pilih RMFT terlebih dahulu</option>
                    <option value="lama" {{ old('tipe_nasabah') == 'lama' ? 'selected' : '' }}>Di dalam Pipeline</option>
                    <option value="baru" {{ old('tipe_nasabah') == 'baru' ? 'selected' : '' }}>Di luar Pipeline</option>
                </select>
            </div>
        </div>

        <!-- Form untuk Nasabah Lama -->
        <div id="form_nasabah_lama" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label id="label_norek">CARI NASABAH <span style="color: red;">*</span></label>
                    <div style="position: relative;">
                        <input type="text" id="norek" name="norek" value="{{ old('norek') }}" placeholder="Pilih RMFT terlebih dahulu" autocomplete="off" style="padding-right: 45px;" disabled>
                        <button type="button" id="btn_search_nasabah" onclick="openNasabahModal()" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;" disabled>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>NAMA NASABAH <span style="color: red;">*</span></label>
                    <input type="text" id="nama_nasabah" name="nama_nasabah" value="{{ old('nama_nasabah') }}" placeholder="Pilih RMFT terlebih dahulu" disabled>
                </div>

                <div class="form-group">
                    <label>RP / JUMLAH <span style="color: red;">*</span></label>
                    <input type="text" id="rp_jumlah" name="rp_jumlah" value="{{ old('rp_jumlah') }}" placeholder="Pilih RMFT terlebih dahulu" disabled>
                </div>
            </div>
        </div>

        <!-- Form untuk Nasabah Baru -->
        <div id="form_nasabah_baru" style="display: none;">
            <div class="form-row">
                <div class="form-group">
                    <label>NO. REKENING <span style="color: red;">*</span></label>
                    <input type="text" id="norek_baru" name="norek_baru" value="{{ old('norek_baru') }}" placeholder="Masukkan nomor rekening" disabled>
                </div>

                <div class="form-group">
                    <label>NAMA NASABAH <span style="color: red;">*</span></label>
                    <input type="text" id="nama_nasabah_baru" name="nama_nasabah_baru" value="{{ old('nama_nasabah_baru') }}" placeholder="Masukkan nama nasabah" disabled>
                </div>

                <div class="form-group">
                    <label>RP / JUMLAH <span style="color: red;">*</span></label>
                    <input type="text" id="rp_jumlah_baru" name="rp_jumlah_baru" value="{{ old('rp_jumlah_baru') }}" placeholder="Masukkan jumlah" disabled>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Aktivitas</button>
            <a href="{{ route('aktivitas.index') }}" class="btn btn-secondary">Batal</a>
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
            
            <div id="nasabahList" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <div style="text-align: center; padding: 40px; color: #0066CC;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 16px;">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Unit Selection -->
<div id="unitModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 600px; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); flex-shrink: 0;">
            <h3 style="margin: 0; color: white;">Pilih Unit di <span id="modal_kc_name"></span></h3>
            <button onclick="closeUnitModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px;">&times;</button>
        </div>
        
        <div style="padding: 20px; flex: 1; overflow-y: auto; display: flex; flex-direction: column;">
            <div style="margin-bottom: 15px; flex-shrink: 0;">
                <input type="text" id="searchUnit" placeholder="Cari nama unit..." style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;" onkeyup="filterUnitList()">
            </div>
            
            <div id="selected_count" style="margin-bottom: 10px; padding: 8px 12px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-size: 13px; font-weight: 600; flex-shrink: 0;">
                <span id="count_text">Belum ada unit dipilih</span>
            </div>
            
            <div id="unitList" style="flex: 1; overflow-y: auto; border: 1px solid #ddd; border-radius: 6px; padding: 10px; min-height: 200px; max-height: 350px;">
                <div style="text-align: center; padding: 40px; color: #666;">
                    <p>Memuat daftar unit...</p>
                </div>
            </div>
            
            <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end; flex-shrink: 0;">
                <button onclick="closeUnitModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
                <button onclick="applySelectedUnits()" style="padding: 10px 20px; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Terapkan Pilihan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Kategori per strategi sesuai dengan dropdown Pull of Pipeline
    const kategoriPerStrategi = {
        'Strategi 1': ['MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)', 'PENURUNAN CASA BRILINK', 'PENURUNAN CASA MERCHANT (QRIS & EDC)', 'Qlola Non Debitur', 'Non Debitur Vol Besar CASA Kecil'],
        'Strategi 2': ['Qlola (Belum ada Qlola / ada namun nonaktif)', 'User Aktif Casa Kecil'],
        'Strategi 3': ['Optimalisasi Business Cluster'],
        'Strategi 4': ['Existing Payroll', 'Potensi Payroll'],
        'Strategi 6': ['List Perusahaan Anak'],
        'Strategi 7': ['Penurunan Prioritas Ritel & Mikro', 'AUM>2M DPK<50 juta'],
        'Strategi 8': ['Wingback Penguatan Produk & Fungsi RM'],
        'Layering': ['Wingback']
    };

    // Handle strategy pipeline change
    function handleStrategyChange() {
        const strategy = document.getElementById('strategy_pipeline').value;
        const kategoriContainer = document.getElementById('kategori_strategi_container');
        const kategoriSelect = document.getElementById('kategori_strategi');
        
        if (strategy && kategoriPerStrategi[strategy]) {
            // Tampilkan field kategori
            kategoriContainer.style.display = 'block';
            kategoriSelect.disabled = false;
            kategoriSelect.required = true;
            
            // Populate kategori options
            kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
            kategoriPerStrategi[strategy].forEach(kategori => {
                const option = document.createElement('option');
                option.value = kategori;
                option.textContent = kategori;
                kategoriSelect.appendChild(option);
            });
            
            // Visual feedback
            const strategySelect = document.getElementById('strategy_pipeline');
            strategySelect.style.borderColor = '#28a745';
            setTimeout(() => {
                strategySelect.style.borderColor = '#ddd';
            }, 1000);
        } else if (strategy === 'Optimalisasi Business Cluster') {
            // Sembunyikan field kategori untuk strategy lama
            kategoriContainer.style.display = 'none';
            kategoriSelect.disabled = true;
            kategoriSelect.required = false;
            kategoriSelect.value = '';
        } else {
            // Sembunyikan field kategori jika tidak ada strategy
            kategoriContainer.style.display = 'none';
            kategoriSelect.disabled = true;
            kategoriSelect.required = false;
            kategoriSelect.value = '';
        }
        
        // Update label dan placeholder
        updateNorekLabelAndPlaceholder();
    }
    
    // Function untuk update label dan placeholder CIFNO berdasarkan kategori
    function updateNorekLabelAndPlaceholder() {
        const kategori = document.getElementById('kategori_strategi')?.value || '';
        const norekInput = document.getElementById('norek');
        const norekLabel = document.querySelector('label[for="norek"]') || document.querySelector('#form_nasabah_lama label:first-child');
        
        if (kategori === 'Potensi Payroll') {
            norekInput.placeholder = 'Nama Perusahaan';
            if (norekLabel) {
                norekLabel.innerHTML = 'NAMA PERUSAHAAN <span style="color: red;">*</span>';
            }
        } else if (kategori === 'Existing Payroll') {
            norekInput.placeholder = 'Corporate Code';
            if (norekLabel) {
                norekLabel.innerHTML = 'CORPORATE CODE <span style="color: red;">*</span>';
            }
        } else {
            norekInput.placeholder = 'Cari nasabah';
            if (norekLabel) {
                norekLabel.innerHTML = 'CARI NASABAH <span style="color: red;">*</span>';
            }
        }
    }
    
    // Event listener untuk perubahan kategori
    document.addEventListener('DOMContentLoaded', function() {
        const kategoriSelect = document.getElementById('kategori_strategi');
        if (kategoriSelect) {
            kategoriSelect.addEventListener('change', updateNorekLabelAndPlaceholder);
        }
    });

    // Function to fill RMFT data when Manager selects RMFT
    function fillRMFTData(rmftUserId) {
        const select = document.getElementById('rmft_select');
        const option = select.options[select.selectedIndex];
        
        if (!option.value) {
            // Clear all fields
            document.getElementById('rmft_id_input').value = '';
            document.getElementById('nama_rmft').value = '';
            document.getElementById('pn').value = '';
            document.getElementById('kode_kc').value = '';
            document.getElementById('nama_kc').value = '';
            document.getElementById('kode_uker').value = '';
            document.getElementById('kode_uker_display').value = '';
            document.getElementById('nama_uker').value = '';
            document.getElementById('nama_uker_display').value = '';
            document.getElementById('kelompok').value = '';
            document.getElementById('is_unit_rmft').value = '0';
            document.getElementById('rmft_kode_kc').value = '';
            
            // Clear list fields
            document.getElementById('kode_uker_list').value = '';
            document.getElementById('nama_uker_list').value = '';
            selectedUnits = [];
            
            // Disable Data Aktivitas fields
            disableAktivitasFields();
            return;
        }
        
        // Fill form with selected RMFT data
        document.getElementById('rmft_id_input').value = option.dataset.rmftId;
        document.getElementById('nama_rmft').value = option.dataset.name;
        document.getElementById('pn').value = option.dataset.pernr;
        document.getElementById('kode_kc').value = option.dataset.kodeKc;
        document.getElementById('nama_kc').value = option.dataset.kanca;
        document.getElementById('kode_uker').value = option.dataset.kodeUker;
        document.getElementById('kode_uker_display').value = option.dataset.kodeUker;
        
        // Simpan kode KC untuk filter unit
        document.getElementById('rmft_kode_kc').value = option.dataset.kodeKc;
        
        // Set default value
        document.getElementById('nama_uker').value = option.dataset.uker;
        document.getElementById('nama_uker_display').value = option.dataset.uker;
        document.getElementById('kelompok').value = option.dataset.kelompok;
        
        // Cek apakah RMFT ini bisa ganti unit
        // Hanya KC/KK/Unit yang bisa ganti, KCP tidak bisa
        const ukerName = option.dataset.uker.toUpperCase();
        const canSelectUnit = ukerName.includes('UNIT') || ukerName.startsWith('KC ') || ukerName.startsWith('KK ');
        
        if (canSelectUnit) {
            // Enable modal untuk pilih unit
            document.getElementById('unit_selector_label').style.display = 'inline';
            document.getElementById('nama_uker_display').style.cursor = 'pointer';
            document.getElementById('nama_uker_display').style.backgroundColor = '#f0f8ff';
            document.getElementById('nama_uker_display').title = 'Klik untuk memilih unit di KC ini';
            document.getElementById('nama_uker_display').onclick = openUnitModal;
            document.getElementById('kode_uker_display').style.cursor = 'pointer';
            document.getElementById('kode_uker_display').style.backgroundColor = '#f0f8ff';
            document.getElementById('kode_uker_display').title = 'Klik untuk memilih unit di KC ini';
            document.getElementById('kode_uker_display').onclick = openUnitModal;
            
            // Set is_unit_rmft jika mengandung UNIT
            if (ukerName.includes('UNIT')) {
                document.getElementById('is_unit_rmft').value = '1';
            } else {
                document.getElementById('is_unit_rmft').value = '0';
            }
            
            // Reset selections
            selectedUnits = [{
                kode_sub_kanca: option.dataset.kodeUker,
                sub_kanca: option.dataset.uker
            }];
        } else {
            // KCP atau lainnya - readonly, tidak bisa ganti
            document.getElementById('is_unit_rmft').value = '0';
            document.getElementById('unit_selector_label').style.display = 'none';
            document.getElementById('nama_uker_display').style.cursor = 'default';
            document.getElementById('nama_uker_display').style.backgroundColor = '#f5f5f5';
            document.getElementById('nama_uker_display').title = '';
            document.getElementById('nama_uker_display').onclick = null;
            document.getElementById('kode_uker_display').style.cursor = 'default';
            document.getElementById('kode_uker_display').style.backgroundColor = '#f5f5f5';
            document.getElementById('kode_uker_display').title = '';
            document.getElementById('kode_uker_display').onclick = null;
            
            // Reset selections
            selectedUnits = [];
        }
        
        // Clear list fields
        document.getElementById('kode_uker_list').value = '';
        document.getElementById('nama_uker_list').value = '';
        
        // Enable Data Aktivitas fields
        enableAktivitasFields();
    }
    
    // Function to disable Data Aktivitas fields
    function disableAktivitasFields() {
        document.getElementById('strategy_pipeline').disabled = true;
        document.getElementById('strategy_pipeline').innerHTML = '<option value="">Pilih RMFT terlebih dahulu</option>';
        document.getElementById('rencana_aktivitas').disabled = true;
        document.getElementById('rencana_aktivitas').innerHTML = '<option value="">Pilih RMFT terlebih dahulu</option>';
        document.getElementById('segmen_nasabah').disabled = true;
        document.getElementById('segmen_nasabah').innerHTML = '<option value="">Pilih RMFT terlebih dahulu</option>';
        document.getElementById('tipe_nasabah').disabled = true;
        document.getElementById('tipe_nasabah').innerHTML = '<option value="">Pilih RMFT terlebih dahulu</option>';
        document.getElementById('norek').disabled = true;
        document.getElementById('norek').placeholder = 'Pilih RMFT terlebih dahulu';
        document.getElementById('btn_search_nasabah').disabled = true;
        document.getElementById('nama_nasabah').disabled = true;
        document.getElementById('nama_nasabah').placeholder = 'Pilih RMFT terlebih dahulu';
        document.getElementById('rp_jumlah').disabled = true;
        document.getElementById('rp_jumlah').placeholder = 'Pilih RMFT terlebih dahulu';
        document.getElementById('norek_baru').disabled = true;
        document.getElementById('norek_baru').placeholder = 'Pilih RMFT terlebih dahulu';
        document.getElementById('nama_nasabah_baru').disabled = true;
        document.getElementById('nama_nasabah_baru').placeholder = 'Pilih RMFT terlebih dahulu';
        document.getElementById('rp_jumlah_baru').disabled = true;
        document.getElementById('rp_jumlah_baru').placeholder = 'Pilih RMFT terlebih dahulu';
    }
    
    // Function to enable Data Aktivitas fields
    function enableAktivitasFields() {
        // Enable Strategy Pipeline
        document.getElementById('strategy_pipeline').disabled = false;
        document.getElementById('strategy_pipeline').innerHTML = `
            <option value="">Pilih Strategy Pull of Pipeline</option>
            <option value="Strategi 1">Strategi 1 - Optimalisasi Digital Channel</option>
            <option value="Strategi 2">Strategi 2 - Rekening Debitur Transaksi</option>
            <option value="Strategi 3">Strategi 3 - Optimalisasi Business Cluster</option>
            <option value="Strategi 4">Strategi 4 - Peningkatan Payroll Berkualitas</option>
            <option value="Strategi 6">Strategi 6 - Kolaborasi Perusahaan Anak</option>
            <option value="Strategi 7">Strategi 7 - Reaktivasi Rekening Dormant & Rekening Tidak Berkualitas</option>
            <option value="Strategi 8">Strategi 8 - Penguatan Produk & Fungsi RM</option>
            <option value="Layering">Layering</option>
        `;
        
        // Enable Rencana Aktivitas
        document.getElementById('rencana_aktivitas').disabled = false;
        document.getElementById('rencana_aktivitas').innerHTML = `
            <option value="">Pilih Rencana Aktivitas</option>
            @foreach($rencanaAktivitas as $item)
            <option value="{{ $item->id }}" data-nama="{{ $item->nama_rencana }}">{{ $item->nama_rencana }}</option>
            @endforeach
        `;
        
        // Enable Segmen Nasabah
        document.getElementById('segmen_nasabah').disabled = false;
        document.getElementById('segmen_nasabah').innerHTML = `
            <option value="">Pilih Segmen</option>
            <option value="Ritel Badan Usaha">Ritel Badan Usaha</option>
            <option value="SME">SME</option>
            <option value="Konsumer">Konsumer</option>
            <option value="Prioritas">Prioritas</option>
            <option value="Merchant">Merchant</option>
            <option value="Agen Brilink">Agen Brilink</option>
            <option value="Mikro">Mikro</option>
            <option value="Komersial">Komersial</option>
        `;
        
        // Enable Tipe Pipeline
        document.getElementById('tipe_nasabah').disabled = false;
        document.getElementById('tipe_nasabah').innerHTML = `
            <option value="">Pilih Tipe</option>
            <option value="lama">Di dalam Pipeline</option>
            <option value="baru">Di luar Pipeline</option>
        `;
        
        // Enable Nasabah Lama fields (default disabled)
        document.getElementById('norek').disabled = false;
        document.getElementById('btn_search_nasabah').disabled = false;
        document.getElementById('nama_nasabah').disabled = false;
        document.getElementById('nama_nasabah').placeholder = 'Nama lengkap nasabah';
        document.getElementById('rp_jumlah').disabled = false;
        document.getElementById('rp_jumlah').placeholder = 'Contoh: 10000000';
        
        // Update placeholder dan label berdasarkan kategori
        updateNorekLabelAndPlaceholder();
        
        // Enable Nasabah Baru fields (default disabled)
        document.getElementById('norek_baru').disabled = false;
        document.getElementById('norek_baru').placeholder = 'Masukkan nomor rekening';
        document.getElementById('nama_nasabah_baru').disabled = false;
        document.getElementById('nama_nasabah_baru').placeholder = 'Masukkan nama nasabah';
        document.getElementById('rp_jumlah_baru').disabled = false;
        document.getElementById('rp_jumlah_baru').placeholder = 'Masukkan jumlah';
    }
    
    // Function to toggle between Nasabah Baru and Nasabah Lama forms
    function toggleNasabahForm() {
        const tipeNasabah = document.getElementById('tipe_nasabah').value;
        const formLama = document.getElementById('form_nasabah_lama');
        const formBaru = document.getElementById('form_nasabah_baru');
        
        if (tipeNasabah === 'lama') {
            formLama.style.display = 'block';
            formBaru.style.display = 'none';
            // Clear Nasabah Baru fields
            document.getElementById('norek_baru').value = '';
            document.getElementById('nama_nasabah_baru').value = '';
            document.getElementById('rp_jumlah_baru').value = '';
        } else if (tipeNasabah === 'baru') {
            formLama.style.display = 'none';
            formBaru.style.display = 'block';
            // Clear Nasabah Lama fields
            document.getElementById('norek').value = '';
            document.getElementById('nama_nasabah').value = '';
            document.getElementById('rp_jumlah').value = '';
        } else {
            formLama.style.display = 'none';
            formBaru.style.display = 'none';
        }
    }

    // Autocomplete for Norek - DISABLED, now using Pipeline Search
    const norekInput = document.getElementById('norek');
    const namaNasabahInput = document.getElementById('nama_nasabah');
    const segmenNasababSelect = document.querySelector('select[name="segmen_nasabah"]');
    
    // Disable autocomplete on direct typing
    /*
    let debounceTimer;
    
    norekInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const norek = this.value;
        
        if (norek.length < 3) {
            return;
        }
        
        debounceTimer = setTimeout(() => {
            // Get KC and Unit from form
            const kodeKc = document.getElementById('kode_kc').value;
            const isUnitRmft = document.getElementById('is_unit_rmft').value;
            
            // Jika multiple units dipilih, gunakan semua unit
            let kodeUker = '';
            if (isUnitRmft === '1' && selectedUnits.length > 0) {
                // Gunakan semua unit yang dipilih
                kodeUker = selectedUnits.map(u => u.kode_sub_kanca).join(',');
            } else {
                // Gunakan single unit
                kodeUker = document.getElementById('kode_uker').value;
            }
            
            // Check if KC and Unit are filled
            if (!kodeKc || !kodeUker) {
                alert('Harap pilih RMFT terlebih dahulu untuk menentukan KC dan Unit');
                norekInput.value = '';
                return;
            }
            
            fetch(`{{ route('api.nasabah.get') }}?norek=${norek}&kode_kc=${kodeKc}&kode_uker=${kodeUker}`)
                .then(response => response.json())
                .then(data => {
                    if (data.found) {
                        // Fill form with nasabah data
                        namaNasabahInput.value = data.data.nama_nasabah;
                        segmenNasabahSelect.value = data.data.segmen_nasabah;
                        
                        // Visual feedback
                        norekInput.style.borderColor = '#4caf50';
                        setTimeout(() => {
                            norekInput.style.borderColor = '#ddd';
                        }, 1000);
                    } else {
                        // Show info that norek is new for this KC/Unit
                        norekInput.style.borderColor = '#ff9800';
                        setTimeout(() => {
                            norekInput.style.borderColor = '#ddd';
                        }, 1000);
                    `;
                });
        }, 300);
    });
    */
    
    // Modal Functions
    function openNasabahModal() {
        // Validasi strategy harus dipilih terlebih dahulu
        const strategy = document.getElementById('strategy_pipeline').value;
        if (!strategy) {
            alert('Harap pilih Strategy Pull of Pipeline terlebih dahulu');
            document.getElementById('strategy_pipeline').focus();
            return;
        }
        
        // Validasi kategori untuk strategi yang memerlukan kategori spesifik
        const kategoriStrategyList = ['Strategi 1', 'Strategi 2', 'Strategi 4', 'Strategi 6'];
        if (kategoriStrategyList.includes(strategy)) {
            const kategori = document.getElementById('kategori_strategi')?.value;
            if (!kategori) {
                alert('Harap pilih Kategori Strategi terlebih dahulu');
                document.getElementById('kategori_strategi').focus();
                return;
            }
        }
        
        const modal = document.getElementById('nasabahModal');
        modal.style.display = 'flex';
        
        // Load available years untuk strategy ini
        loadAvailableYears(strategy);
        
        // Langsung load semua data tanpa perlu ketik
        loadAllNasabahFromPipeline();
    }
    
    // Function to load available years from database
    function loadAvailableYears(strategy) {
        const kategori = document.getElementById('kategori_strategi')?.value || '';
        let url = `{{ route('api.pipeline.years') }}?strategy=${encodeURIComponent(strategy)}`;
        if (kategori) {
            url += `&kategori=${encodeURIComponent(kategori)}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(years => {
                const yearSelect = document.getElementById('filterYear');
                
                // Clear existing options except "Semua Tahun"
                yearSelect.innerHTML = '<option value="">Semua Tahun</option>';
                
                // Add years from database
                years.forEach(year => {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading years:', error);
            });
    }
    
    // Variable to track pagination state
    let currentPage = 1;
    let totalPages = 1;
    let currentStrategy = '';
    let currentKodeKc = '';
    let currentKodeUker = '';
    
    // Function to load all nasabah from pipeline based on KC and Unit
    function loadAllNasabahFromPipeline(page = 1) {
        const kodeKc = document.getElementById('kode_kc').value;
        const isUnitRmft = document.getElementById('is_unit_rmft').value;
        const strategy = document.getElementById('strategy_pipeline').value;
        const kategori = document.getElementById('kategori_strategi').value;
        const filterMonth = document.getElementById('filterMonth').value;
        const filterYear = document.getElementById('filterYear').value;
        
        // Save current state
        currentPage = page;
        currentStrategy = strategy;
        currentKodeKc = kodeKc;
        
        // Jika multiple units dipilih, gunakan semua unit
        let kodeUkerParam = '';
        if (isUnitRmft === '1' && selectedUnits.length > 0) {
            kodeUkerParam = selectedUnits.map(u => u.kode_sub_kanca).join(',');
        } else {
            kodeUkerParam = document.getElementById('kode_uker').value;
        }
        
        currentKodeUker = kodeUkerParam;
        
        // Build filter text
        let filterText = strategy;
        if (filterYear) {
            filterText += ` - Tahun ${filterYear}`;
        }
        if (filterMonth) {
            const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            filterText += ` - ${monthNames[parseInt(filterMonth)]}`;
        }
        
        document.getElementById('nasabahList').innerHTML = `
            <div style="text-align: center; padding: 40px; color: #0066CC;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 16px;">Memuat data nasabah dari ${filterText}...</p>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        
        // Build URL with filters
        let url = `{{ route('api.pipeline.search') }}?search=&kode_kc=${kodeKc}&kode_uker=${kodeUkerParam}&strategy=${encodeURIComponent(strategy)}&load_all=1&page=${page}`;
        if (kategori) {
            url += `&kategori=${encodeURIComponent(kategori)}`;
        }
        if (filterYear) {
            url += `&year=${filterYear}`;
        }
        if (filterMonth) {
            url += `&month=${filterMonth}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(response => {
                // Handle paginated response
                const nasabahs = response.data || [];
                totalPages = response.last_page || 1;
                
                if (nasabahs.length === 0) {
                    document.getElementById('nasabahList').innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #666;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Tidak ada nasabah ditemukan di ${strategy}</p>
                            <small style="color: #999;">Untuk KC: ${document.getElementById('nama_kc').value}</small>
                        </div>
                    `;
                    return;
                }
                
                displayNasabahList(nasabahs, response);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('nasabahList').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #d32f2f;">
                        <p>Terjadi kesalahan saat memuat data</p>
                    </div>
                `;
            });
    }
    
    // Function to display nasabah list
    function displayNasabahList(nasabahs, paginationData) {
        // Create wrapper with table container and fixed pagination at bottom
        let html = '<div style="display: flex; flex-direction: column; height: 100%;">';
        
        // Info header
        html += '<div style="margin-bottom: 10px; padding: 10px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-size: 13px; font-weight: 600; flex-shrink: 0;">';
        html += `<span>Ditemukan ${paginationData.total || nasabahs.length} nasabah`;
        if (paginationData && paginationData.last_page > 1) {
            html += ` - Halaman ${paginationData.current_page} dari ${paginationData.last_page}`;
        }
        html += '</span>';
        html += '</div>';
        
        // Scrollable table container
        html += '<div style="flex: 1; overflow-y: auto; overflow-x: auto; margin-bottom: 10px;">';
        html += '<table style="width: 100%; border-collapse: collapse; min-width: 600px;">';
        html += '<thead><tr style="background: #f5f5f5; border-bottom: 2px solid #ddd; position: sticky; top: 0; z-index: 10;">';
        
        // Cek strategi untuk menentukan kolom yang ditampilkan
        const strategy = document.getElementById('strategy_pipeline').value;
        const kategori = document.getElementById('kategori_strategi').value;
        const isOptimalisasiBC = strategy === 'Optimalisasi Business Cluster';
        const isExistingPayroll = kategori === 'Existing Payroll';
        const isStrategiLain = ['Strategi 1', 'Strategi 2', 'Strategi 3', 'Strategi 4', 'Strategi 6', 'Strategi 7', 'Strategi 8'].includes(strategy);
        const showDelta = false; // Removed Penurunan Brilink
        
        const isPotensiPayroll = kategori === 'Potensi Payroll';
        const isPerusahaanAnak = kategori === 'List Perusahaan Anak';
        const isQlolaNonaktif = kategori === 'Qlola (Belum ada Qlola / ada namun nonaktif)';
        const isMerchantSavol = kategori === 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)';
        const isPenurunanMerchant = kategori === 'PENURUNAN CASA MERCHANT (QRIS & EDC)';
        const isPenurunanBrilink = kategori === 'PENURUNAN CASA BRILINK';
        const isQlolaNonDebitur = kategori === 'Qlola Non Debitur';
        const isNonDebiturVolBesar = kategori === 'Non Debitur Vol Besar CASA Kecil';
        const isUserAktifCasaKecil = kategori === 'User Aktif Casa Kecil';
        const isPenurunanPrioritasRitelMikro = kategori === 'Penurunan Prioritas Ritel & Mikro';
        const isAumDpk = kategori === 'AUM>2M DPK<50 juta';
        const isStrategi8 = kategori === 'Wingback Penguatan Produk & Fungsi RM';
        const isLayeringWingback = kategori === 'Wingback';
        const isOptimalisasiBusinessCluster = kategori === 'Optimalisasi Business Cluster';
        
        if (isMerchantSavol) {
            // Kolom khusus untuk Merchant Savol Besar Casa Kecil (Qris & EDC)
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Jenis Merchant</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">TID / Store ID</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Merchant</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIF</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Savol Bulan Lalu</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">CASA Akhir Bulan</th>';
        } else if (isPenurunanMerchant) {
            // Kolom khusus untuk Penurunan CASA Merchant (QRIS & EDC)
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Segmentasi</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta</th>';
        } else if (isPenurunanBrilink) {
            // Kolom khusus untuk Penurunan CASA Brilink
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta</th>';
        } else if (isQlolaNonDebitur) {
            // Kolom khusus untuk Qlola Non Debitur
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Segmentasi</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Cek QCash</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Cek CMS</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Cek IB</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Keterangan</th>';
        } else if (isNonDebiturVolBesar) {
            // Kolom khusus untuk Non Debitur Vol Besar CASA Kecil
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Segmentasi</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">VOL QCASH</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">VOL QIB</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo</th>';
        } else if (isQlolaNonaktif) {
            // Kolom khusus untuk Qlola (Belum ada Qlola / ada namun nonaktif)
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Pinjaman</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Simpanan</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Debitur</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Plafon</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">PN Pengelola</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Keterangan</th>';
        } else if (isUserAktifCasaKecil) {
            // Kolom khusus untuk User Aktif Casa Kecil
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Kanca</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Norek Pinjaman</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Bulan Lalu</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Bulan Berjalan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta Saldo</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Nama RM Pemrakarsa</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">QCash</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">QIB</th>';
        } else if (isPenurunanPrioritasRitelMikro) {
            // Kolom khusus untuk Penurunan Prioritas Ritel & Mikro
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Segmentasi BPR</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta</th>';
        } else if (isAumDpk) {
            // Kolom khusus untuk AUM>2M DPK<50 juta
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">SLP</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">PBO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIF</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">ID Prioritas</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Nomor Rekening</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">AUM</th>';
        } else if (isStrategi8) {
            // Kolom khusus untuk Wingback Penguatan Produk & Fungsi RM
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Segmentasi</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta</th>';
        } else if (isLayeringWingback) {
            // Kolom khusus untuk Layering Wingback
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">CIFNO</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">No. Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Nasabah</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Segmentasi</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Jenis Simpanan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Last EOM</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Terupdate</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta</th>';
        } else if (isOptimalisasiBusinessCluster) {
            // Kolom khusus untuk Optimalisasi Business Cluster
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Uker</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit Kerja</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Tag Zona Unggulan</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Nomor Rekening</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Usaha Pusat Bisnis</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Nama Tenaga Pemasar</th>';
        } else if (isPerusahaanAnak) {
            // Kolom khusus untuk List Perusahaan Anak
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Partner/Vendor</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Jenis Usaha</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Alamat</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Nama PIC</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">HP PIC</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Perusahaan Anak</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Status Pipeline</th>';
        } else if (isExistingPayroll) {
            // Kolom khusus untuk Existing Payroll
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">Corporate Code</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Nama Perusahaan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Jumlah Rekening</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 150px;">Saldo Rekening</th>';
        } else if (isPotensiPayroll) {
            // Kolom khusus untuk Potensi Payroll
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">Kode Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Cabang Induk</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 200px;">Perusahaan</th>';
            html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Estimasi Pekerja</th>';
        } else {
            // Kolom untuk strategi lainnya
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 100px;">CIFNO</th>';
            
            // Tambahkan kolom No. Rekening untuk BC atau strategi lain
            if (isOptimalisasiBC || isStrategiLain) {
                html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 120px;">No. Rekening</th>';
            }
            
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Nama</th>';
            html += '<th style="padding: 10px; text-align: left; font-size: 13px; min-width: 150px;">Unit</th>';
            
            // Delta column removed (was for Penurunan Brilink)
            if (showDelta) {
                html += '<th style="padding: 10px; text-align: right; font-size: 13px; min-width: 120px;">Delta</th>';
            }
        }
        
        html += '<th style="padding: 10px; text-align: center; font-size: 13px; min-width: 80px;">Aksi</th>';
        html += '</tr></thead><tbody>';
        
        nasabahs.forEach(nasabah => {
            // Ambil nilai delta langsung dari backend
            let deltaValue = 0;
            
            if (nasabah.delta !== undefined && nasabah.delta !== null) {
                // Gunakan nilai delta jika tersedia
                if (typeof nasabah.delta === 'number') {
                    deltaValue = nasabah.delta;
                } else if (typeof nasabah.delta === 'string') {
                    deltaValue = parseInt(nasabah.delta.replace(/[.,]/g, ''));
                }
            } else {
                // Fallback ke saldo_terupdate jika delta tidak ada
                if (typeof nasabah.saldo_terupdate === 'number') {
                    deltaValue = nasabah.saldo_terupdate;
                } else if (typeof nasabah.saldo_terupdate === 'string') {
                    deltaValue = parseInt(nasabah.saldo_terupdate.replace(/[.,]/g, ''));
                } else if (nasabah.saldo_last_eom) {
                    if (typeof nasabah.saldo_last_eom === 'number') {
                        deltaValue = nasabah.saldo_last_eom;
                    } else {
                        deltaValue = parseInt(nasabah.saldo_last_eom.replace(/[.,]/g, ''));
                    }
                }
            }
            
            // Pastikan deltaValue adalah number yang valid
            if (isNaN(deltaValue)) {
                deltaValue = 0;
            }
            
            // Tentukan strategy untuk menampilkan kolom yang sesuai
            const strategy = document.getElementById('strategy_pipeline').value;
            const kategori = document.getElementById('kategori_strategi').value;
            const isOptimalisasiBC = strategy === 'Optimalisasi Business Cluster';
            const isExistingPayroll = kategori === 'Existing Payroll';
            const isPotensiPayroll = kategori === 'Potensi Payroll';
            const isPerusahaanAnak = kategori === 'List Perusahaan Anak';
            const isQlolaNonaktif = kategori === 'Qlola (Belum ada Qlola / ada namun nonaktif)';
            const isMerchantSavol = kategori === 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)';
            const isPenurunanMerchant = kategori === 'PENURUNAN CASA MERCHANT (QRIS & EDC)';
            const isPenurunanBrilink = kategori === 'PENURUNAN CASA BRILINK';
            const isQlolaNonDebitur = kategori === 'Qlola Non Debitur';
            const isNonDebiturVolBesar = kategori === 'Non Debitur Vol Besar CASA Kecil';
            const isUserAktifCasaKecil = kategori === 'User Aktif Casa Kecil';
            const isPenurunanPrioritasRitelMikro = kategori === 'Penurunan Prioritas Ritel & Mikro';
            const isAumDpk = kategori === 'AUM>2M DPK<50 juta';
            const isStrategi8 = kategori === 'Wingback Penguatan Produk & Fungsi RM';
            const isStrategiLain = ['Strategi 1', 'Strategi 2', 'Strategi 3', 'Strategi 4', 'Strategi 6', 'Strategi 7', 'Strategi 8'].includes(strategy);
            const showDelta = false; // Removed Penurunan Brilink
            
            html += '<tr style="border-bottom: 1px solid #eee; transition: background 0.2s;" onmouseenter="this.style.background=\'#f8f9fa\'" onmouseleave="this.style.background=\'white\'">';
            
            if (isMerchantSavol) {
                // Tampilan untuk Merchant Savol Besar Casa Kecil (Qris & EDC)
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_merchant || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.tid_store_id || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_merchant || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.cif || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.savol_bulan_lalu || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.casa_akhir_bulan || '-'}</td>`;
            } else if (isPenurunanMerchant) {
                // Tampilan untuk Penurunan CASA Merchant (QRIS & EDC)
                const deltaValue = nasabah.delta || 0;
                const deltaFormatted = new Intl.NumberFormat('id-ID').format(deltaValue);
                const deltaColor = deltaValue < 0 ? '#d32f2f' : '#2e7d32';
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;"><span style="padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; background-color: ${nasabah.segmentasi === 'MIKRO' ? '#fff3cd' : '#d1ecf1'}; color: ${nasabah.segmentasi === 'MIKRO' ? '#856404' : '#0c5460'};">${nasabah.segmentasi || '-'}</span></td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.saldo_last_eom || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.saldo_terupdate || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600; color: ${deltaColor};">${deltaFormatted}</td>`;
            } else if (isPenurunanBrilink) {
                // Tampilan untuk Penurunan CASA Brilink
                const deltaValue = nasabah.delta || 0;
                const deltaFormatted = new Intl.NumberFormat('id-ID').format(deltaValue);
                const deltaColor = deltaValue < 0 ? '#d32f2f' : '#2e7d32';
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.saldo_last_eom || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.saldo_terupdate || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600; color: ${deltaColor};">${deltaFormatted}</td>`;
            } else if (isQlolaNonDebitur) {
                // Tampilan untuk Qlola Non Debitur
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.uker || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cek_qcash || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cek_cms || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cek_ib || '-'}</td>`;
                html += `<td style="padding: 10px; max-width: 250px; white-space: normal; font-size: 12px;">${nasabah.keterangan || '-'}</td>`;
            } else if (isNonDebiturVolBesar) {
                // Tampilan untuk Non Debitur Vol Besar CASA Kecil
                // Clean and parse values - handle string with thousand separators
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    // Remove dots, commas, and spaces before parsing
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseInt(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const volQcash = parseValue(nasabah.vol_qcash);
                const volQib = parseValue(nasabah.vol_qib);
                const saldo = parseValue(nasabah.saldo);
                
                const volQcashFormatted = new Intl.NumberFormat('id-ID').format(volQcash);
                const volQibFormatted = new Intl.NumberFormat('id-ID').format(volQib);
                const saldoFormatted = new Intl.NumberFormat('id-ID').format(saldo);
                
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.uker || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${volQcashFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${volQibFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600;">${saldoFormatted}</td>`;
            } else if (isQlolaNonaktif) {
                // Tampilan untuk Qlola (Belum ada Qlola / ada namun nonaktif)
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseInt(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const plafon = parseValue(nasabah.plafon);
                const plafonFormatted = new Intl.NumberFormat('id-ID').format(plafon);
                
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || nasabah.kanca || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || nasabah.unit || nasabah.uker || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.norek_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || nasabah.nama_debitur || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600;">${plafonFormatted}</td>`;
                html += `<td style="padding: 10px;">${nasabah.pn_pengelola || '-'}</td>`;
                html += `<td style="padding: 10px; max-width: 250px; white-space: normal; font-size: 12px;">${nasabah.keterangan || '-'}</td>`;
            } else if (isUserAktifCasaKecil) {
                // Tampilan untuk User Aktif Casa Kecil
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseInt(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const saldoBulanLalu = parseValue(nasabah.saldo_bulan_lalu);
                const saldoBulanBerjalan = parseValue(nasabah.saldo_bulan_berjalan);
                const deltaSaldo = parseValue(nasabah.delta_saldo);
                
                const saldoBulanLaluFormatted = new Intl.NumberFormat('id-ID').format(saldoBulanLalu);
                const saldoBulanBerjalanFormatted = new Intl.NumberFormat('id-ID').format(saldoBulanBerjalan);
                const deltaSaldoFormatted = new Intl.NumberFormat('id-ID').format(deltaSaldo);
                const deltaColor = deltaSaldo < 0 ? '#d32f2f' : '#2e7d32';
                
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || nasabah.kode_kanca || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || nasabah.kanca || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || nasabah.uker || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${saldoBulanLaluFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${saldoBulanBerjalanFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600; color: ${deltaColor};">${deltaSaldoFormatted}</td>`;
                html += `<td style="padding: 10px;">${nasabah.nama_rm_pemrakarsa || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.qcash || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.qib || '-'}</td>`;
            } else if (isPenurunanPrioritasRitelMikro) {
                // Tampilan untuk Penurunan Prioritas Ritel & Mikro
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseInt(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const saldoLastEom = parseValue(nasabah.saldo_last_eom);
                const saldoTerupdate = parseValue(nasabah.saldo_terupdate);
                const deltaValue = parseValue(nasabah.delta);
                
                const saldoLastEomFormatted = new Intl.NumberFormat('id-ID').format(saldoLastEom);
                const saldoTerupdateFormatted = new Intl.NumberFormat('id-ID').format(saldoTerupdate);
                const deltaFormatted = new Intl.NumberFormat('id-ID').format(deltaValue);
                const deltaColor = deltaValue < 0 ? '#d32f2f' : '#2e7d32';
                
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi_bpr || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${saldoLastEomFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${saldoTerupdateFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600; color: ${deltaColor};">${deltaFormatted}</td>`;
            } else if (isAumDpk) {
                // Tampilan untuk AUM>2M DPK<50 juta
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseInt(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const aum = parseValue(nasabah.aum);
                const aumFormatted = new Intl.NumberFormat('id-ID').format(aum);
                
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.slp || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.pbo || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.id_prioritas || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600;">${aumFormatted}</td>`;
            } else if (isStrategi8) {
                // Tampilan untuk Wingback Penguatan Produk & Fungsi RM
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseFloat(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const saldoLastEom = nasabah.saldo_last_eom || '0';
                const saldoTerupdate = nasabah.saldo_terupdate || '0';
                const delta = nasabah.delta || '0';
                
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${saldoLastEom}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${saldoTerupdate}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; color: ${parseValue(delta) >= 0 ? '#2e7d32' : '#d32f2f'};">${delta}</td>`;
            } else if (isLayeringWingback) {
                // Tampilan untuk Layering Wingback
                const parseValue = (value) => {
                    if (!value || value === '-') return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value).replace(/[.,\s]/g, '');
                    const parsed = parseInt(cleaned);
                    return isNaN(parsed) ? 0 : parsed;
                };
                
                const saldoLastEom = parseValue(nasabah.saldo_last_eom);
                const saldoTerupdate = parseValue(nasabah.saldo_terupdate);
                const delta = parseValue(nasabah.delta);
                
                const saldoLastEomFormatted = new Intl.NumberFormat('id-ID').format(saldoLastEom);
                const saldoTerupdateFormatted = new Intl.NumberFormat('id-ID').format(saldoTerupdate);
                const deltaFormatted = new Intl.NumberFormat('id-ID').format(delta);
                
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace; font-weight: 600;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.segmentasi || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_simpanan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600;">${saldoLastEomFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600;">${saldoTerupdateFormatted}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600; color: ${delta >= 0 ? '#2e7d32' : '#d32f2f'};">${deltaFormatted}</td>`;
            } else if (isOptimalisasiBusinessCluster) {
                // Tampilan untuk Optimalisasi Business Cluster
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_uker || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.unit_kerja || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.tag_zona_unggulan || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.nama_tenaga_pemasar || '-'}</td>`;
            } else if (isPerusahaanAnak) {
                // Tampilan untuk List Perusahaan Anak
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_partner_vendor || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.jenis_usaha || '-'}</td>`;
                html += `<td style="padding: 10px; font-size: 12px; max-width: 250px; white-space: normal;">${nasabah.alamat || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk_terdekat || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.nama_pic_partner || '-'}</td>`;
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.hp_pic_partner || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.nama_perusahaan_anak || '-'}</td>`;
                html += `<td style="padding: 10px;"><span style="padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; background-color: ${nasabah.status_pipeline && nasabah.status_pipeline.toLowerCase().includes('sudah') ? '#d4edda' : '#fff3cd'}; color: ${nasabah.status_pipeline && nasabah.status_pipeline.toLowerCase().includes('sudah') ? '#155724' : '#856404'};">${nasabah.status_pipeline || '-'}</span></td>`;
            } else if (isExistingPayroll) {
                // Tampilan untuk Existing Payroll
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 600; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                html += `<td style="padding: 10px;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.jumlah_rekening || '0'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace;">${nasabah.saldo_rekening || '0'}</td>`;
            } else if (isPotensiPayroll) {
                // Tampilan untuk Potensi Payroll
                html += `<td style="padding: 10px; font-family: monospace;">${nasabah.kode_cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-size: 12px; color: #666;">${nasabah.cabang_induk || '-'}</td>`;
                html += `<td style="padding: 10px; font-weight: 500;">${nasabah.perusahaan || '-'}</td>`;
                html += `<td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 600;">${nasabah.estimasi_pekerja || '0'}</td>`;
            } else {
                // Tampilan untuk strategi lainnya
                // CIFNO - untuk BC dan strategi lain kosongkan
                if (isOptimalisasiBC || isStrategiLain) {
                    html += `<td style="padding: 10px; font-weight: 600; font-family: monospace;">-</td>`;
                } else {
                    html += `<td style="padding: 10px; font-weight: 600; font-family: monospace;">${nasabah.cifno || '-'}</td>`;
                }
                
                // No. Rekening - untuk BC dan strategi lain
                if (isOptimalisasiBC || isStrategiLain) {
                    html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
                }
                
                html += `<td style="padding: 10px;">${nasabah.nama_nasabah || '-'}</td>`;
                html += `<td style="padding: 10px; font-size: 12px; color: #666;">${nasabah.unit_kerja || nasabah.unit || '-'}</td>`;
            }
            
            // Delta column removed (was for Penurunan Brilink)
            if (showDelta) {
                const saldoFormatted = new Intl.NumberFormat('id-ID').format(deltaValue);
                html += `<td style="padding: 10px; font-size: 13px; text-align: right; color: ${deltaValue > 0 ? '#2e7d32' : '#d32f2f'}; font-weight: 600; font-family: monospace;">Rp ${saldoFormatted}</td>`;
            }
            
            html += `<td style="padding: 10px; text-align: center;">
                <button onclick='selectNasabah(${JSON.stringify(nasabah)})' style="background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: none; padding: 6px 16px; border-radius: 4px; cursor: pointer; font-size: 12px; transition: transform 0.2s;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform='scale(1)'">Pilih</button>
            </td>`;
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        html += '</div>'; // Close scrollable container
        
        // Add pagination controls if needed - FIXED at bottom
        if (paginationData && paginationData.last_page > 1) {
            html += '<div style="padding: 12px; background: #f9fafb; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; border-top: 2px solid #e0e0e0;">';
            
            // Previous button
            const prevDisabled = paginationData.current_page <= 1;
            html += `<button onclick="loadAllNasabahFromPipeline(${paginationData.current_page - 1})" 
                     style="padding: 8px 16px; background: ${prevDisabled ? '#e0e0e0' : '#0066CC'}; color: ${prevDisabled ? '#999' : 'white'}; border: none; border-radius: 4px; cursor: ${prevDisabled ? 'not-allowed' : 'pointer'}; font-size: 13px; font-weight: 600;" 
                     ${prevDisabled ? 'disabled' : ''}>
                     ‹ Sebelumnya
                  </button>`;
            
            // Page info
            html += `<span style="font-size: 13px; color: #666; font-weight: 600;">
                     Halaman ${paginationData.current_page} dari ${paginationData.last_page}
                     <span style="color: #999; font-weight: normal;">(${paginationData.total} total)</span>
                  </span>`;
            
            // Next button
            const nextDisabled = paginationData.current_page >= paginationData.last_page;
            html += `<button onclick="loadAllNasabahFromPipeline(${paginationData.current_page + 1})" 
                     style="padding: 8px 16px; background: ${nextDisabled ? '#e0e0e0' : '#0066CC'}; color: ${nextDisabled ? '#999' : 'white'}; border: none; border-radius: 4px; cursor: ${nextDisabled ? 'not-allowed' : 'pointer'}; font-size: 13px; font-weight: 600;" 
                     ${nextDisabled ? 'disabled' : ''}>
                     Selanjutnya ›
                  </button>`;
            
            html += '</div>';
        }
        
        html += '</div>'; // Close wrapper
        
        document.getElementById('nasabahList').innerHTML = html;
    }
    
    function closeNasabahModal() {
        const modal = document.getElementById('nasabahModal');
        modal.style.display = 'none';
        document.getElementById('searchNasabah').value = '';
        
        // Reset filters
        document.getElementById('filterYear').value = '';
        document.getElementById('filterMonth').value = '';
    }
    
    // Unit Modal Functions
    let allUnits = [];
    let selectedUnits = [];
    
    function openUnitModal() {
        const kodeKc = document.getElementById('rmft_kode_kc').value;
        const namaKc = document.getElementById('nama_kc').value;
        
        if (!kodeKc) {
            alert('Harap pilih RMFT terlebih dahulu');
            return;
        }
        
        document.getElementById('modal_kc_name').textContent = namaKc;
        const modal = document.getElementById('unitModal');
        modal.style.display = 'flex';
        
        // Load units dari KC ini
        loadUnitsForKC(kodeKc);
    }
    
    function closeUnitModal() {
        const modal = document.getElementById('unitModal');
        modal.style.display = 'none';
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
                updateSelectedCount();
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
            const isSelected = selectedUnits.length > 0 && selectedUnits[0].kode_sub_kanca === unit.kode_sub_kanca;
            
            html += `
                <label style="padding: 12px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s; background: ${isSelected ? '#e3f2fd' : 'white'};"
                       onmouseenter="this.style.backgroundColor='#f0f8ff';"
                       onmouseleave="this.style.backgroundColor='${isSelected ? '#e3f2fd' : 'white'}';">
                    <input type="radio" 
                           name="unit_selection"
                           value="${unit.kode_sub_kanca}" 
                           data-name="${unit.sub_kanca}"
                           ${isSelected ? 'checked' : ''}
                           onchange="selectSingleUnit(${JSON.stringify(unit).replace(/"/g, '&quot;')})"
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
    
    function selectSingleUnit(unit) {
        // Clear previous selection and set new one
        selectedUnits = [unit];
        updateSelectedCount();
    }
    
    function updateSelectedCount() {
        const count = selectedUnits.length;
        if (count === 0) {
            document.getElementById('count_text').textContent = 'Belum ada unit dipilih';
        } else {
            document.getElementById('count_text').textContent = `Unit dipilih: ${selectedUnits[0].sub_kanca}`;
        }
    }
    
    function filterUnitList() {
        const searchValue = document.getElementById('searchUnit').value.toLowerCase();
        const filteredUnits = allUnits.filter(unit => 
            unit.sub_kanca.toLowerCase().includes(searchValue) ||
            unit.kode_sub_kanca.toLowerCase().includes(searchValue)
        );
        displayUnits(filteredUnits);
    }
    
    function applySelectedUnits() {
        if (selectedUnits.length === 0) {
            alert('Harap pilih 1 unit');
            return;
        }
        
        // Single selection - gunakan unit yang dipilih
        const selectedUnit = selectedUnits[0];
        
        document.getElementById('nama_uker_display').value = selectedUnit.sub_kanca;
        document.getElementById('nama_uker').value = selectedUnit.sub_kanca;
        document.getElementById('kode_uker').value = selectedUnit.kode_sub_kanca;
        document.getElementById('kode_uker_display').value = selectedUnit.kode_sub_kanca;
        
        // Clear list fields untuk single selection
        document.getElementById('kode_uker_list').value = '';
        document.getElementById('nama_uker_list').value = '';
        
        closeUnitModal();
        
        // Visual feedback
        const displayField = document.getElementById('nama_uker_display');
        const kodeUkerField = document.getElementById('kode_uker_display');
        
        displayField.style.borderColor = '#28a745';
        kodeUkerField.style.borderColor = '#28a745';
        
        setTimeout(() => {
            displayField.style.borderColor = '#ddd';
            kodeUkerField.style.borderColor = '#ddd';
        }, 1500);
    }
    
    let searchTimer;
    function searchNasabahList() {
        clearTimeout(searchTimer);
        const searchValue = document.getElementById('searchNasabah').value;
        
        if (searchValue.length < 2) {
            // Jika kurang dari 2 karakter, load semua data
            loadAllNasabahFromPipeline();
            return;
        }
        
        searchTimer = setTimeout(() => {
            // Get KC and Unit from form
            const kodeKc = document.getElementById('kode_kc').value;
            const isUnitRmft = document.getElementById('is_unit_rmft').value;
            const strategy = document.getElementById('strategy_pipeline').value;
            const kategori = document.getElementById('kategori_strategi').value;
            const filterMonth = document.getElementById('filterMonth').value;
            const filterYear = document.getElementById('filterYear').value;
            
            // Validasi strategy harus dipilih terlebih dahulu
            if (!strategy) {
                alert('Harap pilih Strategy Pull of Pipeline terlebih dahulu');
                document.getElementById('searchNasabah').value = '';
                document.getElementById('strategy_pipeline').focus();
                return;
            }
            
            // Jika multiple units dipilih, gunakan semua unit
            let kodeUkerParam = '';
            if (isUnitRmft === '1' && selectedUnits.length > 0) {
                // Gunakan semua unit yang dipilih
                kodeUkerParam = selectedUnits.map(u => u.kode_sub_kanca).join(',');
            } else {
                // Gunakan single unit
                kodeUkerParam = document.getElementById('kode_uker').value;
            }
            
            document.getElementById('nasabahList').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #0066CC;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0066CC; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 16px;">Mencari...</p>
                </div>
            `;
            
            // Build URL with filters
            let url = `{{ route('api.pipeline.search') }}?search=${searchValue}&kode_kc=${kodeKc}&kode_uker=${kodeUkerParam}&strategy=${encodeURIComponent(strategy)}`;
            if (kategori) {
                url += `&kategori=${encodeURIComponent(kategori)}`;
            }
            if (filterYear) {
                url += `&year=${filterYear}`;
            }
            if (filterMonth) {
                url += `&month=${filterMonth}`;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(response => {
                    // Handle paginated response
                    const nasabahs = response.data || [];
                    
                    if (nasabahs.length === 0) {
                        document.getElementById('nasabahList').innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #666;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.3;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>Tidak ada nasabah ditemukan di ${strategy}</p>
                                <small style="color: #999;">Coba kata kunci lain atau hapus pencarian untuk melihat semua</small>
                            </div>
                        `;
                        return;
                    }
                    
                    displayNasabahList(nasabahs, response);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('nasabahList').innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #d32f2f;">
                            <p>Terjadi kesalahan saat mencari data</p>
                        </div>
                    `;
                });
        }, 500);
    }
    
    // Function to apply filter changes (month/year)
    function applyPipelineFilter() {
        // Reload data dengan filter baru
        loadAllNasabahFromPipeline(1);
    }
    
    function selectNasabah(nasabah) {
        const kategori = document.getElementById('kategori_strategi').value;
        const isPotensiPayroll = kategori === 'Potensi Payroll';
        const isExistingPayroll = kategori === 'Existing Payroll';
        const isMerchantSavol = kategori === 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)';
        const isPerusahaanAnak = kategori === 'List Perusahaan Anak';
        // Set Norek berdasarkan kategori dan data yang tersedia
        if (isPotensiPayroll) {
            document.getElementById('norek').value = nasabah.perusahaan || '';
        } else if (isExistingPayroll) {
            document.getElementById('norek').value = nasabah.cifno || '';
        } else if (isMerchantSavol) {
            // Untuk Merchant Savol, hide nomor rekening
            document.getElementById('norek').value = '-';
        } else {
            // Untuk kategori lainnya, hide nomor rekening
            document.getElementById('norek').value = '-';
        }
        
        // Set nama nasabah - untuk Potensi Payroll gunakan perusahaan, untuk Perusahaan Anak gunakan nama_partner_vendor
        if (isPotensiPayroll) {
            document.getElementById('nama_nasabah').value = nasabah.perusahaan || '';
        } else if (isPerusahaanAnak) {
            document.getElementById('nama_nasabah').value = nasabah.nama_partner_vendor || '';
        } else {
            document.getElementById('nama_nasabah').value = nasabah.nama_nasabah || '';
        }
        
        // Ambil nilai delta atau estimasi pekerja
        let deltaValue = 0;
        
        if (isPotensiPayroll) {
            // Untuk Potensi Payroll, gunakan estimasi_pekerja
            if (nasabah.estimasi_pekerja) {
                if (typeof nasabah.estimasi_pekerja === 'number') {
                    deltaValue = nasabah.estimasi_pekerja;
                } else {
                    deltaValue = parseInt(nasabah.estimasi_pekerja.replace(/[.,]/g, ''));
                }
            }
        } else if (nasabah.delta !== undefined && nasabah.delta !== null) {
            // Gunakan nilai delta jika tersedia
            if (typeof nasabah.delta === 'number') {
                deltaValue = nasabah.delta;
            } else if (typeof nasabah.delta === 'string') {
                deltaValue = parseInt(nasabah.delta.replace(/[.,]/g, ''));
            }
        } else {
            // Fallback ke saldo_terupdate jika delta tidak ada
            if (typeof nasabah.saldo_terupdate === 'number') {
                deltaValue = nasabah.saldo_terupdate;
            } else if (typeof nasabah.saldo_terupdate === 'string') {
                deltaValue = parseInt(nasabah.saldo_terupdate.replace(/[.,]/g, ''));
            } else if (nasabah.saldo_last_eom) {
                if (typeof nasabah.saldo_last_eom === 'number') {
                    deltaValue = nasabah.saldo_last_eom;
                } else {
                    deltaValue = parseInt(nasabah.saldo_last_eom.replace(/[.,]/g, ''));
                }
            }
        }
        
        if (isNaN(deltaValue)) {
            deltaValue = 0;
        }
        
        // Set nilai delta ke field RP/Jumlah
        // document.getElementById('rp_jumlah').value = deltaValue; // Disabled auto-fill
        
        // Set segmen berdasarkan strategy yang dipilih
        const strategy = document.getElementById('strategy_pipeline').value;
        let segmen = '';
        
        if (strategy.includes('Brilink')) {
            segmen = 'Agen Brilink';
        } else if (strategy.includes('Mantri')) {
            segmen = 'Mikro';
        } else if (strategy.includes('Merchant Mikro')) {
            segmen = 'Merchant';
        } else if (strategy.includes('Merchant Ritel')) {
            segmen = 'Merchant';
        } else if (strategy.includes('No-Segment Mikro')) {
            segmen = 'Mikro';
        } else if (strategy.includes('No-Segment Ritel')) {
            segmen = 'Ritel Badan Usaha';
        } else if (strategy.includes('SME Ritel')) {
            segmen = 'SME';
        } else if (strategy.includes('QRIS')) {
            segmen = 'Merchant';
        } else {
            segmen = nasabah.segmen_nasabah || 'Konsumer';
        }
        
        document.querySelector('select[name="segmen_nasabah"]').value = segmen;
        closeNasabahModal();
        
        // Visual feedback
        const norekInput = document.getElementById('norek');
        norekInput.style.borderColor = '#4caf50';
        setTimeout(() => {
            norekInput.style.borderColor = '#ddd';
        }, 1500);
    }
    
    // Close modal when clicking outside
    document.getElementById('nasabahModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNasabahModal();
        }
    });
    
    document.getElementById('unitModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeUnitModal();
        }
    });
    
    // Form validation
    function validateForm() {
        const tipeNasabah = document.getElementById('tipe_nasabah').value;
        
        if (!tipeNasabah) {
            alert('Harap pilih Tipe Pipeline terlebih dahulu');
            return false;
        }
        
        if (tipeNasabah === 'lama') {
            // Validate Nasabah Lama fields
            const norek = document.getElementById('norek').value.trim();
            const namaNasabah = document.getElementById('nama_nasabah').value.trim();
            const rpJumlah = document.getElementById('rp_jumlah').value.trim();
            const strategy = document.getElementById('strategy_pipeline').value;
            const kategori = document.getElementById('kategori_strategi').value;
            
            // Daftar kategori yang tidak memerlukan CIFNO karena menggunakan pipeline data
            const kategoriBebas = [
                'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)',
                'Qlola (Belum ada Qlola / ada namun nonaktif)',
                'Qlola Non Debitur',
                'Non Debitur Vol Besar CASA Kecil',
                'AUM>2M DPK<50 juta',
                'User Aktif Casa Kecil',
                'PENURUNAN CASA BRILINK',
                'PENURUNAN CASA MERCHANT (QRIS & EDC)',
                'Existing Payroll',
                'Potensi Payroll',
                'List Perusahaan Anak'
            ];
            
            const isPipelineData = kategoriBebas.includes(kategori) || 
                                   strategy === 'Wingback Penguatan Produk & Fungsi RM' ||
                                   strategy === 'Layering' ||
                                   kategori === 'Wingback' ||
                                   strategy === 'Optimalisasi Business Cluster';
            
            // CIFNO tidak wajib untuk kategori yang menggunakan pipeline
            if (!isPipelineData && !norek) {
                alert('Harap cari dan pilih nasabah terlebih dahulu');
                return false;
            }
            if (!namaNasabah) {
                alert('Harap isi Nama Nasabah untuk Nasabah Lama');
                return false;
            }
            if (!rpJumlah) {
                alert('Harap isi RP / Jumlah untuk Nasabah Lama');
                return false;
            }
            
            // Remove Nasabah Baru fields from submission
            document.getElementById('norek_baru').removeAttribute('name');
            document.getElementById('nama_nasabah_baru').removeAttribute('name');
            document.getElementById('rp_jumlah_baru').removeAttribute('name');
            
        } else if (tipeNasabah === 'baru') {
            // Validate Nasabah Baru fields
            const norekBaru = document.getElementById('norek_baru').value.trim();
            const namaNasabahBaru = document.getElementById('nama_nasabah_baru').value.trim();
            const rpJumlahBaru = document.getElementById('rp_jumlah_baru').value.trim();
            
            if (!norekBaru) {
                alert('Harap isi No. Rekening untuk Nasabah Baru');
                return false;
            }
            if (!namaNasabahBaru) {
                alert('Harap isi Nama Nasabah untuk Nasabah Baru');
                return false;
            }
            if (!rpJumlahBaru) {
                alert('Harap isi RP / Jumlah untuk Nasabah Baru');
                return false;
            }
            
            // Copy Nasabah Baru values to Nasabah Lama fields for submission
            document.getElementById('norek').value = norekBaru;
            document.getElementById('nama_nasabah').value = namaNasabahBaru;
            document.getElementById('rp_jumlah').value = rpJumlahBaru;
            
            // Remove Nasabah Baru fields from submission
            document.getElementById('norek_baru').removeAttribute('name');
            document.getElementById('nama_nasabah_baru').removeAttribute('name');
            document.getElementById('rp_jumlah_baru').removeAttribute('name');
        }
        
        return true;
    }
    
    // Initialize untuk RMFT - cek apakah bisa pilih unit
    @if(auth()->user()->isRMFT())
    (function() {
        const rmftUkerName = "{{ optional($rmftData)->uker ?? '' }}";
        @php
            $ukerData = null;
            if ($rmftData) {
                // Cari berdasarkan nama uker yang EXACT
                if ($rmftData->uker) {
                    $ukerData = \App\Models\Uker::where('sub_kanca', $rmftData->uker)->first();
                }
                // Fallback ke kanca
                if (!$ukerData && $rmftData->kanca) {
                    $ukerData = \App\Models\Uker::where('kanca', $rmftData->kanca)->first();
                }
            }
        @endphp
        const kodeKc = "{{ $ukerData ? $ukerData->kode_kanca : '' }}";
        
        // Set kode KC untuk modal
        document.getElementById('rmft_kode_kc').value = kodeKc;
        
        // Clear list fields
        document.getElementById('kode_uker_list').value = '';
        document.getElementById('nama_uker_list').value = '';
        
        // Cek apakah RMFT ini bisa ganti unit
        // Hanya KC/KK/Unit yang bisa ganti, KCP tidak bisa
        const ukerNameUpper = rmftUkerName.toUpperCase();
        const canSelectUnit = ukerNameUpper.includes('UNIT') || ukerNameUpper.startsWith('KC ') || ukerNameUpper.startsWith('KK ');
        
        if (canSelectUnit) {
            // Enable modal untuk pilih unit
            document.getElementById('unit_selector_label').style.display = 'inline';
            document.getElementById('nama_uker_display').style.cursor = 'pointer';
            document.getElementById('nama_uker_display').style.backgroundColor = '#f0f8ff';
            document.getElementById('nama_uker_display').title = 'Klik untuk memilih unit di KC ini';
            document.getElementById('nama_uker_display').onclick = openUnitModal;
            document.getElementById('kode_uker_display').style.cursor = 'pointer';
            document.getElementById('kode_uker_display').style.backgroundColor = '#f0f8ff';
            document.getElementById('kode_uker_display').title = 'Klik untuk memilih unit di KC ini';
            document.getElementById('kode_uker_display').onclick = openUnitModal;
            
            // Set is_unit_rmft jika mengandung UNIT
            if (ukerNameUpper.includes('UNIT')) {
                document.getElementById('is_unit_rmft').value = '1';
            } else {
                document.getElementById('is_unit_rmft').value = '0';
            }
        } else {
            // KCP atau lainnya - readonly
            document.getElementById('is_unit_rmft').value = '0';
        }
        
        // Enable aktivitas fields
        enableAktivitasFields();
    })();
    @endif
    
    // Event listener untuk update hidden field rencana_aktivitas
    document.getElementById('rencana_aktivitas').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const namaRencana = selectedOption.getAttribute('data-nama') || selectedOption.text;
        document.getElementById('rencana_aktivitas_text').value = namaRencana;
    });
</script>
@endsection
