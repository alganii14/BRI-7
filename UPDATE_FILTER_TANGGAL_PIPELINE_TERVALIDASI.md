# Update Filter Tanggal untuk Pipeline Tervalidasi

## Perubahan yang Dilakukan

### 1. Form Import Rekap
- **File**: `resources/views/rekap/import.blade.php`
- Mengubah field tanggal tunggal menjadi 2 field:
  - **Dari Tanggal**: Tanggal awal range
  - **Sampai Tanggal**: Tanggal akhir range
- Layout menggunakan grid 2 kolom untuk tampilan yang rapi
- Validasi: Sampai Tanggal harus >= Dari Tanggal

### 2. Controller Import
- **File**: `app/Http/Controllers/RekapController.php`
- Update validasi untuk menerima `dari_tanggal` dan `sampai_tanggal`
- Validasi: `sampai_tanggal` harus `after_or_equal:dari_tanggal`
- Data yang diimport menggunakan `dari_tanggal` sebagai tanggal default

### 3. Dashboard Controller - Filter Pipeline Tervalidasi
- **File**: `app/Http/Controllers/DashboardController.php`
- Menambahkan filter tanggal untuk Pipeline Tervalidasi di 3 dashboard:

#### Dashboard RMFT
- Filter berdasarkan: KC, Nama RMFT, dan Range Tanggal
- Jika ada `startDate` & `endDate`: filter berdasarkan range
- Jika tidak: filter berdasarkan bulan dan tahun yang dipilih

#### Dashboard Manager
- Filter berdasarkan: KC Manager, RMFT (jika dipilih), dan Range Tanggal
- Jika ada `startDate` & `endDate`: filter berdasarkan range
- Jika tidak: filter berdasarkan bulan dan tahun yang dipilih

#### Dashboard Admin
- Filter berdasarkan: KC (jika dipilih), RMFT (jika dipilih), dan Range Tanggal
- Jika ada `startDate` & `endDate`: filter berdasarkan range
- Jika tidak: filter berdasarkan bulan dan tahun yang dipilih

## Cara Penggunaan

### Import Data Rekap
1. Buka: `http://127.0.0.1:8000/rekap/import`
2. Pilih **Dari Tanggal** dan **Sampai Tanggal**
3. Upload file Excel/CSV
4. Klik "Import Data"
5. Data akan tersimpan dengan tanggal sesuai "Dari Tanggal"

### Filter Pipeline Tervalidasi di Dashboard
1. Buka Dashboard (Admin/Manager/RMFT)
2. Gunakan filter yang tersedia:
   - **Bulan & Tahun**: Pipeline Tervalidasi akan difilter berdasarkan bulan/tahun
   - **Dari Tanggal & Sampai Tanggal**: Pipeline Tervalidasi akan difilter berdasarkan range tanggal
3. Filter juga berlaku untuk:
   - Kantor Cabang (Admin & Manager)
   - RMFT (Admin & Manager)

## Logika Filter
- Jika user memilih **range tanggal** (Dari Tanggal & Sampai Tanggal): 
  - Pipeline Tervalidasi dihitung dari data rekap dalam range tersebut
- Jika user memilih **bulan & tahun**:
  - Pipeline Tervalidasi dihitung dari data rekap dalam bulan/tahun tersebut
- Filter KC dan RMFT tetap berlaku sesuai role user

## Catatan
- Field "Sampai Tanggal" harus sama atau setelah "Dari Tanggal"
- Filter tanggal di dashboard mengikuti filter yang sudah ada (bulan/tahun atau range tanggal)
- Pipeline Tervalidasi akan otomatis menyesuaikan dengan filter yang dipilih
