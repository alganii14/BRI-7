# Update: Hide No. Rekening di Modal Nasabah

## Perubahan yang Dilakukan

### Lokasi
Halaman: `http://127.0.0.1:8000/aktivitas/create`  
File: `resources/views/aktivitas/create.blade.php`

### Deskripsi
Menyembunyikan data "No. Rekening" di modal pencarian nasabah dengan menampilkan "-" (seperti CIFNO).

### Detail Perubahan

**Sebelum:**
- Modal menampilkan nomor rekening aktual dari database
- Contoh: `1234567890`, `9876543210`, dll.

**Sesudah:**
- Modal menampilkan "-" untuk semua nomor rekening
- Konsisten dengan field CIFNO yang juga disembunyikan

### Kategori yang Terpengaruh

Perubahan ini berlaku untuk SEMUA kategori pipeline:

#### Strategi 1 - Optimalisasi Digital Channel
- ✅ Merchant Savol Besar Casa Kecil (QRIS & EDC)
- ✅ Penurunan CASA Merchant (QRIS & EDC)
- ✅ Penurunan CASA Brilink
- ✅ Qlola Non Debitur
- ✅ Non Debitur Vol Besar CASA Kecil

#### Strategi 2 - Rekening Debitur Transaksi
- ✅ Qlola (Belum ada Qlola / ada namun nonaktif)
- ✅ User Aktif Casa Kecil

#### Strategi 3 - Optimalisasi Business Cluster
- ✅ Optimalisasi Business Cluster

#### Strategi 4 - Peningkatan Payroll Berkualitas
- ✅ Existing Payroll (Corporate Code tetap terlihat)
- ✅ Potensi Payroll (Nama Perusahaan tetap terlihat)

#### Strategi 6 - Kolaborasi Perusahaan Anak
- ✅ List Perusahaan Anak

#### Strategi 7 - Reaktivasi Rekening Dormant
- ✅ Penurunan Prioritas Ritel & Mikro
- ✅ AUM>2M DPK<50 juta

#### Strategi 8 - Penguatan Produk & Fungsi RM
- ✅ Wingback Penguatan Produk & Fungsi RM

#### Layering
- ✅ Wingback

## Implementasi Teknis

### Perubahan di JavaScript

**Dari:**
```javascript
html += `<td style="padding: 10px; font-family: monospace;">${nasabah.no_rekening || '-'}</td>`;
```

**Jadi:**
```javascript
html += `<td style="padding: 10px; font-family: monospace;">-</td>`;
```

### Jumlah Perubahan
- **Total replacements**: ~15 baris (tampilan modal)
- **JavaScript update**: 2 baris (field input setelah pilih)
- **Kategori terpengaruh**: Semua kategori yang menampilkan No. Rekening
- **Field input**: Menampilkan "-" setelah pilih nasabah (kecuali Potensi Payroll & Existing Payroll)

## Data yang Disembunyikan

### Di Modal Pencarian Nasabah:
- ✅ **CIFNO**: Disembunyikan dengan "-"
- ✅ **No. Rekening**: Disembunyikan dengan "-" (BARU)

### Data yang Tetap Terlihat:
- ✅ Nama Nasabah
- ✅ Kode Kanca / Unit Kerja
- ✅ Segmentasi
- ✅ Saldo / Target
- ✅ Data lainnya (tergantung kategori)

## Testing

### 1. Test Modal Pencarian

**Langkah:**
1. Buka `http://127.0.0.1:8000/aktivitas/create`
2. Pilih RMFT
3. Pilih Strategy Pipeline (misal: Strategi 1)
4. Pilih Kategori (misal: Penurunan CASA Merchant)
5. Pilih Tipe Pipeline: "Di dalam Pipeline"
6. Klik icon pencarian di field "CARI NASABAH"
7. Modal terbuka dengan daftar nasabah

**Verifikasi:**
- ✅ Kolom "No. Rekening" menampilkan "-"
- ✅ Kolom "CIFNO" menampilkan "-"
- ✅ Data lain (Nama, Unit, Saldo) tetap terlihat
- ✅ Tombol "Pilih" berfungsi normal

### 2. Test Berbagai Kategori

Test untuk memastikan semua kategori menampilkan "-":

**Strategi 1:**
- [ ] Merchant Savol → No. Rekening = "-"
- [ ] Penurunan Merchant → No. Rekening = "-"
- [ ] Penurunan Brilink → No. Rekening = "-"
- [ ] Qlola Non Debitur → No. Rekening = "-"
- [ ] Non Debitur Vol Besar → No. Rekening = "-"

**Strategi 2:**
- [ ] Qlola Nonaktif → No. Pinjaman & No. Simpanan = "-"
- [ ] User Aktif Casa Kecil → Norek Pinjaman = "-"

**Strategi 7:**
- [ ] Penurunan Prioritas → No. Rekening = "-"
- [ ] AUM>2M DPK<50 juta → Nomor Rekening = "-"

**Strategi 8:**
- [ ] Wingback → No Rekening = "-"

**Layering:**
- [ ] Wingback → No. Rekening = "-"

### 3. Test Fungsionalitas

**Verifikasi bahwa fitur tetap berfungsi:**
1. Pilih nasabah dari modal
2. **Verifikasi field input "CARI NASABAH" menampilkan "-"**
3. Data nasabah terisi di form (Nama, Target)
4. Form bisa disimpan
5. Data tersimpan dengan benar di database

**Catatan:**
- Field "CARI NASABAH" akan menampilkan "-" setelah pilih nasabah
- Untuk Potensi Payroll: Tetap menampilkan Nama Perusahaan
- Untuk Existing Payroll: Tetap menampilkan Corporate Code (CIFNO)

## Keamanan

### Alasan Perubahan:
- **Privasi Data**: Nomor rekening adalah data sensitif nasabah
- **Konsistensi**: Sama seperti CIFNO yang sudah disembunyikan
- **Compliance**: Sesuai dengan standar keamanan data perbankan

### Data yang Tetap Aman:
- Nomor rekening tetap tersimpan di database
- Nomor rekening tetap digunakan untuk proses internal
- Hanya tampilan di modal yang disembunyikan

## Catatan Penting

1. **Field Input Menampilkan "-"**: Setelah pilih nasabah, field "CARI NASABAH" menampilkan "-" (kecuali Potensi Payroll & Existing Payroll)
2. **Database Tidak Berubah**: Tidak ada perubahan struktur database
3. **Proses Bisnis Tetap**: Semua proses penyimpanan dan validasi tetap sama
4. **Hanya Tampilan**: Perubahan hanya pada tampilan (modal & field input)
5. **Pengecualian**:
   - Potensi Payroll: Tetap menampilkan Nama Perusahaan
   - Existing Payroll: Tetap menampilkan Corporate Code

## Rollback (Jika Diperlukan)

Jika perlu menampilkan kembali nomor rekening:

```javascript
// Ubah dari:
html += `<td style="padding: 10px; font-family: monospace;">-</td>`;

// Kembali ke:
html += `<td style="padding: 10px; font-family: monospace;">${nasabah.no_rekening || '-'}</td>`;
```

## File yang Dimodifikasi

- `resources/views/aktivitas/create.blade.php`

## Status

✅ **Perubahan selesai**  
✅ **No diagnostics errors**  
✅ **Ready for testing**

---

**Last Updated:** 2025-11-24  
**File Modified:** resources/views/aktivitas/create.blade.php  
**Changes:** ~15 replacements (hide No. Rekening di modal)
