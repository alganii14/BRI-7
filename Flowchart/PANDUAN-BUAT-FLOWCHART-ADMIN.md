# Panduan Membuat Flowchart Admin di Diagrams.net

## Langkah Cepat

1. Buka https://app.diagrams.net/
2. Pilih "Create New Diagram"
3. Pilih "Blank Diagram" atau template "Flowchart"
4. Ikuti struktur di bawah ini

---

## STRUKTUR FLOWCHART ADMIN

### Level 1: Login & Dashboard

```
┌─────────────────┐
│  ADMIN LOGIN    │ (Ellipse, Hijau)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Dashboard Admin │ (Rectangle, Biru Muda)
└────────┬────────┘
         │
         ▼
      ◆─────◆
     ◆ Pilih ◆      (Diamond, Kuning)
      ◆ Menu ◆
       ◆───◆
```

### Level 2: Menu Utama (6 Pilihan)

Dari "Pilih Menu" bercabang ke 6 menu:

1. **Manajemen User** (Ungu Muda)
2. **Manajemen Pipeline** (Ungu Muda)
3. **Manajemen Nasabah** (Ungu Muda)
4. **Import Data** (Ungu Muda)
5. **Laporan & Rekap** (Ungu Muda)
6. **Logout** (Merah Muda, Ellipse)

---

## DETAIL SETIAP MODUL

### 1. MANAJEMEN USER

```
Manajemen User
      │
      ▼
   ◆─────◆
  ◆ Aksi  ◆
   ◆ User ◆
    ◆───◆
      │
      ├─→ Tambah User → Input Data → Validasi? ─┬─→ Valid → Simpan → Sukses
      │                                          └─→ Tidak Valid → Error → Kembali
      │
      ├─→ Edit User → Edit Data → Validasi? ─┬─→ Valid → Simpan → Sukses
      │                                       └─→ Tidak Valid → Error → Kembali
      │
      ├─→ Hapus User → Konfirmasi? ─┬─→ Ya → User Dihapus → Sukses
      │                              └─→ Tidak → Kembali
      │
      └─→ Assign Role → Pilih Role (Admin/Manager/RMFT) → Simpan → Sukses
                                                                      │
                                                                      ▼
                                                                  Dashboard
```

### 2. MANAJEMEN PIPELINE

```
Manajemen Pipeline
      │
      ▼
   ◆──────◆
  ◆ Aksi   ◆
   ◆Pipeline◆
    ◆────◆
      │
      ├─→ Lihat Semua Pipeline → Tampilkan Data → Sukses
      │
      ├─→ Tambah Pipeline → Input Data → Validasi? ─┬─→ Valid → Simpan → Sukses
      │                                              └─→ Tidak Valid → Error
      │
      ├─→ Edit Pipeline → Edit Data → Validasi? ─┬─→ Valid → Simpan → Sukses
      │                                           └─→ Tidak Valid → Error
      │
      ├─→ Hapus Pipeline → Konfirmasi? ─┬─→ Ya → Pipeline Dihapus → Sukses
      │                                  └─→ Tidak → Kembali
      │
      └─→ Assign ke RMFT → Pilih RMFT → Simpan Assignment → Notifikasi RMFT → Sukses
                                                                                  │
                                                                                  ▼
                                                                              Dashboard
```

### 3. MANAJEMEN NASABAH

```
Manajemen Nasabah
      │
      ▼
   ◆──────◆
  ◆ Aksi   ◆
   ◆Nasabah◆
    ◆────◆
      │
      ├─→ Lihat Data Nasabah → Tampilkan Data → Sukses
      │
      ├─→ Tambah Nasabah → Input Data → Validasi? ─┬─→ Valid → Simpan → Sukses
      │                                             └─→ Tidak Valid → Error
      │
      ├─→ Edit Nasabah → Edit Data → Validasi? ─┬─→ Valid → Simpan → Sukses
      │                                          └─→ Tidak Valid → Error
      │
      └─→ Hapus Nasabah → Konfirmasi? ─┬─→ Ya → Nasabah Dihapus → Sukses
                                        └─→ Tidak → Kembali
                                                      │
                                                      ▼
                                                  Dashboard
```

### 4. IMPORT DATA

```
Import Data
      │
      ▼
   ◆──────◆
  ◆ Pilih  ◆
   ◆ Jenis ◆
    ◆────◆
      │
      ├─→ AUM DPK ────────┐
      ├─→ Brilink ────────┤
      ├─→ Merchant Savol ─┤
      ├─→ Nasabah ────────┤
      ├─→ Layering ───────┤
      └─→ Lainnya ────────┘
                          │
                          ▼
                  Upload File Excel/CSV
                          │
                          ▼
                    Validasi File?
                          │
                ┌─────────┴─────────┐
                │                   │
              Valid            Tidak Valid
                │                   │
                ▼                   ▼
          Proses Import      Tampilkan Error
                │                   │
                ▼                   └─→ Kembali
          Hasil Import?
                │
      ┌─────────┼─────────┐
      │         │         │
   Sukses   Sebagian   Gagal Semua
      │       Gagal        │
      │         │          │
      ▼         ▼          ▼
  Tampilkan  Tampilkan  Tampilkan Error
   Hasil     Data Gagal     │
      │      Download       └─→ Kembali
      │      Error Log
      │         │
      └─────────┴─→ Dashboard
```

### 5. LAPORAN & REKAP

```
Laporan & Rekap
      │
      ▼
   ◆──────◆
  ◆ Pilih  ◆
   ◆Laporan◆
    ◆────◆
      │
      ├─→ Rekap Pipeline per Status ─┐
      ├─→ Rekap per RMFT ────────────┤
      ├─→ Rekap per Unit Kerja ──────┤
      └─→ Rekap Aktivitas ───────────┘
                                      │
                                      ▼
                              Set Filter & Periode
                                      │
                                      ▼
                              Generate Laporan
                                      │
                                      ▼
                              Tampilkan Laporan
                                      │
                                      ▼
                                  Export?
                                      │
                              ┌───────┴───────┐
                              │               │
                             Ya              Tidak
                              │               │
                              ▼               │
                      Export Excel/PDF        │
                              │               │
                              └───────┬───────┘
                                      │
                                      ▼
                                  Dashboard
```

---

## PANDUAN WARNA

- **Hijau (#d5e8d4)**: Start (Login)
- **Merah Muda (#f8cecc)**: End (Logout)
- **Biru Muda (#dae8fc)**: Dashboard
- **Kuning (#fff2cc)**: Decision (Diamond)
- **Ungu Muda (#e1d5e7)**: Menu Utama
- **Hijau Muda (#d5e8d4)**: Sukses
- **Merah Muda (#f8cecc)**: Error

---

## PANDUAN SHAPES

- **Ellipse (Oval)**: Start/End
- **Rectangle**: Process/Action
- **Diamond**: Decision/Pilihan
- **Arrow**: Flow Direction

---

## TIPS MEMBUAT DI DIAGRAMS.NET

1. **Gunakan Grid**: View > Grid (untuk alignment yang rapi)
2. **Copy-Paste**: Ctrl+C, Ctrl+V untuk duplicate shapes
3. **Align**: Pilih multiple shapes > Arrange > Align
4. **Distribute**: Arrange > Distribute untuk jarak sama
5. **Auto Layout**: Arrange > Layout > Vertical Flow
6. **Group**: Ctrl+G untuk group shapes
7. **Zoom**: Ctrl + Mouse Wheel
8. **Pan**: Klik kanan + drag

---

## EXPORT FLOWCHART

Setelah selesai:
1. File > Export as > PNG (untuk presentasi)
2. File > Export as > PDF (untuk dokumen)
3. File > Save as (untuk edit lagi nanti)

---

## ESTIMASI WAKTU

- Flowchart Sederhana (Level 1-2): 15-20 menit
- Flowchart Lengkap (Semua Detail): 45-60 menit

Selamat membuat flowchart! 🎨
