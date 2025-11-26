@extends('layouts.app')

@section('title', 'Validasi Rekap')
@section('page-title', 'Validasi Rekap')

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

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
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
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        padding: 12px;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    table th:nth-child(1),
    table th:nth-child(2),
    table th:nth-child(3),
    table th:nth-child(4),
    table th:nth-child(5),
    table th:nth-child(6),
    table th:nth-child(7) {
        text-align: left;
    }

    table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
        text-align: center;
    }

    table td:nth-child(1),
    table td:nth-child(2),
    table td:nth-child(3),
    table td:nth-child(4),
    table td:nth-child(5),
    table td:nth-child(6),
    table td:nth-child(7) {
        text-align: left;
    }

    table td:nth-child(8),
    table td:nth-child(9) {
        text-align: right;
    }

    table tr:hover {
        background-color: #f8f9fa;
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

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .pagination-wrapper {
        margin-top: 30px;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        text-align: center;
    }
</style>

<div class="page-header">
    <h2>Validasi Rekap Pipeline</h2>
    <p>Kelola data validasi rekap pipeline</p>
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

<div class="card">
    <div class="header-actions">
        <div>
            <h3 style="margin: 0 0 5px 0;">Data Rekap</h3>
            <p style="margin: 0; color: #666; font-size: 14px;">
                Total: <strong style="color: #28a745;">{{ $rekaps->total() }}</strong> data
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('rekap.import.form') }}" class="btn btn-success">📥 Import Data</a>
            @if(auth()->user()->isAdmin())
            <button onclick="openDeleteAllModal()" class="btn btn-danger">
                🗑️ Hapus Semua Data
            </button>
            @endif
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>TANGGAL</th>
                    <th>NAMA KC</th>
                    <th>PN</th>
                    <th>NAMA RMFT</th>
                    <th>NAMA PEMILIK</th>
                    <th>NO REKENING</th>
                    <th>PIPELINE</th>
                    <th>REALISASI</th>
                    <th>KETERANGAN</th>
                    <th>VALIDASI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $item)
                <tr>
                    <td>{{ $loop->iteration + ($rekaps->currentPage() - 1) * $rekaps->perPage() }}</td>
                    <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}</td>
                    <td>{{ $item->nama_kc ?? '-' }}</td>
                    <td>{{ $item->pn ?? '-' }}</td>
                    <td>{{ $item->nama_rmft ?? '-' }}</td>
                    <td>{{ $item->nama_pemilik ?? '-' }}</td>
                    <td>{{ $item->no_rekening ?? '-' }}</td>
                    <td style="text-align: right;">
                        @if($item->pipeline > 0)
                            Rp {{ number_format($item->pipeline, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($item->realisasi > 0)
                            Rp {{ number_format($item->realisasi, 0, ',', '.') }}
                        @else
                            0
                        @endif
                    </td>
                    <td>{{ $item->keterangan && $item->keterangan != '0' ? $item->keterangan : '-' }}</td>
                    <td>{{ $item->validasi ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 40px; color: #666;">
                        Belum ada data rekap. <a href="{{ route('rekap.import.form') }}" style="color: #28a745;">Import data</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rekaps->hasPages())
    <div class="pagination-wrapper">
        {{ $rekaps->links() }}
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
                    Anda yakin ingin menghapus SEMUA data rekap?
                </p>
                <p style="font-size: 14px; color: #666; margin: 0;">
                    Total: <strong>{{ $rekaps->total() }}</strong> data
                </p>
            </div>
            
            <form action="{{ route('rekap.delete-all') }}" method="POST">
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

document.getElementById('deleteAllModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteAllModal();
    }
});
</script>
@endif

@endsection
