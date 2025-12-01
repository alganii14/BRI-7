@extends('layouts.app')

@section('title', 'Merchant EDC Savol Besar')
@section('page-title', 'Data Merchant EDC Savol Besar Casa Kecil (Strategi 1)')

@section('content')
<style>
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; white-space: nowrap; }
    th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; font-size: 14px; }
    tr:hover { background-color: #f5f5f5; }
    .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.3s; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
    .btn-danger-gradient { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .btn-sm { padding: 6px 12px; font-size: 13px; }
    .btn-warning { background-color: #ffc107; color: #333; }
    .btn-danger { background-color: #dc3545; color: white; }
    .actions { display: flex; gap: 8px; }
    .alert { padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; }
    .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .alert-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 10px; }
    .search-form { display: flex; gap: 10px; }
    .search-form input { padding: 8px 16px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
</style>

<div class="header-actions">
    <div style="display: flex; gap: 10px;">
        @if($merchants->total() > 0)
        <form action="{{ route('merchant-savol-edc.delete-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data Merchant EDC ({{ number_format($merchants->total(), 0, ',', '.') }} baris).\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!\n\nApakah Anda yakin ingin melanjutkan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-gradient">🗑️ Hapus Semua</button>
        </form>
        @endif
        <a href="{{ route('merchant-savol-edc.create') }}" class="btn btn-primary">➕ Tambah Data</a>
        <a href="{{ route('merchant-savol-edc.import.form') }}" class="btn btn-success">📁 Import CSV</a>
    </div>
    
    <form method="GET" action="{{ route('merchant-savol-edc.index') }}" class="search-form">
        <input type="text" name="search" placeholder="Cari merchant, CIF, norek..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        @if(request('search'))
            <a href="{{ route('merchant-savol-edc.index') }}" class="btn btn-warning">✖ Reset</a>
        @endif
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@php
    $latestTanggalPosisi = \App\Models\MerchantSavolEdc::whereNotNull('tanggal_posisi_data')
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
                <th>Kode Kanca</th>
                <th>Nama Kanca</th>
                <th>Kode Uker</th>
                <th>Nama Uker</th>
                <th>Nama Merchant</th>
                <th>No Rek</th>
                <th>CIFNO</th>
                <th>Jumlah TID</th>
                <th>Jumlah TRX</th>
                <th>Sales Volume</th>
                <th>Saldo Posisi</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchants as $index => $item)
            <tr>
                <td>{{ $merchants->firstItem() + $index }}</td>
                <td>{{ $item->kode_kanca }}</td>
                <td>{{ $item->nama_kanca }}</td>
                <td>{{ $item->kode_uker }}</td>
                <td>{{ $item->nama_uker }}</td>
                <td>{{ $item->nama_merchant }}</td>
                <td>{{ $item->norek }}</td>
                <td>{{ $item->cifno }}</td>
                <td>{{ $item->jumlah_tid }}</td>
                <td>{{ $item->jumlah_trx }}</td>
                <td>{{ $item->sales_volume }}</td>
                <td>{{ $item->saldo_posisi }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('merchant-savol-edc.edit', $item->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('merchant-savol-edc.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                    <p style="color: #999; font-size: 16px;">Tidak ada data Merchant EDC.</p>
                    <a href="{{ route('merchant-savol-edc.import.form') }}" class="btn btn-success" style="margin-top: 10px;">Import CSV</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 30px; text-align: center;">
    {{ $merchants->links() }}
</div>

@endsection
