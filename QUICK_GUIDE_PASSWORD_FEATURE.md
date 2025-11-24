# Quick Guide - Fitur Password Visibility & Pembatasan Akses

## 🚀 Instalasi

```bash
# 1. Jalankan migration
php artisan migrate

# 2. Test script (opsional)
php test-password-feature.php
```

## 🎯 Fitur Utama

### 1. Toggle Password Visibility
- **Login Page**: Tombol mata di samping input password
- **Profile Page**: Tombol mata di 3 field password (current, new, confirmation)
- **Cara Pakai**: Klik icon mata untuk show/hide password

### 2. Pembatasan Akses
- **Berlaku untuk**: Manager dan RMFT yang belum ganti password
- **Akses Dibatasi**: Hanya bisa akses halaman "Profil Saya"
- **Cara Unlock**: Ganti password di halaman profil

## 📋 Testing Checklist

### Test 1: Password Visibility Toggle
- [ ] Buka `/login`
- [ ] Ketik password, klik icon mata
- [ ] Password terlihat/tersembunyi
- [ ] Buka `/profile`
- [ ] Test toggle di 3 field password

### Test 2: Pembatasan Akses Manager
```sql
-- Reset untuk testing
UPDATE users SET password_changed_at = NULL WHERE role = 'manager' LIMIT 1;
```
- [ ] Login sebagai manager
- [ ] Coba akses `/dashboard` → Redirect ke profil
- [ ] Lihat warning banner di profil
- [ ] Ganti password
- [ ] Akses `/dashboard` lagi → Berhasil

### Test 3: Pembatasan Akses RMFT
```sql
-- Reset untuk testing
UPDATE users SET password_changed_at = NULL WHERE role = 'rmft' LIMIT 1;
```
- [ ] Login sebagai rmft
- [ ] Coba akses `/dashboard` → Redirect ke profil
- [ ] Lihat warning banner di profil
- [ ] Ganti password
- [ ] Akses `/dashboard` lagi → Berhasil

### Test 4: Admin (Tidak Terpengaruh)
- [ ] Login sebagai admin
- [ ] Bisa akses semua fitur (tidak ada pembatasan)

## 🔧 Troubleshooting

### Error: Column 'password_changed_at' not found
```bash
php artisan migrate
```

### User tidak bisa akses fitur setelah ganti password
```sql
-- Cek apakah password_changed_at sudah terisi
SELECT id, name, email, role, password_changed_at FROM users WHERE id = [USER_ID];

-- Jika masih NULL, set manual
UPDATE users SET password_changed_at = NOW() WHERE id = [USER_ID];
```

### Toggle password tidak berfungsi
- Cek console browser untuk error JavaScript
- Pastikan tidak ada AdBlock yang memblokir script
- Clear cache browser (Ctrl+F5)

## 📝 Route yang Diizinkan untuk User Belum Ganti Password

User manager/rmft yang belum ganti password hanya bisa akses:
- `/profile` - Halaman profil
- `/profile/update` - Update profil
- `/profile/password` - Ganti password
- `/logout` - Logout
- `/api/notifications/*` - Notifikasi

Semua route lain akan redirect ke `/profile` dengan pesan warning.

## 🎨 Customization

### Ubah Pesan Warning
Edit file: `app/Http/Middleware/CheckPasswordChanged.php`
```php
return redirect()->route('profile.index')
    ->with('warning', 'Pesan custom Anda di sini');
```

### Tambah Route yang Diizinkan
Edit file: `app/Http/Middleware/CheckPasswordChanged.php`
```php
$allowedRoutes = [
    'profile.index',
    'profile.update',
    'profile.password',
    'logout',
    'api.notifications.count',
    'api.notifications',
    'route.baru.anda', // Tambahkan di sini
];
```

### Ubah Role yang Terpengaruh
Edit file: `app/Models/User.php` method `needsPasswordChange()`
```php
if (!in_array($this->role, ['manager', 'rmft', 'role_lain'])) {
    return false;
}
```

## 📊 Monitoring

### Cek User yang Perlu Ganti Password
```sql
SELECT 
    id, name, email, role, 
    CASE 
        WHEN password_changed_at IS NULL THEN 'Belum Ganti'
        ELSE 'Sudah Ganti'
    END as status
FROM users 
WHERE role IN ('manager', 'rmft')
ORDER BY password_changed_at IS NULL DESC, name;
```

### Statistik
```sql
SELECT 
    role,
    COUNT(*) as total,
    SUM(CASE WHEN password_changed_at IS NULL THEN 1 ELSE 0 END) as belum_ganti,
    SUM(CASE WHEN password_changed_at IS NOT NULL THEN 1 ELSE 0 END) as sudah_ganti
FROM users 
WHERE role IN ('manager', 'rmft')
GROUP BY role;
```

## 🔐 Security Notes

- Password tetap di-hash dengan bcrypt
- Toggle visibility hanya client-side (tidak mengurangi keamanan)
- Middleware mencegah bypass dengan akses URL langsung
- CSRF protection tetap aktif
- Session management tetap aman
