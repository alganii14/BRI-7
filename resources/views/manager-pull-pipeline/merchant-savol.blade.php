@extends('layouts.app')

@section('title', 'Merchant Savol')
@section('page-title', 'Merchant Savol Besar Casa Kecil (Qris & EDC)')

@section('content')
<style>
    .read-only-badge {
        display: inline-block;
        padding: 6px 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .search-box {
        margin-bottom: 20px;
    }

    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 300px;
        font-size: 14px;
    }

    .btn-search {
        padding: 10px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        margin-left: 10px;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
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

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #666;
    }
</style>

<div class="card">
    <span class="read-only-badge">📊 View Only - Read-Only Access</span>
    
    <div class="search-box">
        <form action="{{ route('manager-pull-pipeline.merchant-savol') }}" method="GET">
            <input type="text" name="search" placeholder="Cari norekening, nama merchant, atau TID..." value="{{ request('search') }}">
            <button type="submit" class="btn-search">🔍 Cari</button>
            @if(request('search'))
                <a href="{{ route('manager-pull-pipeline.merchant-savol') }}" class="btn-search" style="background: #6c757d;">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        @if($data->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        @if(auth()->user()->isAdmin())
                        <th>Norekening</th>
                        @endif
                        <th>Nama Merchant</th>
                        <th>Jenis Merchant</th>
                        <th>TID/Store ID</th>
                        @if(auth()->user()->isAdmin())
                        <th>CIF</th>
                        @endif
                        <th>Kode KC</th>
                        <th>Nama KC</th>
                        <th>Uker</th>
                        <th>Savol Bulan Lalu</th>
                        <th>Casa Akhir Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $item)
                        <tr>
                            <td>{{ $data->firstItem() + $index }}</td>
                            @if(auth()->user()->isAdmin())
                            <td>{{ $item->norekening }}</td>
                            @endif
                            <td>{{ $item->nama_merchant }}</td>
                            <td>{{ $item->jenis_merchant }}</td>
                            <td>{{ $item->tid_store_id }}</td>
                            @if(auth()->user()->isAdmin())
                            <td>{{ $item->cif }}</td>
                            @endif
                            <td>{{ $item->kode_kanca }}</td>
                            <td>{{ $item->kanca }}</td>
                            <td>{{ $item->uker }}</td>
                            <td>{{ $item->savol_bulan_lalu }}</td>
                            <td>{{ $item->casa_akhir_bulan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data ditemukan untuk KC Anda.</p>
            </div>
        @endif
    </div>

    @if($data->hasPages())
        <div class="pagination-wrapper">
            {{ $data->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
