# Instruksi Lengkap: Membuat Flowchart Admin

## 🎯 3 Cara Membuat Flowchart

### Cara 1: Manual di Diagrams.net (RECOMMENDED)
**Paling Fleksibel & Mudah Diedit**

1. Buka https://app.diagrams.net/
2. Klik "Create New Diagram"
3. Pilih "Blank Diagram"
4. Ikuti panduan lengkap di file: `PANDUAN-BUAT-FLOWCHART-ADMIN.md`
5. Export sebagai PNG/PDF

**Keuntungan:**
- ✅ Bisa edit kapan saja
- ✅ Tampilan profesional
- ✅ Banyak pilihan style
- ✅ Gratis

**Waktu:** 30-45 menit

---

### Cara 2: Import Mermaid ke Diagrams.net
**Cepat & Otomatis**

1. Buka https://app.diagrams.net/
2. Klik menu "Arrange" > "Insert" > "Advanced" > "Mermaid"
3. Copy isi file `admin-flowchart.mmd`
4. Paste ke dialog Mermaid
5. Klik "Insert"
6. Edit sesuai kebutuhan
7. Export sebagai PNG/PDF

**Keuntungan:**
- ✅ Cepat (5-10 menit)
- ✅ Struktur sudah jadi
- ✅ Bisa diedit lagi

**Waktu:** 10-15 menit

---

### Cara 3: Generate Otomatis dengan Python
**Untuk Developer**

1. Install Graphviz:
   ```bash
   # Download dari https://graphviz.org/download/
   # Atau via chocolatey (Windows):
   choco install graphviz
   
   # Install Python package:
   pip install graphviz
   ```

2. Jalankan script:
   ```bash
   cd Flowchart
   python generate-flowchart.py
   ```

3. File PNG akan otomatis dibuat: `admin-flowchart-basic.png`

**Keuntungan:**
- ✅ Sangat cepat (1 menit)
- ✅ Bisa di-customize via code
- ✅ Reproducible

**Waktu:** 5 menit (setelah install)

---

## 📁 File yang Tersedia

| File | Deskripsi | Cara Pakai |
|------|-----------|------------|
| `admin-flowchart.mmd` | Mermaid code | Copy-paste ke mermaid.live atau diagrams.net |
| `admin-flowchart-simple.drawio` | File diagrams.net sederhana | Buka langsung di diagrams.net |
| `PANDUAN-BUAT-FLOWCHART-ADMIN.md` | Panduan lengkap manual | Baca dan ikuti step-by-step |
| `generate-flowchart.py` | Script Python | Jalankan untuk auto-generate PNG |
| `CARA-PAKAI-DIAGRAMS-NET.md` | Tutorial diagrams.net | Panduan umum diagrams.net |

---

## 🎨 Rekomendasi Saya

**Untuk Presentasi/Dokumen Formal:**
→ Gunakan **Cara 1** (Manual di Diagrams.net)
- Hasil paling profesional
- Bisa customize warna, font, layout
- Mudah diedit kapan saja

**Untuk Prototype/Draft Cepat:**
→ Gunakan **Cara 2** (Import Mermaid)
- Cepat dapat hasil
- Struktur sudah benar
- Bisa dipoles lagi nanti

**Untuk Developer/Automation:**
→ Gunakan **Cara 3** (Python Script)
- Bisa integrate ke CI/CD
- Version control friendly
- Reproducible

---

## 📊 Struktur Flowchart Admin

```
ADMIN LOGIN
    ↓
Dashboard Admin
    ↓
Pilih Menu
    ├─→ Manajemen User
    │   ├─→ Tambah User
    │   ├─→ Edit User
    │   ├─→ Hapus User
    │   └─→ Assign Role
    │
    ├─→ Manajemen Pipeline
    │   ├─→ Lihat Pipeline
    │   ├─→ Tambah Pipeline
    │   ├─→ Edit Pipeline
    │   ├─→ Hapus Pipeline
    │   └─→ Assign ke RMFT
    │
    ├─→ Manajemen Nasabah
    │   ├─→ Lihat Nasabah
    │   ├─→ Tambah Nasabah
    │   ├─→ Edit Nasabah
    │   └─→ Hapus Nasabah
    │
    ├─→ Import Data
    │   ├─→ Import AUM
    │   ├─→ Import Brilink
    │   ├─→ Import Merchant
    │   ├─→ Import Nasabah
    │   └─→ Import Lainnya
    │
    ├─→ Laporan & Rekap
    │   ├─→ Rekap Pipeline
    │   ├─→ Rekap RMFT
    │   ├─→ Rekap Unit Kerja
    │   └─→ Rekap Aktivitas
    │
    └─→ LOGOUT
```

---

## ❓ FAQ

**Q: File mana yang harus saya buka di diagrams.net?**
A: Buka `admin-flowchart-simple.drawio` untuk struktur dasar, atau buat baru mengikuti `PANDUAN-BUAT-FLOWCHART-ADMIN.md`

**Q: Kenapa file .drawio tidak menampilkan detail lengkap?**
A: File .drawio yang saya buat adalah struktur dasar. Untuk detail lengkap, ikuti panduan manual di `PANDUAN-BUAT-FLOWCHART-ADMIN.md`

**Q: Bagaimana cara export ke PNG dengan kualitas tinggi?**
A: Di diagrams.net: File > Export as > PNG > Set DPI ke 300 > Export

**Q: Bisakah saya edit flowchart setelah di-export?**
A: Ya, simpan dulu sebagai .drawio (File > Save as), baru export ke PNG/PDF

**Q: Apakah saya perlu install software?**
A: Tidak, diagrams.net adalah web-based. Tapi untuk Cara 3 (Python), perlu install Graphviz.

---

## 🚀 Next Steps

Setelah flowchart Admin selesai:
1. ✅ Review dengan tim
2. ✅ Buat flowchart Manager
3. ✅ Buat flowchart RMFT
4. ✅ Dokumentasikan dalam SOP

---

**Butuh bantuan?** Lihat file `PANDUAN-BUAT-FLOWCHART-ADMIN.md` untuk tutorial step-by-step!
