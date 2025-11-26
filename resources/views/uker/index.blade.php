@extends('layouts.app')

@section('title', 'Manajemen Uker')
@section('page-title', 'Unit Kerja')

@section('content')
<div class="page-header">
    <h2>Manajemen Unit Kerja (Uker)</h2>
    <p>Kelola data unit kerja BRI Selindo</p>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning">
    {{ session('warning') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    {{ session('error') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-header-actions">
            <form action="{{ route('uker.index') }}" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari Uker..." value="{{ $search ?? '' }}" class="search-input">
                <button type="submit" class="btn btn-search">Cari</button>
            </form>
            <div class="button-group">
                <a href="{{ route('uker.create') }}" class="btn btn-primary">+ Tambah Uker</a>
                <button type="button" class="btn btn-success" onclick="openImportModal()">📁 Import CSV</button>
                @if($ukers->total() > 0)
                <button type="button" class="btn btn-danger" onclick="confirmDeleteAll()">🗑️ Hapus Semua</button>
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Kode Sub Kanca</th>
                    <th>Sub Kanca</th>
                    <th>Segment</th>
                    <th>Kode Kanca</th>
                    <th>Kanca</th>
                    <th>Kanwil</th>
                    <th>Kode Kanwil</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ukers as $index => $uker)
                <tr>
                    <td>{{ $ukers->firstItem() + $index }}</td>
                    <td>{{ $uker->kode_sub_kanca }}</td>
                    <td>{{ $uker->sub_kanca }}</td>
                    <td><span class="badge badge-{{ $uker->segment == 'MIKRO' ? 'primary' : 'secondary' }}">{{ $uker->segment }}</span></td>
                    <td>{{ $uker->kode_kanca }}</td>
                    <td>{{ $uker->kanca }}</td>
                    <td>{{ $uker->kanwil }}</td>
                    <td>{{ $uker->kode_kanwil }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('uker.edit', $uker->id) }}" class="btn-action btn-edit" title="Edit">✏️</a>
                            <form action="{{ route('uker.destroy', $uker->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                        <p style="font-size: 16px; margin-bottom: 10px;">📂 Belum ada data Uker</p>
                        <p style="font-size: 14px;">Klik tombol "Tambah Uker" atau "Import CSV" untuk menambahkan data</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ukers->hasPages())
    <div class="pagination-wrapper" style="text-align: center;">
        <p style="color: #666; font-size: 14px; margin: 0 0 15px 0;">Showing {{ $ukers->firstItem() }} to {{ $ukers->lastItem() }} of {{ $ukers->total() }} results</p>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: wrap;">
            {{-- Previous Button --}}
            @if ($ukers->onFirstPage())
                <span style="padding: 8px 16px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">← Previous</span>
            @else
                <a href="{{ $ukers->appends(request()->query())->previousPageUrl() }}" style="padding: 8px 16px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;">← Previous</a>
            @endif

            {{-- Page Numbers --}}
            @php
                $currentPage = $ukers->currentPage();
                $lastPage = $ukers->lastPage();
                
                // Hitung range halaman yang akan ditampilkan (maksimal 5)
                $maxPages = 5;
                $halfMax = floor($maxPages / 2);
                
                if ($lastPage <= $maxPages) {
                    // Jika total halaman <= 5, tampilkan semua
                    $startPage = 1;
                    $endPage = $lastPage;
                } else {
                    // Jika lebih dari 5 halaman
                    if ($currentPage <= $halfMax) {
                        // Di awal (halaman 1-3)
                        $startPage = 1;
                        $endPage = $maxPages;
                    } elseif ($currentPage >= ($lastPage - $halfMax)) {
                        // Di akhir
                        $startPage = $lastPage - $maxPages + 1;
                        $endPage = $lastPage;
                    } else {
                        // Di tengah
                        $startPage = $currentPage - $halfMax;
                        $endPage = $currentPage + $halfMax;
                    }
                }
            @endphp

            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <span style="padding: 8px 14px; background: #007bff; color: white; border: 1px solid #007bff; border-radius: 4px; font-size: 14px; font-weight: 600; min-width: 40px; text-align: center;">{{ $page }}</span>
                @else
                    <a href="{{ $ukers->appends(request()->query())->url($page) }}" style="padding: 8px 14px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s; min-width: 40px; text-align: center;">{{ $page }}</a>
                @endif
            @endfor

            {{-- Next Button --}}
            @if ($ukers->hasMorePages())
                <a href="{{ $ukers->appends(request()->query())->nextPageUrl() }}" style="padding: 8px 16px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;">Next →</a>
            @else
                <span style="padding: 8px 16px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Import Modal -->
<div id="importModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeImportModal()">&times;</span>
        <h3>Import Data Uker dari CSV</h3>
        <form action="{{ route('uker.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Pilih File CSV</label>
                <input type="file" name="csv_file" accept=".csv" required class="file-input">
                <small style="color: #666; display: block; margin-top: 8px;">
                    Format CSV: Kode Sub Kanca;Sub Kanca;SEGMENT;Kode Kanca;Kanca;Kanwil;Kode Kanwil
                </small>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Batal</button>
                <button type="submit" class="btn btn-success">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete All Form -->
<form id="deleteAllForm" action="{{ route('uker.delete-all') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
    .alert {
        padding: 14px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e0e0e0;
    }

    .card-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .search-form {
        display: flex;
        gap: 8px;
    }

    .search-input {
        padding: 8px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        width: 250px;
    }

    .search-input:focus {
        outline: none;
        border-color: #0066CC;
    }

    .button-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
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

    .btn-success {
        background-color: #4caf50;
        color: white;
    }

    .btn-success:hover {
        background-color: #45a049;
    }

    .btn-danger {
        background-color: #f44336;
        color: white;
    }

    .btn-danger:hover {
        background-color: #da190b;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-search {
        background-color: #0066CC;
        color: white;
    }

    .btn-search:hover {
        background-color: #0052A3;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background-color: #f5f5f5;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: #333;
        border-bottom: 2px solid #e0e0e0;
    }

    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
        color: #555;
    }

    .data-table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-primary {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .badge-secondary {
        background-color: #f5f5f5;
        color: #666;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        background: none;
    }

    .btn-edit:hover {
        background-color: #fff3cd;
    }

    .btn-delete:hover {
        background-color: #ffebee;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        position: relative;
    }

    .modal-content h3 {
        margin-bottom: 20px;
        color: #333;
    }

    .close {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
    }

    .close:hover {
        color: #000;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
    }

    .file-input {
        width: 100%;
        padding: 10px;
        border: 2px dashed #e0e0e0;
        border-radius: 6px;
        cursor: pointer;
    }

    .file-input:hover {
        border-color: #0066CC;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .pagination-wrapper {
        padding: 20px 24px;
        border-top: 1px solid #e0e0e0;
    }

    .pagination-wrapper a:hover {
        background: #0056b3 !important;
        color: white !important;
        border-color: #0056b3 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').style.display = 'block';
    }

    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }

    function confirmDeleteAll() {
        if (confirm('PERINGATAN! Anda akan menghapus SEMUA data Uker. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')) {
            document.getElementById('deleteAllForm').submit();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('importModal');
        if (event.target == modal) {
            closeImportModal();
        }
    }
</script>
@endpush

