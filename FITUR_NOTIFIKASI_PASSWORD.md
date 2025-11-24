# Fitur Notifikasi Password Default

## Deskripsi
Fitur ini mengingatkan user dengan role **Manager** dan **RMFT** untuk mengubah password default mereka. Admin dapat melihat status password di halaman Akun.

## Fitur Utama

### 1. Notifikasi untuk Manager & RMFT
- Icon bell di navbar menampilkan badge merah jika ada notifikasi
- Dropdown notifikasi menampilkan peringatan untuk mengubah password default
- Link langsung ke halaman profil untuk mengubah password

### 2. Status Password di Halaman Akun (Admin)
- Admin dapat melihat kolom "STATUS PASSWORD" di tabel Manager dan RMFT
- Status ditampilkan dengan badge:
  - ✓ **Sudah Diubah** (hijau) - menampilkan tanggal perubahan
  - ⚠ **Password Default** (kuning) - belum pernah mengubah password

### 3. Tracking Password Change
- Sistem mencatat waktu perubahan password di kolom `password_changed_at`
- Saat user mengubah password melalui halaman profil, timestamp akan diupdate
- Saat admin membuat akun baru atau reset password, status kembali ke "Password Default"

## Cara Kerja

### Untuk Manager & RMFT:
1. Login ke sistem
2. Jika belum pernah mengubah password, akan muncul notifikasi di icon bell
3. Klik icon bell untuk melihat detail notifikasi
4. Klik "Ubah Password" untuk ke halaman profil
5. Ubah password di halaman profil
6. Notifikasi akan hilang setelah password berhasil diubah

### Untuk Admin:
1. Buka menu "Akun" di sidebar
2. Lihat kolom "STATUS PASSWORD" di tabel Manager dan RMFT
3. Badge kuning menandakan user belum mengubah password default
4. Badge hijau menandakan user sudah mengubah password (dengan tanggal)

## File yang Dimodifikasi

### Database
- `database/migrations/2025_11_24_104201_add_password_changed_at_to_users_table.php` - Migration untuk kolom baru

### Models
- `app/Models/User.php` - Menambahkan method `needsPasswordChange()`

### Controllers
- `app/Http/Controllers/NotificationController.php` - Controller baru untuk notifikasi
- `app/Http/Controllers/ProfileController.php` - Update untuk set `password_changed_at`
- `app/Http/Controllers/AkunController.php` - Update untuk reset `password_changed_at`

### Routes
- `routes/web.php` - Menambahkan route API notifikasi

### Views
- `resources/views/layouts/app.blade.php` - Menambahkan komponen notifikasi di navbar
- `resources/views/akun/index.blade.php` - Menambahkan kolom status password

## Testing

### Reset Status Password untuk Testing:
```bash
php artisan db:seed --class=TestPasswordSeeder
```

Perintah ini akan mereset `password_changed_at` menjadi null untuk semua Manager dan RMFT, sehingga mereka akan menerima notifikasi.

## API Endpoints

### Get Notification Count
```
GET /api/notifications/count
Response: { "count": 1 }
```

### Get Notifications
```
GET /api/notifications
Response: {
  "notifications": [
    {
      "id": "password-change",
      "type": "warning",
      "title": "Ubah Password Default",
      "message": "Untuk keamanan akun Anda, silakan ubah password default Anda.",
      "link": "/profile",
      "link_text": "Ubah Password"
    }
  ]
}
```

## Keamanan
- Password selalu di-hash menggunakan bcrypt
- Notifikasi hanya muncul untuk role yang sesuai (manager, rmft)
- Admin tidak menerima notifikasi password (diasumsikan sudah aman)
- Timestamp perubahan password dicatat untuk audit

## Catatan
- Notifikasi akan refresh otomatis setiap 5 menit
- Notifikasi hilang setelah user mengubah password
- Admin dapat melihat siapa saja yang belum mengubah password default
