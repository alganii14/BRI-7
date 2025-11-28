@extends('layouts.app')

@section('title', 'Brilink Saldo < 10 Juta')
@section('page-title', 'Data Brilink Saldo Kurang Dari 10 Juta')

@section('content')
<style>
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }

    th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .btn {
        padding: 8px 16px;
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

    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .btn-danger-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .btn-primary:hover, .btn-success:hover, .btn-danger-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #333;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .actions {
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
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px;
    }

    .search-form {
        display: flex;
        gap: 10px;
    }

    .search-form input, .search-form select {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
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
</style>

<div class="header-actions">
    <div style="display: flex; gap: 10px;">
        @if($brilinks->total() > 0)
        <form action="{{ route('brilink.delete-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data Brilink ({{ $brilinks->total() }} baris).\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!\n\nApakah Anda yakin ingin melanjutkan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-gradient">
                🗑️ Hapus Semua
            </button>
        </form>
        @endif
        <a href="{{ route('brilink.create') }}" class="btn btn-primary">
            ➕ Tambah Data
        </a>
        <a href="{{ route('brilink.import.form') }}" class="btn btn-success">
            📁 Import CSV
        </a>
    </div>
    
    <form method="GET" action="{{ route('brilink.index') }}" class="search-form" style="display:flex;gap:10px;align-items:end;">
        <div style="flex:1;">
            <select name="kd_cabang" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:6px;font-size:14px;background:white;">
                <option value="">Semua Cabang</option>
                @foreach($listCabang as $cabang)
                    <option value="{{ $cabang->kd_cabang }}" {{ request('kd_cabang') == $cabang->kd_cabang ? 'selected' : '' }}>
                        {{ $cabang->cabang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:2;">
            <input type="text" name="search" placeholder="Cari nama agen, ID agen, no rekening..." value="{{ request('search') }}" style="width:100%;">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        @if(request('search') || request('kd_cabang'))
            <a href="{{ route('brilink.index') }}" class="btn btn-warning">✖ Reset</a>
        @endif
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@php
    $latestTanggalPosisi = \App\Models\Brilink::whereNotNull('tanggal_posisi_data')
                                              ->orderBy('tanggal_posisi_data', 'desc')
                                              ->first();
@endphp

@if($latestTanggalPosisi && $latestTanggalPosisi->tanggal_posisi_data)
<div style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px 20px; border-radius: 6px; margin-bottom: 20px;">
    <p style="margin: 0; color: #1976d2; font-weight: 600; font-size: 14px;">
        📅 Posisi Data: {{ \Carbon\Carbon::parse($latestTanggalPosisi->tanggal_posisi_data)->format('d F Y') }}
    </p>
</div>
@endif

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Cabang</th>
                <th>Cabang</th>
                <th>Kode Uker</th>
                <th>Unit Kerja</th>
                <th>Nama Agen</th>
                <th>ID Agen</th>
                <th>Kelas</th>
                <th>No Telpon</th>
                <th>Bidang Usaha</th>
                <th>No Rekening</th>
                <th>CASA</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brilinks as $index => $item)
            <tr>
                <td>{{ $brilinks->firstItem() + $index }}</td>
                <td>{{ $item->kd_cabang }}</td>
                <td>{{ $item->cabang }}</td>
                <td>{{ $item->kd_uker }}</td>
                <td>{{ $item->uker }}</td>
                <td>{{ $item->nama_agen }}</td>
                <td>{{ $item->id_agen }}</td>
                <td>{{ $item->kelas }}</td>
                <td>{{ $item->no_telpon }}</td>
                <td>{{ $item->bidang_usaha }}</td>
                <td>{{ $item->norek }}</td>
                <td>{{ $item->casa }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('brilink.edit', $item->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('brilink.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" style="text-align: center; padding: 40px;">
                    <p style="color: #999; font-size: 16px;">Tidak ada data Brilink.</p>
                    <a href="{{ route('brilink.import.form') }}" class="btn btn-success" style="margin-top: 10px;">Import CSV</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    <p class="pagination-info">Showing {{ $brilinks->firstItem() }} to {{ $brilinks->lastItem() }} of {{ $brilinks->total() }} results</p>
    
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
        @if ($brilinks->onFirstPage())
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">← Previous</span>
        @else
            <a href="{{ $brilinks->previousPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">← Previous</a>
        @endif

        @php
            $currentPage = $brilinks->currentPage();
            $lastPage = $brilinks->lastPage();
            $startPage = 1;
            $endPage = min(5, $lastPage);
        @endphp

        @foreach (range($startPage, $endPage) as $page)
            @if ($page == $currentPage)
                <span style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 1px solid #667eea; border-radius: 4px;">{{ $page }}</span>
            @else
                <a href="{{ $brilinks->url($page) }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">{{ $page }}</a>
            @endif
        @endforeach

        @if ($brilinks->hasMorePages())
            <a href="{{ $brilinks->nextPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">Next →</a>
        @else
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">Next →</span>
        @endif
    </div>
</div>

@endsection

<script>
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);
</script>
