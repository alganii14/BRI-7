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

    .btn-warning {
        background-color: #ffc107;
        color: #333;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .info-box p {
        margin: 0;
        color: #555;
        font-size: 14px;
    }
</style>

@php
    $latestTanggalPosisi = \App\Models\Brilink::whereNotNull('tanggal_posisi_data')
                                              ->where('kd_cabang', Auth::user()->kode_kanca ?? '')
                                              ->orderBy('tanggal_posisi_data', 'desc')
                                              ->first();
@endphp

<div class="info-box">
    <p>📊 Data Brilink dengan saldo CASA kurang dari 10 juta untuk cabang Anda</p>
    @if($latestTanggalPosisi && $latestTanggalPosisi->tanggal_posisi_data)
        <p style="margin-top: 8px; font-weight: 600; color: #1976d2;">
            📅 Posisi Data: {{ \Carbon\Carbon::parse($latestTanggalPosisi->tanggal_posisi_data)->format('d F Y') }}
        </p>
    @endif
</div>

<div class="header-actions">
    <div></div>
    
    <form method="GET" action="{{ route('manager-pull-pipeline.brilink-saldo-kurang') }}" class="search-form">
        <input type="text" name="search" placeholder="Cari nama agen, ID agen, no rekening..." value="{{ request('search') }}" style="width:300px;">
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        @if(request('search'))
            <a href="{{ route('manager-pull-pipeline.brilink-saldo-kurang') }}" class="btn btn-warning">✖ Reset</a>
        @endif
    </form>
</div>

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
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td>{{ $data->firstItem() + $index }}</td>
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
            </tr>
            @empty
            <tr>
                <td colspan="12" style="text-align: center; padding: 40px;">
                    <p style="color: #999; font-size: 16px;">Tidak ada data Brilink untuk cabang Anda.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    <p class="pagination-info">Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results</p>
    
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
        @if ($data->onFirstPage())
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">← Previous</span>
        @else
            <a href="{{ $data->previousPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">← Previous</a>
        @endif

        @php
            $currentPage = $data->currentPage();
            $lastPage = $data->lastPage();
            $startPage = 1;
            $endPage = min(5, $lastPage);
        @endphp

        @foreach (range($startPage, $endPage) as $page)
            @if ($page == $currentPage)
                <span style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 1px solid #667eea; border-radius: 4px;">{{ $page }}</span>
            @else
                <a href="{{ $data->url($page) }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">{{ $page }}</a>
            @endif
        @endforeach

        @if ($data->hasMorePages())
            <a href="{{ $data->nextPageUrl() }}" style="padding: 10px 20px; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-decoration: none;">Next →</a>
        @else
            <span style="padding: 10px 20px; background: #f0f0f0; color: #999; border: 1px solid #ddd; border-radius: 4px; cursor: not-allowed;">Next →</span>
        @endif
    </div>
</div>

@endsection
