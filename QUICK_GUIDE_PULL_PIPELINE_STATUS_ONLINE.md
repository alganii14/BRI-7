# Quick Guide - Pull Pipeline RMFT & Status Online

## 🚀 Quick Start

```bash
# 1. Jalankan migration
php artisan migrate

# 2. Test di browser
# - Login sebagai RMFT → Cek menu Pull Pipeline
# - Login sebagai Admin → Cek status online di halaman Akun
```

## 📋 Fitur 1: Pull Pipeline untuk RMFT

### Apa yang Berubah?
- Menu "Pull Of Pipeline" sekarang terlihat untuk role **RMFT**
- Sebelumnya hanya Manager dan Admin yang bisa akses

### Cara Test
1. Login sebagai RMFT
2. Lihat sidebar → Menu "Pull Of Pipeline" harus ada
3. Klik menu → Semua strategi (1-8) dan Layering terlihat
4. Klik salah satu → Data pipeline terlihat (read-only)

### Akses
- ✅ **Manager**: Read-only
- ✅ **RMFT**: Read-only (BARU!)
- ✅ **Admin**: Full access

## 📋 Fitur 2: Status Online User

### Apa yang Baru?
- Admin dapat melihat user mana yang sedang online
- Kolom "STATUS ONLINE" di halaman Akun (tabel Manager & RMFT)

### Cara Test
1. Login sebagai Admin
2. Buka halaman "Akun"
3. Lihat kolom "STATUS ONLINE":
   - 🟢 **Online**: User aktif dalam 5 menit terakhir
   - ⚫ **Offline**: User tidak aktif > 5 menit

### Cara Kerja
- User dianggap **online** jika aktif dalam **5 menit** terakhir
- Halaman auto-refresh setiap **30 detik**
- Status update otomatis setiap user melakukan request

## 🎯 Testing Checklist

### Pull Pipeline RMFT
- [ ] Login sebagai RMFT
- [ ] Menu "Pull Of Pipeline" terlihat di sidebar
- [ ] Bisa akses Strategi 1-8
- [ ] Bisa akses Layering
- [ ] Data pipeline terlihat

### Status Online
- [ ] Login sebagai Admin
- [ ] Buka halaman Akun
- [ ] Kolom "STATUS ONLINE" terlihat
- [ ] Login user lain di tab baru
- [ ] Status user lain jadi "Online"
- [ ] Tunggu 30 detik → Halaman auto-refresh
- [ ] Logout user lain
- [ ] Tunggu 5 menit → Status jadi "Offline"

## 🔧 Konfigurasi

### Ubah Durasi Online (Default: 5 menit)
Edit `app/Models/User.php`:
```php
// Ubah angka 5 sesuai kebutuhan
return $this->last_activity->diffInMinutes(now()) < 5;
```

### Ubah Interval Refresh (Default: 30 detik)
Edit `resources/views/akun/index.blade.php`:
```javascript
// Ubah 30000 (30 detik) sesuai kebutuhan
setInterval(function() {
    location.reload();
}, 30000);
```

## 🐛 Troubleshooting

### Pull Pipeline tidak muncul untuk RMFT
```sql
-- Cek role user
SELECT id, name, role FROM users WHERE role = 'rmft';
```
- Pastikan role = 'rmft' (lowercase)
- Clear cache browser

### Status selalu Offline
```sql
-- Cek last_activity
SELECT id, name, last_activity FROM users;
```
- Jika NULL → User belum login setelah migration
- Login sekali untuk populate data

### Auto-refresh tidak jalan
- Clear cache browser (Ctrl+F5)
- Cek console browser untuk error
- Pastikan JavaScript enabled

## 📊 Monitoring

### Cek User Online
```sql
SELECT 
    id, name, role, last_activity,
    CASE 
        WHEN last_activity IS NULL THEN 'Never Login'
        WHEN TIMESTAMPDIFF(MINUTE, last_activity, NOW()) < 5 THEN 'Online'
        ELSE 'Offline'
    END as status
FROM users 
WHERE role IN ('manager', 'rmft')
ORDER BY last_activity DESC;
```

### Statistik Online
```sql
SELECT 
    role,
    COUNT(*) as total,
    SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, last_activity, NOW()) < 5 THEN 1 ELSE 0 END) as online,
    SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, last_activity, NOW()) >= 5 OR last_activity IS NULL THEN 1 ELSE 0 END) as offline
FROM users 
WHERE role IN ('manager', 'rmft')
GROUP BY role;
```

## 📝 File yang Dimodifikasi

### Pull Pipeline RMFT
- `resources/views/layouts/app.blade.php` (1 baris)

### Status Online
- `database/migrations/..._add_last_activity_to_users_table.php` (NEW)
- `app/Models/User.php` (3 methods baru)
- `app/Http/Middleware/UpdateLastActivity.php` (NEW)
- `app/Http/Kernel.php` (1 baris)
- `routes/web.php` (1 baris)
- `resources/views/akun/index.blade.php` (kolom baru + CSS + JS)

## ✅ Checklist Deployment

- [ ] Backup database
- [ ] Run migration: `php artisan migrate`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Test Pull Pipeline RMFT
- [ ] Test Status Online
- [ ] Monitor performance
- [ ] User acceptance testing

## 🎉 Summary

### Pull Pipeline RMFT
- Menu Pull Pipeline sekarang dapat diakses oleh RMFT
- RMFT dapat melihat semua data pipeline (read-only)

### Status Online
- Admin dapat melihat user yang sedang online
- Badge online/offline dengan waktu terakhir aktif
- Auto-refresh setiap 30 detik

**Status: READY FOR PRODUCTION** 🚀

---

**Last Updated:** 2025-11-24  
**Version:** 1.0.0
