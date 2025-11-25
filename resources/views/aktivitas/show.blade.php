@extends('layouts.app')

@section('title', 'Detail Pipeline')
@section('page-title', 'Detail Pipeline')

@section('content')
<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .detail-header {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .detail-body {
        padding: 30px;
    }

    .detail-section {
        margin-bottom: 30px;
    }

    .detail-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0066CC;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 15px;
        color: #333;
        font-weight: 500;
    }

    .detail-value.empty {
        color: #999;
        font-style: italic;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
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

    .badge-assigned {
        background-color: #ff9800;
        color: white;
    }

    .badge-self {
        background-color: #4caf50;
        color: white;
    }

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

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
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
        background-color: #ff9800;
        color: white;
    }

    .btn-warning:hover {
        background-color: #e68900;
        transform: translateY(-2px);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }

    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #0066CC;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .info-box p {
        margin: 0;
        color: #666;
        font-size: 14px;
        line-height: 1.6;
    }

    .status-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="detail-card">
    <div class="detail-header">
        <h2>📋 Detail Pipeline #{{ $aktivitas->id }}</h2>
        <span class="badge {{ $aktivitas->tipe == 'assigned' ? 'badge-assigned' : 'badge-self' }}">
            {{ $aktivitas->tipe == 'assigned' ? 'Assigned by Manager' : 'Self Entry' }}
        </span>
    </div>

    <div class="detail-body">
        <!-- Info Box -->
        @if($aktivitas->tipe == 'assigned' && $aktivitas->assignedBy)
        <div class="info-box">
            <p>
                <strong>📌 Aktivitas ini di-assign oleh:</strong> 
                {{ $aktivitas->assignedBy->name }} ({{ $aktivitas->assignedBy->email }})
                pada {{ $aktivitas->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        @endif

        <!-- Data RMFT Section -->
        <div class="detail-section">
            <div class="section-title">👤 Data RMFT</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nama RMFT</div>
                    <div class="detail-value">{{ $aktivitas->nama_rmft }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PN</div>
                    <div class="detail-value">{{ $aktivitas->pn }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Kelompok</div>
                    <div class="detail-value">{{ $aktivitas->kelompok }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tanggal</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($aktivitas->tanggal)->format('d F Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Data Wilayah Section -->
        <div class="detail-section">
            <div class="section-title">🏢 Data Wilayah</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Kode KC</div>
                    <div class="detail-value">{{ $aktivitas->kode_kc }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nama KC</div>
                    <div class="detail-value">{{ $aktivitas->nama_kc }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Kode Uker</div>
                    <div class="detail-value">{{ $aktivitas->kode_uker }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nama Uker</div>
                    <div class="detail-value">{{ $aktivitas->nama_uker }}</div>
                </div>
            </div>
        </div>

        <!-- Data Aktivitas Section -->
        <div class="detail-section">
            <div class="section-title">📊 Data Aktivitas</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Strategy Pull of Pipeline</div>
                    <div class="detail-value">{{ $aktivitas->strategy_pipeline }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Rencana Aktivitas</div>
                    <div class="detail-value">{{ $aktivitas->rencana_aktivitas }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Segmen Nasabah</div>
                    <div class="detail-value">{{ $aktivitas->segmen_nasabah }}</div>
                </div>
            </div>
        </div>

        <!-- Data Nasabah Section -->
        <div class="detail-section">
            <div class="section-title">👥 Data Nasabah</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">CIFNO</div>
                    <div class="detail-value">{{ $aktivitas->nasabah->cifno ?? '-' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">No. Rekening</div>
                    <div class="detail-value">-</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nama Nasabah</div>
                    <div class="detail-value">{{ $aktivitas->nama_nasabah }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Target (RP / Jumlah)</div>
                    <div class="detail-value">{{ $aktivitas->rp_jumlah }}</div>
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        @if($aktivitas->keterangan)
        <div class="detail-section">
            <div class="section-title">📝 Keterangan</div>
            <div class="detail-value">{{ $aktivitas->keterangan }}</div>
        </div>
        @endif

        <!-- Status & Realisasi Section -->
        <div class="detail-section">
            <div class="section-title">✅ Status & Realisasi</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Status Realisasi</div>
                    <div class="status-container">
                        @if($aktivitas->status_realisasi == 'tercapai')
                            <span class="badge badge-success">✓ Tercapai</span>
                        @elseif($aktivitas->status_realisasi == 'tidak_tercapai')
                            <span class="badge badge-danger">✗ Tidak Tercapai</span>
                        @elseif($aktivitas->status_realisasi == 'lebih')
                            <span class="badge badge-info">↑ Lebih dari Target</span>
                        @else
                            <span class="badge badge-warning">⏳ Belum Ada Feedback</span>
                        @endif
                    </div>
                </div>

                @if($aktivitas->status_realisasi)
                <div class="detail-item">
                    <div class="detail-label">Nominal Realisasi</div>
                    <div class="detail-value">{{ $aktivitas->nominal_realisasi ?? '-' }}</div>
                </div>
                @endif

                @if($aktivitas->tanggal_feedback)
                <div class="detail-item">
                    <div class="detail-label">Tanggal Feedback</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($aktivitas->tanggal_feedback)->format('d F Y H:i') }}</div>
                </div>
                @endif
            </div>

            @if($aktivitas->keterangan_realisasi)
            <div class="detail-item" style="margin-top: 15px;">
                <div class="detail-label">Keterangan Realisasi</div>
                <div class="detail-value">{{ $aktivitas->keterangan_realisasi }}</div>
            </div>
            @endif

            @if($aktivitas->latitude && $aktivitas->longitude)
            <div class="detail-item" style="margin-top: 15px;">
                <div class="detail-label">📍 Lokasi Feedback</div>
                <div class="detail-value">
                    Latitude: {{ $aktivitas->latitude }}, Longitude: {{ $aktivitas->longitude }}
                </div>
                <div id="map" style="width: 100%; height: 300px; border-radius: 8px; margin-top: 10px; border: 2px solid #e0e0e0;"></div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('aktivitas.index') }}" class="btn btn-secondary">
                ← Kembali ke Daftar
            </a>

            @if(auth()->user()->isRMFT() && $aktivitas->rmft_id == auth()->user()->rmft_id && !$aktivitas->status_realisasi)
                <a href="{{ route('aktivitas.feedback', $aktivitas->id) }}" class="btn btn-warning">
                    📝 Berikan Feedback
                </a>
            @endif

            @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                @if(auth()->user()->isAdmin() || (auth()->user()->isManager() && $aktivitas->kode_kc == auth()->user()->kode_kanca))
                    <a href="{{ route('aktivitas.edit', $aktivitas->id) }}" class="btn btn-primary">
                        ✏️ Edit Pipeline
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Additional Info Card -->
<div class="detail-card">
    <div class="detail-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <h2>ℹ️ Informasi Tambahan</h2>
    </div>
    <div class="detail-body">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Dibuat Pada</div>
                <div class="detail-value">{{ $aktivitas->created_at->format('d F Y H:i:s') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Terakhir Diupdate</div>
                <div class="detail-value">{{ $aktivitas->updated_at->format('d F Y H:i:s') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">ID Aktivitas</div>
                <div class="detail-value">#{{ $aktivitas->id }}</div>
            </div>
            @if($aktivitas->rmft)
            <div class="detail-item">
                <div class="detail-label">RMFT ID</div>
                <div class="detail-value">#{{ $aktivitas->rmft_id }}</div>
            </div>
            @endif
        </div>
    </div>
</div>


@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($aktivitas->latitude && $aktivitas->longitude)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        const map = L.map('map').setView([{{ $aktivitas->latitude }}, {{ $aktivitas->longitude }}], 15);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add marker
        const marker = L.marker([{{ $aktivitas->latitude }}, {{ $aktivitas->longitude }}]).addTo(map);
        
        // Add popup to marker
        marker.bindPopup(`
            <div style="text-align: center;">
                <strong>📍 Lokasi Feedback</strong><br>
                <small>{{ $aktivitas->nama_rmft }}</small><br>
                <small>{{ $aktivitas->tanggal_feedback ? \Carbon\Carbon::parse($aktivitas->tanggal_feedback)->format('d M Y H:i') : '' }}</small>
            </div>
        `).openPopup();
    });
</script>
@endif
@endpush
