# Fitur Password Visibility Toggle dan Pembatasan Akses

## Deskripsi
Fitur ini menambahkan:
1. **Toggle Password Visibility**: Tombol untuk menampilkan/menyembunyikan password pada form login dan profil
2. **Pembatasan Akses**: Manager dan RMFT yang belum mengubah password default hanya bisa mengakses halaman "Profil Saya"

## Komponen yang Dimodifikasi/Ditambahkan

### 1. View Login (`resources/views/auth/login.blade.php`)
- Menambahkan tombol toggle visibility pada input password
- Icon mata berubah saat password ditampilkan/disembunyikan
- JavaScript untuk toggle tipe input antara `password` dan `text`

### 2. View Profil (`resources/views/profile/index.blade.php`)
- Menambahkan tombol toggle visibility pada 3 input password:
  - Password Saat Ini
  - Password Baru
  - Konfirmasi Password Baru
- Menambahkan notifikasi warning untuk user yang belum ganti password
- JavaScript untuk toggle visibility setiap field password

### 3. Middleware (`app/Http/Middleware/CheckPasswordChanged.php`)
**Fungsi**: Memeriksa apakah user manager/rmft sudah mengubah password default

**Logika**:
- Jika user adalah manager atau rmft DAN `password_changed_at` masih `NULL`
- Maka user hanya bisa mengakses route:
  - `profile.index` - Halaman profil
  - `profile.update` - Update profil
  - `profile.password` - Ganti password
  - `logout` - Logout
  - `api.notifications.count` - Notifikasi count
  - `api.notifications` - Notifikasi list
- Jika mencoba akses route lain, akan di-redirect ke profil dengan pesan warning

### 4. Model User (`app/Models/User.php`)
**Method `needsPasswordChange()`**:
```php
public function needsPasswordChange()
{
    // Hanya untuk manager dan rmft
    if (!in_array($this->role, ['manager', 'rmft'])) {
        return false;
    }
    
    // Jika password_changed_at null, berarti belum pernah ganti password
    return is_null($this->password_changed_at);
}
```

### 5. Kernel (`app/Http/Kernel.php`)
- Mendaftarkan middleware `check.password.changed`

### 6. Routes (`routes/web.php`)
- Menambahkan middleware `check.password.changed` pada group route yang memerlukan autentikasi

### 7. Migration (`database/migrations/2025_11_24_113042_add_password_changed_at_to_users_table.php`)
- Menambahkan kolom `password_changed_at` (timestamp, nullable) pada tabel `users`
- Kolom ini akan diisi saat user berhasil mengubah password

### 8. ProfileController (`app/Http/Controllers/ProfileController.php`)
**Method `updatePassword()`** sudah mengupdate `password_changed_at`:
```php
$user->update([
    'password' => Hash::make($validated['new_password']),
    'password_changed_at' => now()
]);
```

## Cara Kerja

### Flow Password Visibility Toggle
1. User klik tombol mata di samping input password
2. JavaScript mengubah tipe input dari `password` ke `text` atau sebaliknya
3. Icon mata berubah (mata terbuka ↔ mata tertutup dengan garis)

### Flow Pembatasan Akses
1. User login dengan role manager/rmft
2. Middleware `CheckPasswordChanged` memeriksa `password_changed_at`
3. Jika `NULL`:
   - User mencoba akses dashboard → Redirect ke profil dengan warning
   - User mencoba akses aktivitas → Redirect ke profil dengan warning
   - User akses profil → Diizinkan
   - User ganti password → `password_changed_at` diisi dengan timestamp sekarang
4. Setelah ganti password:
   - `password_changed_at` tidak lagi `NULL`
   - User bisa akses semua fitur sesuai role

## Testing

### 1. Test Toggle Password Visibility
**Login Page**:
- Buka halaman login
- Ketik password
- Klik icon mata → Password terlihat
- Klik lagi → Password tersembunyi

**Profile Page**:
- Buka halaman profil
- Pada form ganti password, test 3 field:
  - Password Saat Ini
  - Password Baru
  - Konfirmasi Password Baru
- Setiap field bisa di-toggle secara independen

### 2. Test Pembatasan Akses

**Persiapan**:
```sql
-- Set password_changed_at menjadi NULL untuk testing
UPDATE users SET password_changed_at = NULL WHERE role IN ('manager', 'rmft');
```

**Test Case 1: Manager Belum Ganti Password**
1. Login sebagai manager yang `password_changed_at = NULL`
2. Setelah login, coba akses:
   - Dashboard → Redirect ke profil dengan warning
   - Aktivitas → Redirect ke profil dengan warning
   - Pipeline → Redirect ke profil dengan warning
3. Akses profil → Berhasil, muncul warning banner
4. Ganti password → Berhasil
5. Coba akses dashboard lagi → Berhasil (tidak di-redirect lagi)

**Test Case 2: RMFT Belum Ganti Password**
1. Login sebagai rmft yang `password_changed_at = NULL`
2. Setelah login, coba akses:
   - Dashboard → Redirect ke profil dengan warning
   - Aktivitas → Redirect ke profil dengan warning
3. Akses profil → Berhasil, muncul warning banner
4. Ganti password → Berhasil
5. Coba akses dashboard lagi → Berhasil

**Test Case 3: Admin (Tidak Terpengaruh)**
1. Login sebagai admin
2. Meskipun `password_changed_at = NULL`, admin tetap bisa akses semua fitur
3. Middleware tidak membatasi admin

**Test Case 4: User Sudah Ganti Password**
1. Login sebagai manager/rmft yang sudah pernah ganti password
2. `password_changed_at` sudah terisi
3. Bisa akses semua fitur tanpa pembatasan

## Migrasi Database

Jalankan migration untuk menambahkan kolom `password_changed_at`:

```bash
php artisan migrate
```

Jika kolom sudah ada sebelumnya, migration akan skip (ada pengecekan `hasColumn`).

## Catatan Penting

1. **Admin tidak terpengaruh**: Pembatasan hanya berlaku untuk role `manager` dan `rmft`
2. **Notifikasi tetap berfungsi**: User yang belum ganti password tetap bisa menerima notifikasi
3. **Logout tetap bisa**: User bisa logout kapan saja
4. **Password visibility**: Fitur toggle password bekerja di semua browser modern
5. **Security**: Password tetap dikirim dalam bentuk terenkripsi (HTTPS), toggle hanya mengubah tampilan di browser

## Keamanan

- Password tetap di-hash dengan bcrypt sebelum disimpan
- Toggle visibility hanya mengubah tampilan di client-side
- Middleware memastikan user tidak bisa bypass pembatasan dengan mengakses URL langsung
- Session tetap aman dengan CSRF protection
