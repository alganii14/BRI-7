@extends('layouts.app')

@section('title', 'Pipeline')
@section('page-title', 'Pipeline')

@section('content')
<style>
    .btn {
        padding: 10px 20px;
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

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-warning {
        background-color: #ff9800;
        color: white;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .badge-rmft {
        background-color: #4caf50;
        color: white;
    }

    .badge-assigned {
        background-color: #ff9800;
        color: white;
        font-size: 11px;
        padding: 3px 8px;
        margin-left: 8px;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #333;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    table th {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }

    table tr:hover {
        background-color: #f8f9fa;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .header-actions {
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .btn {
            padding: 8px 16px;
            font-size: 13px;
        }
        
        .header-actions {
            gap: 10px !important;
        }
        
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 480px) {
        .btn {
            padding: 8px 12px;
            font-size: 12px;
        }
    }

    .pagination-wrapper {
        margin-top: 30px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        text-align: center;
    }

    .pagination-info {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .rekap-table-wrapper {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .rekap-table-wrapper h4 {
        margin: 0 0 15px 0;
        color: #28a745;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #rekapTable table {
        font-size: 12px;
    }

    #rekapTable th {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
        color: white;
        padding: 12px;
        font-size: 12px;
        text-align: center;
        font-weight: 600;
    }

    #rekapTable td {
        padding: 12px;
        text-align: center;
        white-space: nowrap;
    }

    #rekapTable td:nth-child(2),
    #rekapTable td:nth-child(4) {
        text-align: left;
    }

    .pagination-wrapper a:hover {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 102, 204, 0.3);
        transition: all 0.3s ease;
    }

    .pagination-wrapper span {
        transition: all 0.3s ease;
    }
</style>

<div class="page-header">
    <h2>Manajemen Pipeline</h2>
    <p>Kelola semua aktivitas pipeline</p>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(auth()->user()->isAdmin())
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('aktivitas.index') }}">
        <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label for="kode_kc" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Filter per KC</label>
                <select name="kode_kc" id="kode_kc" class="form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    <option value="">-- Semua KC --</option>
                    @foreach($listKC as $kc)
                    <option value="{{ $kc->kode_kc }}" {{ request('kode_kc') == $kc->kode_kc ? 'selected' : '' }}>
                        {{ $kc->kode_kc }} - {{ $kc->nama_kc }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label for="rmft_id" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Filter per RMFT</label>
                <select name="rmft_id" id="rmft_id_admin" class="form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;" {{ !request('kode_kc') ? 'disabled' : '' }}>
                    <option value="">{{ !request('kode_kc') ? '-- Pilih KC terlebih dahulu --' : '-- Semua RMFT --' }}</option>
                    @foreach($listRMFT as $rmft)
                    <option value="{{ $rmft->id }}" data-kc="{{ $rmft->kode_kc }}" {{ request('rmft_id') == $rmft->id ? 'selected' : '' }}>
                        {{ $rmft->pernr }} - {{ $rmft->completename }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label for="kode_uker" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Filter per Unit</label>
                <select name="kode_uker" id="kode_uker" class="form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    <option value="">-- Semua Unit --</option>
                    @foreach($listUnit as $unit)
                    <option value="{{ $unit->kode_uker }}" data-kc="{{ $unit->kode_kc }}" {{ request('kode_uker') == $unit->kode_uker ? 'selected' : '' }}>
                        {{ $unit->kode_uker }} - {{ $unit->nama_uker }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label for="tanggal_dari" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px;">
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label for="tanggal_sampai" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px;">
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Filter</button>
                <a href="{{ route('aktivitas.index') }}" class="btn" style="background-color: #6c757d; color: white; white-space: nowrap;">Reset</a>
            </div>
        </div>
    </form>
</div>

<script>
// Filter RMFT dan unit berdasarkan KC yang dipilih
document.getElementById('kode_kc').addEventListener('change', function() {
    var selectedKC = this.value;
    var rmftSelect = document.getElementById('rmft_id_admin');
    var unitSelect = document.getElementById('kode_uker');
    
    // Handle RMFT filter
    var rmftOptions = rmftSelect.querySelectorAll('option');
    if (selectedKC === '') {
        // Disable RMFT select jika KC belum dipilih
        rmftSelect.disabled = true;
        rmftSelect.value = '';
        rmftOptions[0].textContent = '-- Pilih KC terlebih dahulu --';
        // Hide all RMFT options except first
        rmftOptions.forEach(function(option, index) {
            if (index > 0) option.style.display = 'none';
        });
    } else {
        // Enable RMFT select dan filter berdasarkan KC
        rmftSelect.disabled = false;
        rmftOptions[0].textContent = '-- Semua RMFT --';
        rmftOptions.forEach(function(option) {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                var optionKC = option.getAttribute('data-kc');
                if (optionKC === selectedKC) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });
        // Reset RMFT selection jika tidak sesuai dengan KC
        if (rmftSelect.value !== '') {
            var selectedOption = rmftSelect.querySelector('option[value="' + rmftSelect.value + '"]');
            if (selectedOption && selectedOption.style.display === 'none') {
                rmftSelect.value = '';
            }
        }
    }
    
    // Handle Unit filter
    var unitOptions = unitSelect.querySelectorAll('option');
    unitOptions.forEach(function(option) {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            var optionKC = option.getAttribute('data-kc');
            if (selectedKC === '' || optionKC === selectedKC) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    // Reset unit selection jika tidak sesuai dengan KC
    if (unitSelect.value !== '') {
        var selectedOption = unitSelect.querySelector('option[value="' + unitSelect.value + '"]');
        if (selectedOption && selectedOption.style.display === 'none') {
            unitSelect.value = '';
        }
    }
});

// Initialize on page load
(function() {
    var selectedKC = document.getElementById('kode_kc').value;
    var rmftSelect = document.getElementById('rmft_id_admin');
    var rmftOptions = rmftSelect.querySelectorAll('option');
    
    if (selectedKC === '') {
        rmftSelect.disabled = true;
        rmftOptions[0].textContent = '-- Pilih KC terlebih dahulu --';
        rmftOptions.forEach(function(option, index) {
            if (index > 0) option.style.display = 'none';
        });
    } else {
        rmftOptions.forEach(function(option) {
            if (option.value !== '') {
                var optionKC = option.getAttribute('data-kc');
                if (optionKC !== selectedKC) {
                    option.style.display = 'none';
                }
            }
        });
    }
})();
</script>
@endif

@if(auth()->user()->isManager())
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('aktivitas.index') }}">
        <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label for="rmft_id" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Filter per RMFT</label>
                <select name="rmft_id" id="rmft_id" class="form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    <option value="">-- Semua RMFT --</option>
                    @foreach($listRMFT as $rmft)
                    <option value="{{ $rmft->id }}" {{ request('rmft_id') == $rmft->id ? 'selected' : '' }}>
                        {{ $rmft->pernr }} - {{ $rmft->completename }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label for="kode_uker" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Filter per Unit</label>
                <select name="kode_uker" id="kode_uker" class="form-control" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                    <option value="">-- Semua Unit --</option>
                    @foreach($listUnit as $unit)
                    <option value="{{ $unit->kode_uker }}" {{ request('kode_uker') == $unit->kode_uker ? 'selected' : '' }}>
                        {{ $unit->kode_uker }} - {{ $unit->nama_uker }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label for="tanggal_dari" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px;">
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label for="tanggal_sampai" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px;">
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Filter</button>
                <a href="{{ route('aktivitas.index') }}" class="btn" style="background-color: #6c757d; color: white; white-space: nowrap;">Reset</a>
            </div>
        </div>
    </form>
</div>
@endif

@if(auth()->user()->isRMFT())
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('aktivitas.index') }}">
        <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label for="tanggal_dari" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px;">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label for="tanggal_sampai" style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; font-size: 14px;">
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Filter</button>
                <a href="{{ route('aktivitas.index') }}" class="btn" style="background-color: #6c757d; color: white; white-space: nowrap;">Reset</a>
            </div>
        </div>
    </form>
</div>
@endif

<div class="card">
    <div class="header-actions" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <h3 style="margin: 0 0 5px 0;">Daftar Pipeline</h3>
            <p style="margin: 0; color: #666; font-size: 14px;">
                Total: <strong style="color: #0066CC;">{{ $aktivitas->total() }}</strong> aktivitas 
                @if($aktivitas->hasPages())
                    | Halaman <strong>{{ $aktivitas->currentPage() }}</strong> dari <strong>{{ $aktivitas->lastPage() }}</strong>
                    | Menampilkan <strong>20 data per halaman</strong>
                @endif
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            @if(auth()->user()->isAdmin())
            <button onclick="toggleRekapTable()" class="btn btn-info" style="white-space: nowrap;">📊 Rekap</button>
            <div style="position: relative;">
                <button onclick="toggleExportDropdown()" class="btn btn-success" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; gap: 8px; color: white; white-space: nowrap;">
                    <span>📥</span>
                    <span>Export</span>
                    <span style="font-size: 10px;">▼</span>
                </button>
                <div id="exportDropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 5px; background: white; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 150px; z-index: 1000;">
                    <a href="#" onclick="exportRekap('excel'); return false;" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; transition: background 0.2s;">
                        <span style="margin-right: 8px;">📊</span> Excel
                    </a>
                    <a href="#" onclick="exportRekap('csv'); return false;" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; transition: background 0.2s;">
                        <span style="margin-right: 8px;">📄</span> CSV
                    </a>
                </div>
            </div>
            @endif
            <a href="{{ route('aktivitas.create') }}" class="btn btn-primary" style="white-space: nowrap;">+ Tambah Pipeline</a>
            @if(auth()->user()->isRMFT())
            <a href="{{ route('aktivitas.sick-leave.form') }}" class="btn" style="background: linear-gradient(135deg, #FF6B6B 0%, #EE5A52 100%); color: white; white-space: nowrap;">
                🏥 Tandai Sakit/Izin
            </a>
            @endif
            @if(auth()->user()->isAdmin())
            <button onclick="openDeleteAllModal()" class="btn" style="background: linear-gradient(135deg, #e53935 0%, #c62828 100%); color: white; white-space: nowrap;">
                🗑️ Hapus Semua Data
            </button>
            @endif
        </div>
    </div>

    <!-- Tabel Rekap Pipeline (Hidden by default) -->
    <div id="rekapTable" class="rekap-table-wrapper" style="display: none;">
        <h4 style="margin: 0 0 15px 0;">📊 Rekap Pipeline</h4>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama KC</th>
                        <th>PN</th>
                        <th>Nama RMFT</th>
                        <th>Nama Pemilik</th>
                        <th>No Rekening</th>
                        <th>Pipeline</th>
                        <th>Realisasi</th>
                        <th>Keterangan</th>
                        <th>Validasi</th>
                    </tr>
                </thead>
                <tbody id="rekapTableBody">
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 20px;">
                            Loading data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>TANGGAL DANA MASUK</th>
                    <th>NAMA RMFT</th>
                    <th>PN</th>
                    <th>KODE KC</th>
                    <th>NAMA KC</th>
                    <th>KELOMPOK</th>
                    <th>STRATEGI</th>
                    <th>KATEGORI</th>
                    <th>RENCANA AKTIVITAS</th>
                    <th>SEGMEN NASABAH</th>
                    <th>NAMA NASABAH</th>
                    <th>CIFNO</th>
                    <th>NO. REKENING</th>
                    <th>TARGET</th>
                    <th>STATUS</th>
                    <th>REALISASI</th>
                    <th>KETERANGAN</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aktivitas as $item)
                <tr>
                    <td>{{ $loop->iteration + ($aktivitas->currentPage() - 1) * $aktivitas->perPage() }}</td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>
                        {{ $item->nama_rmft }}
                        @if($item->tipe == 'assigned')
                        <span class="badge badge-assigned">Assigned by {{ $item->assignedBy->name ?? 'Manager' }}</span>
                        @endif
                    </td>
                    <td>{{ $item->pn }}</td>
                    <td>{{ $item->kode_kc }}</td>
                    <td>{{ $item->nama_kc }}</td>
                    <td>{{ $item->kelompok }}</td>
                    <td>{{ $item->strategy_pipeline ?? '-' }}</td>
                    <td>{{ $item->kategori_strategi ?? '-' }}</td>
                    <td>{{ $item->rencana_aktivitas }}</td>
                    <td>{{ $item->segmen_nasabah }}</td>
                    <td>{{ $item->nama_nasabah }}</td>
                    <td>{{ $item->nasabah->cifno ?? '-' }}</td>
                    <td>{{ $item->norek ?? '-' }}</td>
                    <td>Rp {{ number_format($item->rp_jumlah, 0, ',', '.') }}</td>
                    <td>
                        @if($item->status_realisasi == 'belum')
                        <span class="badge badge-warning">Belum</span>
                        @elseif($item->status_realisasi == 'tercapai')
                        <span class="badge badge-success">✅ Tercapai</span>
                        @if($item->latitude && $item->longitude)
                        <span class="badge" style="background-color: #2196F3; color: white; margin-left: 4px;" title="Lokasi tersedia">📍</span>
                        @endif
                        @elseif($item->status_realisasi == 'tidak_tercapai')
                        <span class="badge badge-danger">❌ Tidak Tercapai</span>
                        @if($item->latitude && $item->longitude)
                        <span class="badge" style="background-color: #2196F3; color: white; margin-left: 4px;" title="Lokasi tersedia">📍</span>
                        @endif
                        @elseif($item->status_realisasi == 'lebih')
                        <span class="badge badge-info">🎉 Melebihi</span>
                        @if($item->latitude && $item->longitude)
                        <span class="badge" style="background-color: #2196F3; color: white; margin-left: 4px;" title="Lokasi tersedia">📍</span>
                        @endif
                        @endif
                    </td>
                    <td>
                        @if($item->status_realisasi != 'belum')
                        Rp {{ number_format($item->nominal_realisasi, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($item->keterangan_realisasi)
                            @if($item->keterangan_realisasi == 'Sakit')
                                <span class="badge" style="background-color: #FF6B6B; color: white;">😷 Sakit</span>
                            @elseif($item->keterangan_realisasi == 'Izin')
                                <span class="badge" style="background-color: #FFA726; color: white;">📋 Izin</span>
                            @else
                                {{ $item->keterangan_realisasi }}
                            @endif
                        @elseif($item->keterangan)
                            {{ $item->keterangan }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('aktivitas.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">👁️</a>
                            
                            @if(auth()->user()->isAdmin())
                                {{-- Admin bisa edit/hapus kapan saja --}}
                                <a href="{{ route('aktivitas.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('aktivitas.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus aktivitas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @elseif(auth()->user()->isManager())
                                {{-- Manager hanya bisa edit/hapus jika status masih belum --}}
                                @if($item->status_realisasi == 'belum')
                                <a href="{{ route('aktivitas.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('aktivitas.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus aktivitas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                                @endif
                            @elseif(auth()->user()->isRMFT())
                                @if($item->status_realisasi == 'belum')
                                <a href="{{ route('aktivitas.feedback', $item->id) }}" class="btn btn-primary btn-sm">Feedback</a>
                                @else
                                <span style="color: #28a745; font-size: 12px;">✓ Sudah Feedback</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="16" style="text-align: center; padding: 40px; color: #666;">
                        Belum ada data pipeline. <a href="{{ route('aktivitas.create') }}" style="color: #0066CC;">Tambah pipeline</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($aktivitas->hasPages())
    <div class="pagination-wrapper">
        <p class="pagination-info">Showing {{ $aktivitas->firstItem() }} to {{ $aktivitas->lastItem() }} of {{ $aktivitas->total() }} results</p>
        
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
            @if ($aktivitas->onFirstPage())
                <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">← Previous</span>
            @else
                <a href="{{ $aktivitas->appends(request()->query())->previousPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">← Previous</a>
            @endif

            {{-- Show pages 1 to 5 only --}}
            @php
                $currentPage = $aktivitas->currentPage();
                $lastPage = $aktivitas->lastPage();
                $startPage = 1;
                $endPage = min(5, $lastPage);
            @endphp

            @foreach (range($startPage, $endPage) as $page)
                @php $url = $aktivitas->appends(request()->query())->url($page); @endphp
                @if ($page == $currentPage)
                    <span style="padding: 10px 20px; background: linear-gradient(135deg, #0066CC 0%, #003D82 100%); color: white; border: 1px solid #0066CC; border-radius: 4px;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">{{ $page }}</a>
                @endif
            @endforeach

            @if ($aktivitas->hasMorePages())
                <a href="{{ $aktivitas->appends(request()->query())->nextPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">Next →</a>
            @else
                <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Modal Delete All Confirmation -->
@if(auth()->user()->isAdmin())
<div id="deleteAllModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 500px; padding: 0; box-shadow: 0 10px 40px rgba(0,0,0,0.3); overflow: hidden;">
        <div style="padding: 20px; background: linear-gradient(135deg, #e53935 0%, #c62828 100%); color: white;">
            <h3 style="margin: 0; font-size: 20px; font-weight: 600;">⚠️ Konfirmasi Hapus Semua Data</h3>
        </div>
        
        <div style="padding: 30px 20px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="font-size: 60px; margin-bottom: 15px;">🗑️</div>
                <p style="font-size: 16px; color: #333; margin: 0 0 10px 0; font-weight: 600;">
                    Anda yakin ingin menghapus SEMUA data aktivitas?
                </p>
                <p style="font-size: 14px; color: #666; margin: 0;">
                    Total: <strong id="totalCount">{{ $aktivitas->total() }}</strong> aktivitas
                </p>
            </div>
            
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <p style="margin: 0; color: #856404; font-size: 13px; line-height: 1.6;">
                    <strong>⚠️ PERINGATAN:</strong><br>
                    • Semua data aktivitas akan dihapus secara permanen<br>
                    • Tindakan ini TIDAK DAPAT dibatalkan<br>
                    • Pastikan Anda sudah membuat backup jika diperlukan
                </p>
            </div>
            
            <form id="deleteAllForm" action="{{ route('aktivitas.delete-all') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeDeleteAllModal()" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">
                        Batal
                    </button>
                    <button type="submit" style="padding: 12px 24px; background: linear-gradient(135deg, #e53935 0%, #c62828 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                        Ya, Hapus Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteAllModal() {
    document.getElementById('deleteAllModal').style.display = 'flex';
}

function closeDeleteAllModal() {
    document.getElementById('deleteAllModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deleteAllModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteAllModal();
    }
});

function toggleRekapTable() {
    const rekapTable = document.getElementById('rekapTable');
    const isVisible = rekapTable.style.display !== 'none';
    
    if (isVisible) {
        rekapTable.style.display = 'none';
    } else {
        rekapTable.style.display = 'block';
        loadRekapData();
    }
}

function getFilterParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const params = new URLSearchParams();
    
    @if(auth()->user()->isAdmin())
        if (urlParams.get('kode_kc')) params.append('kode_kc', urlParams.get('kode_kc'));
        if (urlParams.get('kode_uker')) params.append('kode_uker', urlParams.get('kode_uker'));
    @elseif(auth()->user()->isManager())
        if (urlParams.get('kode_uker')) params.append('kode_uker', urlParams.get('kode_uker'));
    @endif
    
    if (urlParams.get('tanggal_dari')) params.append('tanggal_dari', urlParams.get('tanggal_dari'));
    if (urlParams.get('tanggal_sampai')) params.append('tanggal_sampai', urlParams.get('tanggal_sampai'));
    
    return params;
}

function loadRekapData() {
    const tbody = document.getElementById('rekapTableBody');
    tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 20px;">Loading data...</td></tr>';
    
    const params = getFilterParams();
    
    fetch(`{{ route('aktivitas.rekap') }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 20px; color: #666;">Tidak ada data rekap</td></tr>';
                return;
            }
            
            let html = '';
            data.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_kc || '-'}</td>
                        <td>${item.pn || '-'}</td>
                        <td>${item.nama_rmft || '-'}</td>
                        <td>${item.nama_pemilik || '-'}</td>
                        <td>${item.no_rekening || '-'}</td>
                        <td>Rp ${(item.pipeline / 1000000).toFixed(2)} Jt</td>
                        <td>Rp ${(item.realisasi / 1000000).toFixed(2)} Jt</td>
                        <td>${item.keterangan || '-'}</td>
                        <td>${item.validasi || '-'}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading rekap data:', error);
            tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 20px; color: #dc3545;">Error loading data</td></tr>';
        });
}

function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function exportRekap(format) {
    const params = getFilterParams();
    params.append('format', format);
    
    const url = `{{ route('aktivitas.export-rekap') }}?${params.toString()}`;
    window.location.href = url;
    
    // Close dropdown
    document.getElementById('exportDropdown').style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('exportDropdown');
    const button = e.target.closest('button[onclick="toggleExportDropdown()"]');
    
    if (dropdown && !button && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<style>
#exportDropdown a:hover {
    background-color: #f8f9fa;
}
</style>
@endif

@endsection
