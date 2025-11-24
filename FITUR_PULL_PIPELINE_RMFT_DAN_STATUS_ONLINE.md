# Fitur Pull Pipeline untuk RMFT & Status Online User

## Deskripsi
Implementasi 2 fitur baru:
1. **Pull Pipeline untuk RMFT**: Menu Pull of Pipeline sekarang dapat diakses oleh role RMFT (sebelumnya hanya Manager dan Admin)
2. **Status Online User**: Admin dapat melihat user mana saja yang sedang online di halaman Akun

## Komponen yang Dimodifikasi/Ditambahkan

### 1. Pull Pipeline untuk RMFT

#### File yang Dimodifikasi:
**`resources/views/layouts/app.blade.php`**
- Mengubah kondisi menu Pull Pipeline dari `@if(auth()->user()->isManager())` menjadi `@if(auth()->user()->isManager() || auth()->user()->isRMFT())`
- Sekarang menu Pull Pipeline dapat diakses oleh:
  - ✅ Manager (Read-only)
  - ✅ RMFT (Read-only)
  - ✅ Admin (Full access)

**Perubahan:**
```php
// Sebelum
@if(auth()->user()->isManager())
<!-- Pull Of Pipeline Menu for Manager (Read-only) -->

// Sesudah
@if(auth()->user()->isManager() || auth()->user()->isRMFT())
<!-- Pull Of Pipeline Menu for Manager & RMFT (Read-only) -->
```

### 2. Status Online User

#### A. Database Migration
**`database/migrations/2025_11_24_144109_add_last_activity_to_users_table.php`**
- Menambahkan kolom `last_activity` (timestamp, nullable) pada tabel `users`
- Kolom ini menyimpan waktu terakhir user melakukan aktivitas

#### B. Model User
**`app/Models/User.php`**

**Perubahan:**
1. Menambahkan `last_activity` ke `$fillable`
2. Menambahkan cast untuk `last_activity` sebagai datetime
3. Menambahkan method `isOnline()`:
```php
public function isOnline()
{
    if (is_null($this->last_activity)) {
        return false;
    }
    
    // User dianggap online jika aktif dalam 5 menit terakhir
    return $this->last_activity->diffInMinutes(now()) < 5;
}
```

4. Menambahkan method `updateLastActivity()`:
```php
public function updateLastActivity()
{
    $this->last_activity = now();
    $this->save();
}
```

#### C. Middleware
**`app/Http/Middleware/UpdateLastActivity.php`** (NEW)
- Middleware baru untuk update `last_activity` setiap kali user melakukan request
- Otomatis update timestamp saat user aktif

```php
public function handle(Request $request, Closure $next)
{
    if (auth()->check()) {
        // Update last_activity setiap request
        auth()->user()->update(['last_activity' => now()]);
    }
    
    return $next($request);
}
```

#### D. Kernel
**`app/Http/Kernel.php`**
- Mendaftarkan middleware `update.last.activity`

#### E. Routes
**`routes/web.php`**
- Menambahkan middleware `update.last.activity` pada group route yang memerlukan autentikasi

```php
Route::middleware(['auth', 'check.password.changed', 'update.last.activity'])->group(function () {
    // ...
});
```

#### F. View Akun
**`resources/views/akun/index.blade.php`**

**Perubahan:**
1. Menambahkan kolom "STATUS ONLINE" di tabel Manager dan RMFT
2. Menampilkan badge online/offline dengan informasi waktu
3. Menambahkan CSS untuk badge online/offline
4. Menambahkan auto-refresh setiap 30 detik

**Tampilan Status:**
- 🟢 **Online**: User aktif dalam 5 menit terakhir
  - Badge hijau dengan teks "🟢 Online"
  - Menampilkan "Aktif X menit yang lalu"
  
- ⚫ **Offline**: User tidak aktif lebih dari 5 menit
  - Badge abu-abu dengan teks "⚫ Offline"
  - Menampilkan "Terakhir X jam/hari yang lalu"

## Cara Kerja

### Flow Pull Pipeline untuk RMFT
1. User login sebagai RMFT
2. Sidebar menampilkan menu "Pull Of Pipeline"
3. RMFT dapat melihat semua data pipeline (read-only)
4. RMFT menggunakan route yang sama dengan Manager (`manager-pull-pipeline.*`)

### Flow Status Online
1. User login ke aplikasi
2. Middleware `UpdateLastActivity` update `last_activity` setiap request
3. Admin membuka halaman Akun
4. Sistem cek `last_activity` setiap user:
   - Jika < 5 menit → Status Online
   - Jika ≥ 5 menit → Status Offline
5. Halaman auto-refresh setiap 30 detik untuk update status

## Testing

### 1. Test Pull Pipeline untuk RMFT

**Persiapan:**
```sql
-- Pastikan ada user RMFT
SELECT * FROM users WHERE role = 'rmft';
```

**Test Case:**
1. Login sebagai RMFT
2. Cek sidebar → Menu "Pull Of Pipeline" harus terlihat
3. Klik menu "Pull Of Pipeline"
4. Cek semua submenu (Strategi 1-8, Layering)
5. Klik salah satu submenu → Harus bisa akses (read-only)
6. Verifikasi data pipeline terlihat

### 2. Test Status Online

**Persiapan:**
```bash
# Jalankan migration
php artisan migrate
```

**Test Case 1: User Online**
1. Login sebagai Admin
2. Buka tab baru, login sebagai Manager/RMFT
3. Kembali ke tab Admin
4. Buka halaman Akun
5. Cek status Manager/RMFT → Harus "🟢 Online"
6. Lihat waktu "Aktif X detik/menit yang lalu"

**Test Case 2: User Offline**
1. Login sebagai Admin
2. Buka halaman Akun
3. Cek user yang tidak login → Status "⚫ Offline"
4. Jika ada `last_activity`, tampil "Terakhir X jam/hari yang lalu"

**Test Case 3: Auto-Refresh**
1. Login sebagai Admin
2. Buka halaman Akun
3. Tunggu 30 detik
4. Halaman harus auto-refresh
5. Status online/offline harus update

**Test Case 4: Transisi Online ke Offline**
1. Login sebagai Admin dan Manager (2 tab)
2. Admin buka halaman Akun → Manager status "Online"
3. Tutup tab Manager (logout)
4. Tunggu 5 menit
5. Refresh halaman Akun
6. Status Manager harus berubah jadi "Offline"

## Migrasi Database

Jalankan migration untuk menambahkan kolom `last_activity`:

```bash
php artisan migrate
```

## Konfigurasi

### Ubah Durasi Online
Edit `app/Models/User.php` method `isOnline()`:

```php
// Default: 5 menit
return $this->last_activity->diffInMinutes(now()) < 5;

// Ubah jadi 10 menit
return $this->last_activity->diffInMinutes(now()) < 10;

// Ubah jadi 2 menit
return $this->last_activity->diffInMinutes(now()) < 2;
```

### Ubah Interval Auto-Refresh
Edit `resources/views/akun/index.blade.php`:

```javascript
// Default: 30 detik
setInterval(function() {
    location.reload();
}, 30000);

// Ubah jadi 60 detik
setInterval(function() {
    location.reload();
}, 60000);

// Ubah jadi 15 detik
setInterval(function() {
    location.reload();
}, 15000);
```

## Catatan Penting

### Pull Pipeline RMFT
1. **Read-Only**: RMFT hanya bisa melihat data, tidak bisa edit/delete
2. **Route Sama**: RMFT menggunakan route `manager-pull-pipeline.*` (sama dengan Manager)
3. **Akses Penuh**: RMFT bisa akses semua strategi (1-8) dan Layering

### Status Online
1. **Threshold 5 Menit**: User dianggap online jika aktif dalam 5 menit terakhir
2. **Auto-Update**: `last_activity` update otomatis setiap request
3. **Performance**: Middleware ringan, tidak mempengaruhi performa
4. **Auto-Refresh**: Halaman Akun refresh otomatis setiap 30 detik
5. **Hanya Admin**: Kolom status online hanya terlihat oleh Admin

## Keamanan

- ✅ Middleware hanya update `last_activity` untuk user yang sudah login
- ✅ Status online hanya terlihat oleh Admin
- ✅ Tidak ada data sensitif yang terekspos
- ✅ Auto-refresh tidak mengganggu user experience

## Performance

- ✅ Update `last_activity` sangat cepat (single query)
- ✅ Method `isOnline()` hanya kalkulasi sederhana
- ✅ Auto-refresh hanya di halaman Akun
- ✅ Tidak ada polling ke server (hanya refresh halaman)

## Troubleshooting

### Pull Pipeline tidak muncul untuk RMFT
```php
// Cek role user
SELECT id, name, email, role FROM users WHERE role = 'rmft';

// Pastikan method isRMFT() ada di User model
// Cek app/Models/User.php
```

### Status selalu Offline
```sql
-- Cek kolom last_activity
SELECT id, name, last_activity FROM users;

-- Jika NULL, user belum pernah login setelah migration
-- Login sekali untuk populate data
```

### Auto-refresh tidak jalan
- Clear cache browser (Ctrl+F5)
- Cek console browser untuk error JavaScript
- Pastikan JavaScript enabled

### Status tidak update
```bash
# Cek middleware terdaftar
php artisan route:list --name=dashboard

# Harus ada middleware: update.last.activity
```

## Summary

### Fitur 1: Pull Pipeline RMFT
- ✅ Menu Pull Pipeline sekarang dapat diakses RMFT
- ✅ RMFT dapat melihat semua data pipeline
- ✅ Akses read-only (tidak bisa edit/delete)

### Fitur 2: Status Online
- ✅ Admin dapat melihat user yang sedang online
- ✅ Badge online/offline dengan informasi waktu
- ✅ Auto-refresh setiap 30 detik
- ✅ Threshold 5 menit untuk status online

**Status: READY FOR TESTING** 🚀
