@extends('layouts.app')

@section('title', 'Strategi 8 - Wingback Penguatan Produk & Fungsi RM')
@section('page-title', 'Data Strategi 8 - Wingback Penguatan Produk & Fungsi RM')

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

    .btn-info {
        background-color: #17a2b8;
        color: white;
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

    .search-form input {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        min-width: 300px;
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
        @if($strategi8->total() > 0)
        <form action="{{ route('strategi8.delete-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data strategi 8 ({{ number_format($strategi8->total(), 0, ',', '.') }} baris).\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!\n\nApakah Anda yakin ingin melanjutkan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-gradient">
                🗑️ Hapus Semua
            </button>
        </form>
        @endif
        <a href="{{ route('strategi8.create') }}" class="btn btn-primary">
            ➕ Tambah Data
        </a>
        <a href="{{ route('strategi8.import.form') }}" class="btn btn-success">
            📁 Import CSV
        </a>
    </div>
    
    <form method="GET" action="{{ route('strategi8.index') }}" class="search-form" style="display:flex;gap:10px;align-items:end;">
        <div style="flex:1;">
            <select name="year" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:6px;font-size:14px;background:white;">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" {{ request('year') == $availableYear ? 'selected' : '' }}>{{ $availableYear }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;">
            <select name="month" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:6px;font-size:14px;background:white;">
                <option value="">Semua Bulan</option>
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$i] }}</option>
                @endfor
            </select>
        </div>
        <div style="flex:2;">
            <input type="text" name="search" placeholder="Cari nama nasabah, rekening, CIFNO, cabang, unit kerja..." value="{{ request('search') }}" style="width:100%;">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        @if(request('search') || request('month') || request('year'))
            <a href="{{ route('strategi8.index') }}" class="btn btn-warning">✖ Reset</a>
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

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Cabang Induk</th>
                <th>Cabang Induk</th>
                <th>Kode Uker</th>
                <th>Unit Kerja</th>
                <th>CIFNO</th>
                <th>No Rekening</th>
                <th>Nama Nasabah</th>
                <th>Segmentasi</th>
                <th>Jenis Simpanan</th>
                <th>Saldo Last EOM</th>
                <th>Saldo Terupdate</th>
                <th>Delta</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($strategi8 as $index => $item)
            <tr>
                <td>{{ $strategi8->firstItem() + $index }}</td>
                <td>{{ $item->kode_cabang_induk }}</td>
                <td>{{ $item->cabang_induk }}</td>
                <td>{{ $item->kode_uker }}</td>
                <td>{{ $item->unit_kerja }}</td>
                <td>{{ $item->cifno }}</td>
                <td>{{ $item->no_rekening }}</td>
                <td>{{ $item->nama_nasabah }}</td>
                <td>{{ $item->segmentasi }}</td>
                <td>{{ $item->jenis_simpanan }}</td>
                <td>{{ $item->saldo_last_eom }}</td>
                <td>{{ $item->saldo_terupdate }}</td>
                <td>{{ $item->delta }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('strategi8.show', $item->id) }}" class="btn btn-sm btn-info">👁️ View</a>
                        <a href="{{ route('strategi8.edit', $item->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('strategi8.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="text-align: center; padding: 40px;">
                    <p style="color: #999; font-size: 16px;">Tidak ada data strategi 8.</p>
                    <a href="{{ route('strategi8.import.form') }}" class="btn btn-success" style="margin-top: 10px;">Import CSV</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    <p class="pagination-info">Showing {{ $strategi8->firstItem() }} to {{ $strategi8->lastItem() }} of {{ $strategi8->total() }} results</p>
    
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
        @if ($strategi8->onFirstPage())
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">← Previous</span>
        @else
            <a href="{{ $strategi8->previousPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">← Previous</a>
        @endif

        {{-- Show pages 1 to 5 only --}}
        @php
            $currentPage = $strategi8->currentPage();
            $lastPage = $strategi8->lastPage();
            $startPage = 1;
            $endPage = min(5, $lastPage);
        @endphp

        @foreach (range($startPage, $endPage) as $page)
            @php $url = $strategi8->url($page); @endphp
            @if ($page == $currentPage)
                <span style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 1px solid #667eea; border-radius: 4px;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">{{ $page }}</a>
            @endif
        @endforeach

        @if ($strategi8->hasMorePages())
            <a href="{{ $strategi8->nextPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">Next →</a>
        @else
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">Next →</span>
        @endif
    </div>
</div>
@endsection
