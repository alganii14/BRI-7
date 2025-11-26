@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<style>
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
    }

    table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }

    table tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-manager {
        background-color: #0066CC;
        color: white;
    }

    .badge-rmft {
        background-color: #4caf50;
        color: white;
    }

    .badge-admin {
        background-color: #ff5722;
        color: white;
    }

    .badge-success {
        background-color: #4caf50;
        color: white;
    }

    .badge-warning {
        background-color: #ff9800;
        color: white;
    }

    .badge-online {
        background-color: #4caf50;
        color: white;
    }

    .badge-offline {
        background-color: #9e9e9e;
        color: white;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-top: 32px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #0066CC;
    }

    .section-title:first-child {
        margin-top: 0;
    }

    .info-box {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .info-item {
        text-align: center;
    }

    .info-item .number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .info-item .label {
        font-size: 13px;
        opacity: 0.9;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-warning {
        background-color: #ffc107;
        color: #000;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-sm {
        padding: 4px 12px;
        font-size: 12px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .pagination-wrapper a:hover {
        background: #0056b3 !important;
        color: white !important;
        border-color: #0056b3 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
    }
</style>

<div class="page-header">
    <h2>Manajemen Akun</h2>
    <p>Kelola akun Manager dan RMFT</p>
    @if(auth()->user()->isAdmin())
    <div style="margin-top: 16px;">
        <a href="{{ route('akun.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Akun Baru
        </a>
    </div>
    @endif
</div>

<div class="info-box">
    <div class="info-item">
        <div class="number">{{ $managers->total() }}</div>
        <div class="label">Akun Manager</div>
    </div>
    <div class="info-item">
        <div class="number">{{ $rmfts->total() }}</div>
        <div class="label">Akun RMFT</div>
    </div>
    <div class="info-item">
        <div class="number">{{ $managers->total() + $rmfts->total() }}</div>
        <div class="label">Total Akun</div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="card">
    <div class="section-title">🔑 Akun Admin</div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>EMAIL</th>
                    <th>ROLE</th>
                    <th>DIBUAT</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $admins = \App\Models\User::where('role', 'admin')->orderBy('name', 'asc')->get();
                @endphp
                @forelse($admins as $admin)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td><span class="badge badge-admin">ADMIN</span></td>
                    <td>{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('akun.edit', $admin->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            @if($admin->id !== auth()->id())
                            <form action="{{ route('akun.destroy', $admin->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                            @else
                            <button class="btn btn-danger btn-sm" disabled title="Tidak dapat menghapus akun sendiri">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                        Tidak ada akun admin
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="section-title">👤 Akun Manager</div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>EMAIL</th>
                    <th>ROLE</th>
                    @if(auth()->user()->isAdmin())
                    <th>STATUS ONLINE</th>
                    <th>STATUS PASSWORD</th>
                    @endif
                    <th>DIBUAT</th>
                    @if(auth()->user()->isAdmin())
                    <th>AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($managers as $manager)
                <tr>
                    <td>{{ $loop->iteration + ($managers->currentPage() - 1) * $managers->perPage() }}</td>
                    <td>{{ $manager->name }}</td>
                    <td>{{ $manager->email }}</td>
                    <td><span class="badge badge-manager">MANAGER</span></td>
                    @if(auth()->user()->isAdmin())
                    <td>
                        @if($manager->isOnline())
                            <span class="badge badge-online">🟢 Online</span>
                            <br><small style="color: #666;">Aktif {{ $manager->last_activity->diffForHumans() }}</small>
                        @else
                            <span class="badge badge-offline">⚫ Offline</span>
                            @if($manager->last_activity)
                            <br><small style="color: #666;">Terakhir {{ $manager->last_activity->diffForHumans() }}</small>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($manager->password_changed_at)
                            <span class="badge badge-success">✓ Sudah Diubah</span>
                            <br><small style="color: #666;">{{ $manager->password_changed_at->format('d/m/Y H:i') }}</small>
                        @else
                            <span class="badge badge-warning">⚠ Password Default</span>
                        @endif
                    </td>
                    @endif
                    <td>{{ $manager->created_at->format('d/m/Y H:i') }}</td>
                    @if(auth()->user()->isAdmin())
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('akun.edit', $manager->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('akun.destroy', $manager->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? '8' : '5' }}" style="text-align: center; padding: 40px; color: #666;">
                        Tidak ada akun manager
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($managers->hasPages())
    <div class="pagination-wrapper" style="margin-top: 30px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center;">
        <p style="color: #666; font-size: 14px; margin: 0 0 15px 0;">Showing {{ $managers->firstItem() }} to {{ $managers->lastItem() }} of {{ $managers->total() }} results</p>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: wrap;">
            {{-- Previous Button --}}
            @if ($managers->onFirstPage())
                <span style="padding: 8px 16px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">← Previous</span>
            @else
                <a href="{{ $managers->previousPageUrl() }}" style="padding: 8px 16px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;">← Previous</a>
            @endif

            {{-- Page Numbers --}}
            @php
                $currentPage = $managers->currentPage();
                $lastPage = $managers->lastPage();
                
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
                    <a href="{{ $managers->url($page) }}" style="padding: 8px 14px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s; min-width: 40px; text-align: center;">{{ $page }}</a>
                @endif
            @endfor

            {{-- Next Button --}}
            @if ($managers->hasMorePages())
                <a href="{{ $managers->nextPageUrl() }}" style="padding: 8px 16px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;">Next →</a>
            @else
                <span style="padding: 8px 16px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

<div class="card">
    <div class="section-title">👥 Akun RMFT</div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>PERNR</th>
                    <th>NAMA</th>
                    <th>EMAIL</th>
                    <th>KANCA</th>
                    <th>KELOMPOK</th>
                    <th>ROLE</th>
                    @if(auth()->user()->isAdmin())
                    <th>STATUS ONLINE</th>
                    <th>STATUS PASSWORD</th>
                    @endif
                    <th>DIBUAT</th>
                    @if(auth()->user()->isAdmin())
                    <th>AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($rmfts as $rmft)
                <tr>
                    <td>{{ $loop->iteration + ($rmfts->currentPage() - 1) * $rmfts->perPage() }}</td>
                    <td><strong>{{ $rmft->pernr ?? '-' }}</strong></td>
                    <td>{{ $rmft->name }}</td>
                    <td>{{ $rmft->email }}</td>
                    <td>{{ $rmft->rmftData->kanca ?? '-' }}</td>
                    <td>{{ $rmft->rmftData->kelompok_jabatan ?? '-' }}</td>
                    <td><span class="badge badge-rmft">RMFT</span></td>
                    @if(auth()->user()->isAdmin())
                    <td>
                        @if($rmft->isOnline())
                            <span class="badge badge-online">🟢 Online</span>
                            <br><small style="color: #666;">Aktif {{ $rmft->last_activity->diffForHumans() }}</small>
                        @else
                            <span class="badge badge-offline">⚫ Offline</span>
                            @if($rmft->last_activity)
                            <br><small style="color: #666;">Terakhir {{ $rmft->last_activity->diffForHumans() }}</small>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($rmft->password_changed_at)
                            <span class="badge badge-success">✓ Sudah Diubah</span>
                            <br><small style="color: #666;">{{ $rmft->password_changed_at->format('d/m/Y H:i') }}</small>
                        @else
                            <span class="badge badge-warning">⚠ Password Default</span>
                        @endif
                    </td>
                    @endif
                    <td>{{ $rmft->created_at->format('d/m/Y H:i') }}</td>
                    @if(auth()->user()->isAdmin())
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('akun.edit', $rmft->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('akun.destroy', $rmft->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? '11' : '8' }}" style="text-align: center; padding: 40px; color: #666;">
                        Tidak ada akun RMFT
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rmfts->hasPages())
    <div class="pagination-wrapper" style="margin-top: 30px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center;">
        <p style="color: #666; font-size: 14px; margin: 0 0 15px 0;">Showing {{ $rmfts->firstItem() }} to {{ $rmfts->lastItem() }} of {{ $rmfts->total() }} results</p>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: wrap;">
            {{-- Previous Button --}}
            @if ($rmfts->onFirstPage())
                <span style="padding: 8px 16px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">← Previous</span>
            @else
                <a href="{{ $rmfts->previousPageUrl() }}" style="padding: 8px 16px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;">← Previous</a>
            @endif

            {{-- Page Numbers --}}
            @php
                $currentPage = $rmfts->currentPage();
                $lastPage = $rmfts->lastPage();
                
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
                    <a href="{{ $rmfts->url($page) }}" style="padding: 8px 14px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s; min-width: 40px; text-align: center;">{{ $page }}</a>
                @endif
            @endfor

            {{-- Next Button --}}
            @if ($rmfts->hasMorePages())
                <a href="{{ $rmfts->nextPageUrl() }}" style="padding: 8px 16px; background: white; color: #007bff; border: 1px solid #dee2e6; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;">Next →</a>
            @else
                <span style="padding: 8px 16px; background: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-size: 14px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // Auto-refresh status online setiap 30 detik
    setInterval(function() {
        location.reload();
    }, 30000); // 30 detik
</script>
@endpush
