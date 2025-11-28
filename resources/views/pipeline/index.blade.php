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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-form {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 13px;
    }

    .form-group select,
    .form-group input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }
</style>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="header-actions">
    <div>
        <a href="{{ route('pipeline.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 4px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pipeline
        </a>
    </div>
    @if(auth()->user()->isAdmin())
    <div>
        <form action="{{ route('pipeline.delete-all') }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA data pipeline? Tindakan ini tidak dapat dibatalkan!');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 4px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Semua Data
            </button>
        </form>
    </div>
    @endif
</div>

<!-- Filter Form -->
<form method="GET" action="{{ route('pipeline.index') }}" class="filter-form">
    <div class="filter-row">
        @if(auth()->user()->isAdmin())
        <div class="form-group">
            <label>Kantor Cabang</label>
            <select name="kode_kc" class="form-control">
                <option value="">Semua KC</option>
                @foreach($listKC as $kc)
                    <option value="{{ $kc->kode_kc }}" {{ request('kode_kc') == $kc->kode_kc ? 'selected' : '' }}>
                        {{ $kc->nama_kc }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <div class="form-group">
            <label>Unit Kerja</label>
            <select name="kode_uker" class="form-control">
                <option value="">Semua Unit</option>
                @foreach($listUnit as $unit)
                    <option value="{{ $unit->kode_uker }}" {{ request('kode_uker') == $unit->kode_uker ? 'selected' : '' }}>
                        {{ $unit->nama_uker }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Tanggal Dari</label>
            <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
        </div>
        
        <div class="form-group">
            <label>Tanggal Sampai</label>
            <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
        </div>
    </div>
    
    <div style="display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('pipeline.index') }}" class="btn btn-secondary btn-sm">Reset</a>
    </div>
</form>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                @if(auth()->user()->isAdmin())
                <th>KC</th>
                @endif
                <th>Unit</th>
                <th>Strategi</th>
                <th>Kategori</th>
                <th>Nasabah</th>
                <th>Tipe</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pipelines as $index => $pipeline)
            <tr>
                <td>{{ $pipelines->firstItem() + $index }}</td>
                <td>{{ $pipeline->tanggal->format('d/m/Y') }}</td>
                @if(auth()->user()->isAdmin())
                <td>{{ $pipeline->nama_kc }}</td>
                @endif
                <td>{{ $pipeline->nama_uker }}</td>
                <td>{{ $pipeline->strategy_pipeline }}</td>
                <td>{{ $pipeline->kategori_strategi }}</td>
                <td>{{ $pipeline->nama_nasabah }}</td>
                <td>
                    @if($pipeline->tipe == 'lama')
                        <span class="badge badge-info">Di Dalam Pipeline</span>
                    @else
                        <span class="badge badge-warning">Di Luar Pipeline</span>
                    @endif
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('pipeline.show', $pipeline->id) }}" class="btn btn-info btn-sm">Detail</a>
                        <a href="{{ route('pipeline.edit', $pipeline->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('pipeline.destroy', $pipeline->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->isAdmin() ? '9' : '8' }}" style="text-align: center; padding: 40px;">
                    Tidak ada data pipeline
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 20px;">
    {{ $pipelines->links() }}
</div>

@endsection
