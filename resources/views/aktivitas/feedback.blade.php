@extends('layouts.app')

@section('title', 'Feedback Aktivitas')
@section('page-title', 'Feedback Aktivitas')

@section('content')
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #0066CC;
    }

    .form-group input:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }

    .btn {
        padding: 10px 24px;
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

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert ul {
        margin: 8px 0 0 20px;
    }

    .info-box {
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .info-box h4 {
        margin: 0 0 12px 0;
        font-size: 18px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        opacity: 0.9;
    }

    .info-value {
        font-weight: 600;
    }

    .required {
        color: red;
    }

    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .radio-option:hover {
        border-color: #0066CC;
        background-color: #f8f9ff;
    }

    .radio-option input[type="radio"] {
        margin-right: 12px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .radio-option label {
        cursor: pointer;
        margin: 0;
        font-size: 15px;
    }

    .radio-option.selected {
        border-color: #0066CC;
        background-color: #f8f9ff;
    }

    #nominal_field {
        display: none;
    }

    .target-info {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        color: #856404;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .target-info strong {
        font-size: 18px;
    }

    .location-info {
        background-color: #e7f3ff;
        border: 1px solid #2196F3;
        color: #0d47a1;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .location-info.error {
        background-color: #ffebee;
        border-color: #f44336;
        color: #c62828;
    }

    .location-info.success {
        background-color: #e8f5e9;
        border-color: #4caf50;
        color: #2e7d32;
    }

    .location-btn {
        background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .location-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
    }

    .location-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
</style>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Error:</strong>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="info-box">
        <h4>Informasi Aktivitas</h4>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($aktivitas->tanggal)->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Rencana Aktivitas</span>
            <span class="info-value">{{ $aktivitas->rencana_aktivitas }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nasabah</span>
            <span class="info-value">{{ $aktivitas->nama_nasabah }}</span>
        </div>
    </div>

    <div class="target-info">
        <strong>Target: Rp {{ number_format($aktivitas->rp_jumlah, 0, ',', '.') }}</strong>
        <p style="margin: 8px 0 0 0;">Apakah target ini tercapai?</p>
    </div>

    <div class="location-info" id="location-info">
        <strong>📍 Lokasi Anda</strong>
        <p style="margin: 8px 0 0 0;" id="location-status">Klik tombol di bawah untuk mengambil lokasi Anda</p>
        <button type="button" class="location-btn" id="get-location-btn" onclick="getLocation()">
            <span>📍</span> Ambil Lokasi Saya
        </button>
    </div>

    <form action="{{ route('aktivitas.storeFeedback', $aktivitas->id) }}" method="POST" id="feedback-form">
        @csrf

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <div class="form-group">
            <label>Status Realisasi <span class="required">*</span></label>
            <div class="radio-group">
                <div class="radio-option" onclick="selectStatus('tercapai', this)">
                    <input type="radio" name="status_realisasi" id="tercapai" value="tercapai" {{ old('status_realisasi') == 'tercapai' ? 'checked' : '' }} required>
                    <label for="tercapai">✅ Tercapai - Sesuai target (Rp {{ number_format($aktivitas->rp_jumlah, 0, ',', '.') }})</label>
                </div>

                <div class="radio-option" onclick="selectStatus('tidak_tercapai', this)">
                    <input type="radio" name="status_realisasi" id="tidak_tercapai" value="tidak_tercapai" {{ old('status_realisasi') == 'tidak_tercapai' ? 'checked' : '' }} required>
                    <label for="tidak_tercapai">❌ Tidak Tercapai - Kurang dari target</label>
                </div>

                <div class="radio-option" onclick="selectStatus('lebih', this)">
                    <input type="radio" name="status_realisasi" id="lebih" value="lebih" {{ old('status_realisasi') == 'lebih' ? 'checked' : '' }} required>
                    <label for="lebih">🎉 Melebihi Target - Lebih dari target</label>
                </div>
            </div>
        </div>

        <div class="form-group" id="nominal_field">
            <label>Nominal Realisasi <span class="required">*</span></label>
            <input type="text" name="nominal_realisasi" id="nominal_realisasi" value="{{ old('nominal_realisasi') }}" placeholder="Contoh: 5000000">
            <div style="font-size: 12px; color: #666; margin-top: 4px;">
                Masukkan nominal realisasi yang sebenarnya tercapai
            </div>
        </div>

        <div class="form-group">
            <label>Keterangan (Opsional)</label>
            <textarea name="keterangan_realisasi" rows="4" placeholder="Tambahkan keterangan jika diperlukan">{{ old('keterangan_realisasi') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Feedback</button>
            <a href="{{ route('aktivitas.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
    let locationObtained = false;

    function getLocation() {
        const btn = document.getElementById('get-location-btn');
        const locationInfo = document.getElementById('location-info');
        const locationStatus = document.getElementById('location-status');
        
        if (!navigator.geolocation) {
            locationInfo.classList.add('error');
            locationStatus.textContent = '❌ Browser Anda tidak mendukung geolokasi';
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> Mengambil lokasi...';
        locationStatus.textContent = 'Mohon tunggu, sedang mengambil lokasi Anda...';
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
                
                locationInfo.classList.remove('error');
                locationInfo.classList.add('success');
                locationStatus.innerHTML = `✅ Lokasi berhasil didapatkan!<br>
                    <small>Latitude: ${latitude.toFixed(6)}, Longitude: ${longitude.toFixed(6)}</small>`;
                btn.innerHTML = '<span>✓</span> Lokasi Tersimpan';
                btn.style.background = '#4caf50';
                locationObtained = true;
            },
            function(error) {
                btn.disabled = false;
                btn.innerHTML = '<span>📍</span> Coba Lagi';
                locationInfo.classList.add('error');
                
                let errorMsg = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = '❌ Anda menolak permintaan lokasi. Mohon izinkan akses lokasi di browser Anda.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = '❌ Informasi lokasi tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        errorMsg = '❌ Waktu permintaan lokasi habis. Coba lagi.';
                        break;
                    default:
                        errorMsg = '❌ Terjadi kesalahan saat mengambil lokasi.';
                }
                locationStatus.textContent = errorMsg;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // Validasi form sebelum submit
    document.getElementById('feedback-form').addEventListener('submit', function(e) {
        if (!locationObtained) {
            e.preventDefault();
            alert('⚠️ Mohon ambil lokasi Anda terlebih dahulu sebelum mengirim feedback!');
            document.getElementById('location-info').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
    });

    function selectStatus(status, element) {
        // Remove selected class from all options
        document.querySelectorAll('.radio-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Add selected class to clicked option
        element.classList.add('selected');
        
        // Check the radio button
        document.getElementById(status).checked = true;
        
        // Show/hide nominal field
        const nominalField = document.getElementById('nominal_field');
        const nominalInput = document.getElementById('nominal_realisasi');
        
        if (status === 'tercapai') {
            nominalField.style.display = 'none';
            nominalInput.required = false;
            nominalInput.value = '';
        } else {
            nominalField.style.display = 'block';
            nominalInput.required = true;
        }
    }
    
    // Initialize on page load if there's old input
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[name="status_realisasi"]:checked');
        if (checked) {
            const option = checked.closest('.radio-option');
            if (option) {
                option.classList.add('selected');
            }
            selectStatus(checked.value, option);
        }
    });
</script>
@endsection
