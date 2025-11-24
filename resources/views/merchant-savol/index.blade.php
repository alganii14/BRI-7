@extends('layouts.app')

@section('title', 'Merchant Savol Besar Casa Kecil')
@section('page-title', 'Data Merchant Savol Besar Casa Kecil (Qris & EDC)')

@section('content')
<style>
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
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
        white-space: nowrap;
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
        flex-wrap: wrap;
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
        flex-direction: column;
        gap: 15px;
        margin-bottom: 20px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-form input {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        flex: 1;
        min-width: 200px;
    }

    .search-form select {
        flex: 1;
        min-width: 120px;
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

    @media (max-width: 768px) {
        .btn {
            padding: 8px 12px;
            font-size: 13px;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
        }

        th, td {
            padding: 8px;
            font-size: 12px;
        }

        .search-form input,
        .search-form select {
            width: 100%;
            min-width: unset;
        }

        .action-buttons {
            width: 100%;
        }

        .action-buttons .btn {
            flex: 1;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .btn {
            padding: 8px 10px;
            font-size: 12px;
        }

        th, td {
            padding: 6px;
            font-size: 11px;
        }

        .actions {
            flex-direction: column;
            gap: 4px;
        }

        .actions .btn-sm {
            width: 100%;
        }
    }
</style>

<div class="header-actions">
    <div class="action-buttons">
        @if($merchantSavols->total() > 0)
        <form action="{{ route('merchant-savol.delete-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data merchant savol ({{ number_format($merchantSavols->total(), 0, ',', '.') }} baris).\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!\n\nApakah Anda yakin ingin melanjutkan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-gradient">
                🗑️ Hapus Semua
            </button>
        </form>
        @endif
        <a href="{{ route('merchant-savol.create') }}" class="btn btn-primary">
            ➕ Tambah Data
        </a>
        <a href="{{ route('merchant-savol.import.form') }}" class="btn btn-success">
            📁 Import CSV
        </a>
    </div>
    
    <form method="GET" action="{{ route('merchant-savol.index') }}" class="search-form">
        <div style="flex:1;min-width:120px;">
            <select name="year" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:6px;font-size:14px;background:white;">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" {{ request('year') == $availableYear ? 'selected' : '' }}>{{ $availableYear }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:120px;">
            <select name="month" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:6px;font-size:14px;background:white;">
                <option value="">Semua Bulan</option>
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$i] }}</option>
                @endfor
            </select>
        </div>
        <input type="text" name="search" placeholder="Cari merchant, TID/Store ID, rekening, CIF, atau unit..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        @if(request('search') || request('month') || request('year'))
            <a href="{{ route('merchant-savol.index') }}" class="btn btn-warning">✖ Reset</a>
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
                <th>Kode Kanca</th>
                <th>Kanca</th>
                <th>Kode Uker</th>
                <th>Uker</th>
                <th>Jenis Merchant</th>
                <th>TID / Store ID</th>
                <th>Nama Merchant</th>
                <th>No. Rekening</th>
                <th>CIF</th>
                <th>Savol Bulan Lalu</th>
                <th>CASA Akhir Bulan</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchantSavols as $index => $item)
            <tr>
                <td>{{ $merchantSavols->firstItem() + $index }}</td>
                <td>{{ $item->kode_kanca }}</td>
                <td>{{ $item->kanca }}</td>
                <td>{{ $item->kode_uker }}</td>
                <td>{{ $item->uker }}</td>
                <td>{{ $item->jenis_merchant }}</td>
                <td>{{ $item->tid_store_id }}</td>
                <td>{{ $item->nama_merchant }}</td>
                <td>{{ $item->norekening }}</td>
                <td>{{ $item->cif }}</td>
                <td>{{ $item->savol_bulan_lalu }}</td>
                <td>{{ $item->casa_akhir_bulan }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('merchant-savol.show', $item->id) }}" class="btn btn-sm btn-info">👁️ View</a>
                        <a href="{{ route('merchant-savol.edit', $item->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('merchant-savol.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                    <p style="color: #999; font-size: 16px;">Tidak ada data merchant savol.</p>
                    <a href="{{ route('merchant-savol.import.form') }}" class="btn btn-success" style="margin-top: 10px;">Import CSV</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    <p class="pagination-info">Showing {{ $merchantSavols->firstItem() }} to {{ $merchantSavols->lastItem() }} of {{ $merchantSavols->total() }} results</p>
    
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
        @if ($merchantSavols->onFirstPage())
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">← Previous</span>
        @else
            <a href="{{ $merchantSavols->previousPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">← Previous</a>
        @endif

        {{-- Show pages 1 to 5 only --}}
        @php
            $currentPage = $merchantSavols->currentPage();
            $lastPage = $merchantSavols->lastPage();
            $startPage = 1;
            $endPage = min(5, $lastPage);
        @endphp

        @foreach (range($startPage, $endPage) as $page)
            @php $url = $merchantSavols->url($page); @endphp
            @if ($page == $currentPage)
                <span style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 1px solid #667eea; border-radius: 4px;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">{{ $page }}</a>
            @endif
        @endforeach

        @if ($merchantSavols->hasMorePages())
            <a href="{{ $merchantSavols->nextPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">Next →</a>
        @else
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">Next →</span>
        @endif
    </div>
</div>
@endsection
