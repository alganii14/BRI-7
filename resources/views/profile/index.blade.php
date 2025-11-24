@extends('layouts.app')

@section('title', 'Profil')
@section('page-title', 'Profil Saya')

@section('content')
<style>
    .profile-card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f0f0;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 600;
        margin-right: 20px;
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info h3 {
        margin: 0 0 8px 0;
        color: #333;
    }

    .profile-info .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        background: linear-gradient(135deg, #0066CC 0%, #003D82 100%);
        color: white;
    }

    .form-section {
        margin-bottom: 32px;
    }

    .form-section h4 {
        margin-bottom: 16px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section h4 svg {
        width: 20px;
        height: 20px;
    }

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

    .form-group input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
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

    .btn-warning {
        background-color: #ffc107;
        color: #333;
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

    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert ul {
        margin: 8px 0 0 20px;
    }

    .required {
        color: red;
    }

    .form-help {
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }

    .divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #ddd, transparent);
        margin: 32px 0;
    }
</style>

@if($user->needsPasswordChange())
<div class="alert" style="background-color: #fff3cd; border: 1px solid #ffc107; color: #856404; margin-bottom: 20px;">
    <strong>⚠️ Perhatian!</strong> Anda harus mengubah password default terlebih dahulu sebelum dapat mengakses fitur lainnya.
</div>
@endif

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('warning'))
<div class="alert" style="background-color: #fff3cd; border: 1px solid #ffc107; color: #856404;">
    {{ session('warning') }}
</div>
@endif

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
    <div class="profile-header">
        <div class="profile-avatar">
            @if($user->photo)
                <img src="{{ asset('storage/photos/' . $user->photo) }}" alt="{{ $user->name }}">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <h3>{{ $user->name }}</h3>
            <span class="role-badge">
                @if($user->isManager())
                    MANAGER
                @elseif($user->isRMFT())
                    RMFT
                @else
                    ADMIN
                @endif
            </span>
            @if($user->kode_kanca)
            <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                KC: {{ $user->nama_kanca }} ({{ $user->kode_kanca }})
            </p>
            @endif
        </div>
    </div>

    <!-- Update Profile Form -->
    <div class="form-section">
        <h4>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informasi Profil
        </h4>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="photo" accept="image/*" onchange="previewImage(event)">
                <div class="form-help">Format: JPG, PNG, GIF. Max: 2MB</div>
                @if($user->photo)
                <div style="margin-top: 10px;">
                    <img id="preview" src="{{ asset('storage/photos/' . $user->photo) }}" alt="Preview" style="max-width: 150px; border-radius: 8px; border: 2px solid #ddd;">
                </div>
                @else
                <div style="margin-top: 10px;">
                    <img id="preview" src="" alt="Preview" style="max-width: 150px; border-radius: 8px; border: 2px solid #ddd; display: none;">
                </div>
                @endif
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
            </div>

            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Masukkan email">
            </div>

            @if($user->pernr)
            <div class="form-group">
                <label>PERNR</label>
                <input type="text" value="{{ $user->pernr }}" disabled>
                <div class="form-help">PERNR tidak dapat diubah</div>
            </div>
            @endif

            <button type="submit" class="btn btn-primary">Update Profil</button>
        </form>
    </div>

    <div class="divider"></div>

    <!-- Change Password Form -->
    <div class="form-section">
        <h4>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Ganti Password
        </h4>

        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Password Saat Ini <span class="required">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="current_password" name="current_password" required placeholder="Masukkan password saat ini" style="padding-right: 45px;">
                    <button type="button" onclick="togglePasswordField('current_password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px;">
                        <svg class="eye-icon" width="20" height="20" fill="none" stroke="#666" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Password Baru <span class="required">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="new_password" name="new_password" required placeholder="Masukkan password baru (min. 8 karakter)" style="padding-right: 45px;">
                    <button type="button" onclick="togglePasswordField('new_password')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px;">
                        <svg class="eye-icon" width="20" height="20" fill="none" stroke="#666" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <div class="form-help">Password minimal 8 karakter</div>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru <span class="required">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Konfirmasi password baru" style="padding-right: 45px;">
                    <button type="button" onclick="togglePasswordField('new_password_confirmation')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px;">
                        <svg class="eye-icon" width="20" height="20" fill="none" stroke="#666" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-warning">Ganti Password</button>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function togglePasswordField(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('.eye-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        field.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
@endsection
