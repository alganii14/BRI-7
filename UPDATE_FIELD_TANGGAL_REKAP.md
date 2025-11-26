# Update Field Tanggal pada Data Rekap

## Perubahan yang Dilakukan

### 1. Database Migration
- **File**: `database/migrations/2025_11_26_100349_add_tanggal_to_rekaps_table.php`
- Menambahkan kolom `tanggal` (date, nullable) pada tabel `rekaps`

### 2. Model Rekap
- **File**: `app/Models/Rekap.php`
- Menambahkan `tanggal` ke dalam `$fillable`

### 3. Form Import
- **File**: `resources/views/rekap/import.blade.php`
- Menambahkan input field tanggal sebelum upload file
- Default value: tanggal hari ini
- Field wajib diisi (required)

### 4. Controller Import
- **File**: `app/Http/Controllers/RekapController.php`
- Menambahkan validasi untuk field `tanggal`
- Menyimpan tanggal ke setiap record yang diimport
- Update method `importCsv()` dan `importExcel()` untuk menerima parameter tanggal

### 5. Tabel Data Rekap
- **File**: `resources/views/rekap/index.blade.php`
- Menambahkan kolom "TANGGAL" di tabel
- Format tampilan: dd MMM yyyy (contoh: 26 Nov 2025)
- Menyesuaikan CSS untuk alignment kolom

## Cara Penggunaan

1. Buka halaman Import Rekap: `http://127.0.0.1:8000/rekap/import`
2. Pilih tanggal untuk data yang akan diimport
3. Upload file Excel/CSV
4. Klik "Import Data"
5. Semua data yang diimport akan memiliki tanggal yang sama sesuai pilihan

## Catatan
- Field tanggal bersifat wajib (required) saat import
- Tanggal default adalah hari ini
- Tanggal akan ditampilkan di kolom kedua tabel Data Rekap
