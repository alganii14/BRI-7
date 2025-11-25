# Update: Hide No. Rekening di Tabel Daftar Pipeline

## Perubahan yang Dilakukan

### Lokasi
Halaman: `http://127.0.0.1:8000/aktivitas`  
File: `resources/views/aktivitas/index.blade.php`

### Deskripsi
Menyembunyikan data "No. Rekening" di tabel "Daftar Pipeline" dengan menampilkan "-".

### Detail Perubahan

**Sebelum:**
```php
<td>{{ $item->norek ?? '-' }}</td>
```
- Menampilkan nomor rekening aktual dari database
- Contoh: `1234567890`, `9876543210`, dll.

**Sesudah:**
```php
<td>-</td>
```
- Menampilkan "-" untuk semua baris
- Konsisten dengan field CIFNO dan field lainnya yang disembunyikan

### Kolom Tabel yang Terpengaruh

Di tabel "Daftar Pipeline", kolom yang disembunyikan:
- ✅ **CIFNO**: Sudah disembunyikan (menampilkan "-")
- ✅ **NO. REKENING**: Disembunyikan (menampilkan "-") - BARU

### Kolom yang Tetap Terlihat
- ✅ Tanggal
- ✅ Nama RMFT
- ✅ PN
- ✅ Kode KC / Nama KC
- ✅ Kelompok
- ✅ Strategi / Kategori
- ✅ Rencana Aktivitas
- ✅ Segmen Nasabah
- ✅ Nama Nasabah
- ✅ Target (Rp)
- ✅ Status Realisasi
- ✅ Nominal Realisasi
- ✅ Keterangan
- ✅ Aksi (Detail, Edit, Delete, Feedback)

## Testing

### 1. Test Tabel Daftar Pipeline

**Langkah:**
1. Buka `http://127.0.0.1:8000/aktivitas`
2. Login sebagai user (Manager, RMFT, atau Admin)
3. Lihat tabel "Daftar Pipeline"

**Verifikasi:**
- ✅ Kolom "CIFNO" menampilkan "-"
- ✅ Kolom "NO. REKENING" menampilkan "-"
- ✅ Kolom "NAMA NASABAH" tetap terlihat
- ✅ Kolom "TARGET" tetap terlihat
- ✅ Semua data lain tetap terlihat dengan benar

### 2. Test Berbagai Role

**Manager:**
- [ ] Buka halaman aktivitas
- [ ] Kolom "NO. REKENING" = "-"

**RMFT:**
- [ ] Buka halaman aktivitas
- [ ] Kolom "NO. REKENING" = "-"

**Admin:**
- [ ] Buka halaman aktivitas
- [ ] Kolom "NO. REKENING" = "-"

### 3. Test Pagination

**Verifikasi di semua halaman:**
1. Buka halaman 1 → NO. REKENING = "-"
2. Buka halaman 2 → NO. REKENING = "-"
3. Buka halaman 3 → NO. REKENING = "-"

### 4. Test Filter & Search

**Verifikasi setelah filter:**
1. Filter berdasarkan tanggal
2. Filter berdasarkan RMFT
3. Filter berdasarkan status
4. Kolom "NO. REKENING" tetap menampilkan "-"

## Keamanan

### Alasan Perubahan:
- **Privasi Data**: Nomor rekening adalah data sensitif nasabah
- **Konsistensi**: Sama seperti CIFNO yang sudah disembunyikan
- **Compliance**: Sesuai dengan standar keamanan data perbankan
- **Uniformity**: Konsisten dengan halaman detail dan create

### Data yang Tetap Aman:
- Nomor rekening tetap tersimpan di database
- Nomor rekening tetap digunakan untuk proses internal
- Hanya tampilan di tabel yang disembunyikan

## Konsistensi Aplikasi

Sekarang field "No. Rekening" disembunyikan di:
1. ✅ **Halaman Detail** (`/aktivitas/{id}`) - Menampilkan "-"
2. ✅ **Modal Pencarian** (`/aktivitas/create`) - Menampilkan "-"
3. ✅ **Field Input** (`/aktivitas/create`) - Menampilkan "-" setelah pilih
4. ✅ **Tabel Daftar** (`/aktivitas`) - Menampilkan "-" (BARU)

## Catatan Penting

1. **Database Tidak Berubah**: Tidak ada perubahan struktur database
2. **Proses Bisnis Tetap**: Semua proses penyimpanan dan validasi tetap sama
3. **Hanya Tampilan**: Perubahan hanya pada tampilan di tabel
4. **Semua Role**: Berlaku untuk Manager, RMFT, dan Admin

## Rollback (Jika Diperlukan)

Jika perlu menampilkan kembali nomor rekening:

```php
// Ubah dari:
<td>-</td>

// Kembali ke:
<td>{{ $item->norek ?? '-' }}</td>
```

## File yang Dimodifikasi

- `resources/views/aktivitas/index.blade.php`

## Status

✅ **Perubahan selesai**  
✅ **No diagnostics errors**  
✅ **Ready for testing**

---

**Last Updated:** 2025-11-24  
**File Modified:** resources/views/aktivitas/index.blade.php  
**Changes:** 1 line (hide No. Rekening di tabel)
