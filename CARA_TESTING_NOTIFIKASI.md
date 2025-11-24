# Cara Testing Fitur Notifikasi Password

## Persiapan

1. Pastikan migration sudah dijalankan:
```bash
php artisan migrate
```

2. Reset status password untuk testing:
```bash
php artisan db:seed --class=TestPasswordSeeder
```

## Skenario Testing

### Test 1: Notifikasi untuk Manager
1. Login sebagai user dengan role **manager**
2. Setelah login, perhatikan icon bell di navbar (sebelah kiri profil)
3. Seharusnya ada badge merah dengan angka "1"
4. Klik icon bell
5. Akan muncul dropdown notifikasi dengan pesan:
   - Title: "Ubah Password Default"
   - Message: "Untuk keamanan akun Anda, silakan ubah password default Anda."
   - Link: "Ubah Password"

### Test 2: Notifikasi untuk RMFT
1. Login sebagai user dengan role **rmft**
2. Setelah login, perhatikan icon bell di navbar
3. Seharusnya ada badge merah dengan angka "1"
4. Klik icon bell
5. Akan muncul dropdown notifikasi yang sama seperti manager

### Test 3: Tidak Ada Notifikasi untuk Admin
1. Login sebagai user dengan role **admin**
2. Setelah login, perhatikan icon bell di navbar
3. Seharusnya TIDAK ada badge merah
4. Klik icon bell
5. Akan muncul "Tidak ada notifikasi"

### Test 4: Mengubah Password (Manager/RMFT)
1. Login sebagai manager atau rmft yang memiliki notifikasi
2. Klik icon bell, lalu klik "Ubah Password"
3. Akan redirect ke halaman profil
4. Scroll ke bagian "Ubah Password"
5. Isi form:
   - Password Saat Ini: [password lama]
   - Password Baru: [password baru minimal 8 karakter]
   - Konfirmasi Password Baru: [password baru yang sama]
6. Klik "Ubah Password"
7. Seharusnya muncul pesan sukses
8. Refresh halaman
9. Badge notifikasi seharusnya hilang
10. Klik icon bell, seharusnya muncul "Tidak ada notifikasi"

### Test 5: Status Password di Halaman Akun (Admin)
1. Login sebagai **admin**
2. Buka menu "Akun" di sidebar
3. Scroll ke bagian "Akun Manager"
4. Perhatikan kolom "STATUS PASSWORD":
   - User yang belum mengubah password: Badge kuning "⚠ Password Default"
   - User yang sudah mengubah password: Badge hijau "✓ Sudah Diubah" + tanggal
5. Scroll ke bagian "Akun RMFT"
6. Perhatikan kolom "STATUS PASSWORD" yang sama

### Test 6: Admin Membuat Akun Baru
1. Login sebagai **admin**
2. Buka menu "Akun" di sidebar
3. Klik "Tambah Akun Baru"
4. Isi form untuk membuat akun manager atau rmft baru
5. Set password default (misalnya: "password123")
6. Submit form
7. Kembali ke halaman Akun
8. Akun baru seharusnya memiliki status "⚠ Password Default"
9. Logout dan login sebagai akun baru tersebut
10. Seharusnya muncul notifikasi untuk mengubah password

### Test 7: Admin Reset Password User
1. Login sebagai **admin**
2. Buka menu "Akun" di sidebar
3. Pilih salah satu manager/rmft yang sudah mengubah password (badge hijau)
4. Klik "Edit" pada akun tersebut
5. Isi password baru di form edit
6. Submit form
7. Kembali ke halaman Akun
8. Status password user tersebut seharusnya kembali ke "⚠ Password Default"
9. Logout dan login sebagai user tersebut
10. Seharusnya muncul notifikasi lagi

### Test 8: Responsive Mobile
1. Buka browser developer tools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Pilih device mobile (misalnya iPhone 12)
4. Login sebagai manager/rmft
5. Perhatikan icon bell di navbar
6. Klik icon bell
7. Dropdown notifikasi seharusnya muncul dengan ukuran yang sesuai untuk mobile

### Test 9: Auto Refresh Notifikasi
1. Login sebagai manager/rmft yang memiliki notifikasi
2. Biarkan halaman terbuka selama 5 menit
3. Notifikasi seharusnya di-refresh otomatis
4. Badge count akan tetap sama jika status tidak berubah

### Test 10: Multiple Browser/Tab
1. Login sebagai manager/rmft di browser pertama
2. Ubah password di browser pertama
3. Buka tab/browser kedua dengan user yang sama
4. Refresh halaman di browser kedua
5. Notifikasi seharusnya hilang di browser kedua juga

## Checklist Testing

- [ ] Badge notifikasi muncul untuk manager dengan password default
- [ ] Badge notifikasi muncul untuk rmft dengan password default
- [ ] Badge notifikasi TIDAK muncul untuk admin
- [ ] Dropdown notifikasi menampilkan pesan yang benar
- [ ] Link "Ubah Password" redirect ke halaman profil
- [ ] Setelah ubah password, notifikasi hilang
- [ ] Status password di halaman Akun (admin) menampilkan badge yang benar
- [ ] Akun baru memiliki status "Password Default"
- [ ] Admin reset password mengubah status kembali ke "Password Default"
- [ ] Responsive di mobile device
- [ ] Auto refresh notifikasi bekerja
- [ ] Notifikasi sinkron di multiple browser/tab

## Troubleshooting

### Badge tidak muncul
- Cek apakah migration sudah dijalankan
- Cek apakah seeder sudah dijalankan
- Cek console browser untuk error JavaScript
- Cek network tab untuk API call `/api/notifications/count`

### Notifikasi tidak hilang setelah ubah password
- Cek apakah `password_changed_at` ter-update di database
- Refresh halaman secara manual
- Clear cache browser

### Status password tidak muncul di halaman Akun
- Pastikan login sebagai admin
- Cek apakah kolom `password_changed_at` ada di database
- Refresh halaman

## Database Query untuk Debugging

### Cek status password semua user:
```sql
SELECT id, name, email, role, password_changed_at 
FROM users 
WHERE role IN ('manager', 'rmft')
ORDER BY role, name;
```

### Reset password status untuk testing:
```sql
UPDATE users 
SET password_changed_at = NULL 
WHERE role IN ('manager', 'rmft');
```

### Cek user yang belum ubah password:
```sql
SELECT id, name, email, role 
FROM users 
WHERE role IN ('manager', 'rmft') 
AND password_changed_at IS NULL;
```

### Cek user yang sudah ubah password:
```sql
SELECT id, name, email, role, password_changed_at 
FROM users 
WHERE role IN ('manager', 'rmft') 
AND password_changed_at IS NOT NULL
ORDER BY password_changed_at DESC;
```
