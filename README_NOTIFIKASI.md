# 🔔 Fitur Notifikasi Password Default

## Quick Start

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Reset Status untuk Testing
```bash
php artisan db:seed --class=TestPasswordSeeder
```

### 3. Login dan Test
- Login sebagai **Manager** atau **RMFT** → Lihat notifikasi di icon bell
- Login sebagai **Admin** → Buka menu "Akun" untuk melihat status password

## Fitur

✅ Notifikasi real-time untuk Manager & RMFT dengan password default  
✅ Badge indicator di icon bell navbar  
✅ Status password tracking di halaman Akun (Admin)  
✅ Auto-refresh notifikasi setiap 5 menit  
✅ Responsive design untuk mobile  

## File Penting

### Backend
- `app/Models/User.php` - Method `needsPasswordChange()`
- `app/Http/Controllers/NotificationController.php` - API notifikasi
- `app/Http/Controllers/ProfileController.php` - Update password
- `app/Http/Controllers/AkunController.php` - Manage akun
- `routes/web.php` - Route API notifikasi

### Frontend
- `resources/views/layouts/app.blade.php` - Komponen notifikasi
- `resources/views/akun/index.blade.php` - Status password

### Database
- `database/migrations/2025_11_24_104201_add_password_changed_at_to_users_table.php`

## API Endpoints

```
GET /api/notifications/count  → { "count": 1 }
GET /api/notifications        → { "notifications": [...] }
```

## Dokumentasi Lengkap

📖 [FITUR_NOTIFIKASI_PASSWORD.md](FITUR_NOTIFIKASI_PASSWORD.md) - Dokumentasi lengkap fitur  
🧪 [CARA_TESTING_NOTIFIKASI.md](CARA_TESTING_NOTIFIKASI.md) - Panduan testing  
🏗️ [STRUKTUR_KODE_NOTIFIKASI.md](STRUKTUR_KODE_NOTIFIKASI.md) - Struktur kode detail  
👁️ [preview_notifikasi.html](preview_notifikasi.html) - Preview visual fitur  

## Screenshot

### Notifikasi di Navbar
![Notifikasi](preview_notifikasi.html)

### Status Password di Halaman Akun
- ✓ **Sudah Diubah** (Badge Hijau) - User sudah mengubah password
- ⚠ **Password Default** (Badge Kuning) - User belum mengubah password

## Troubleshooting

### Badge tidak muncul?
1. Cek migration: `php artisan migrate:status`
2. Cek seeder: `php artisan db:seed --class=TestPasswordSeeder`
3. Cek console browser untuk error JavaScript

### Notifikasi tidak hilang setelah ubah password?
1. Refresh halaman
2. Cek database: `SELECT password_changed_at FROM users WHERE id = ?`
3. Clear browser cache

## Support

Jika ada pertanyaan atau issue, silakan buka dokumentasi lengkap di file-file di atas.
