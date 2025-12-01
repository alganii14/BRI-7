@extends('layouts.app')

@section('title', 'Existing Payroll')
@section('page-title', 'Data Existing Payroll')

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
        @if($existingPayrolls->total() > 0)
        <form action="{{ route('existing-payroll.delete-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data existing payroll ({{ number_format($existingPayrolls->total(), 0, ',', '.') }} baris).\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!\n\nApakah Anda yakin ingin melanjutkan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-gradient">
                🗑️ Hapus Semua
            </button>
        </form>
        @endif
        <a href="{{ route('existing-payroll.create') }}" class="btn btn-primary">
            ➕ Tambah Data
        </a>
        <a href="{{ route('existing-payroll.import.form') }}" class="btn btn-success">
            📁 Import CSV
        </a>
    </div>
    
    <form method="GET" action="{{ route('existing-payroll.index') }}" class="search-form" style="display:flex;gap:10px;align-items:end;">
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
            <input type="text" name="search" placeholder="Cari perusahaan, corporate code, atau cabang..." value="{{ request('search') }}" style="width:100%;">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        @if(request('search') || request('month') || request('year'))
            <a href="{{ route('existing-payroll.index') }}" class="btn btn-warning">✖ Reset</a>
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
    $latestTanggalPosisi = \App\Models\ExistingPayroll::whereNotNull('tanggal_posisi_data')
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
                <th>No</th>
                <th>Kode Cabang Induk</th>
                <th>Cabang Induk</th>
                <th>Corporate Code</th>
                <th>Nama Perusahaan</th>
                <th>Jumlah Rekening</th>
                <th>Saldo Rekening</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($existingPayrolls as $index => $payroll)
            <tr>
                <td>{{ $existingPayrolls->firstItem() + $index }}</td>
                <td>{{ $payroll->kode_cabang_induk }}</td>
                <td>{{ $payroll->cabang_induk }}</td>
                <td>{{ $payroll->corporate_code }}</td>
                <td>{{ $payroll->nama_perusahaan }}</td>
                <td>{{ $payroll->jumlah_rekening }}</td>
                <td>{{ $payroll->saldo_rekening }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('existing-payroll.show', $payroll->id) }}" class="btn btn-sm btn-info">👁️ View</a>
                        <a href="{{ route('existing-payroll.edit', $payroll->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('existing-payroll.destroy', $payroll->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <div style="color: #999; font-size: 16px;">
                        📋 Tidak ada data existing payroll
                    </div>
                    <a href="{{ route('existing-payroll.import.form') }}" class="btn btn-success" style="margin-top: 10px;">Import CSV</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($existingPayrolls->hasPages())
<div class="pagination-wrapper">
    {{ $existingPayrolls->links() }}
    <p class="pagination-info">
        Menampilkan {{ $existingPayrolls->firstItem() }} - {{ $existingPayrolls->lastItem() }} dari {{ number_format($existingPayrolls->total(), 0, ',', '.') }} data
    </p>
</div>
@endif

@endsection
