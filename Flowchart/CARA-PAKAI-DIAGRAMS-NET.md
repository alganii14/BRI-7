# Cara Menggunakan Flowchart di Diagrams.net

## Metode 1: Import File .drawio (RECOMMENDED)

1. Buka https://app.diagrams.net/
2. Klik **"Open Existing Diagram"**
3. Pilih **"Open from Device"**
4. Pilih file `admin-flowchart.drawio`
5. Flowchart akan terbuka dan bisa langsung diedit!
6. Untuk save: File > Export as > PNG/PDF/SVG

## Metode 2: Import Mermaid Code

1. Buka https://app.diagrams.net/
2. Klik **"Arrange"** di menu atas
3. Pilih **"Insert"** > **"Advanced"** > **"Mermaid"**
4. Copy isi file `admin-flowchart.mmd`
5. Paste ke dialog Mermaid
6. Klik **"Insert"**
7. Flowchart akan muncul dan bisa diedit!

## Metode 3: Buat Manual (Paling Fleksibel)

Jika ingin membuat dari awal dengan kontrol penuh:

1. Buka https://app.diagrams.net/
2. Pilih **"Create New Diagram"**
3. Pilih template **"Flowchart"** atau **"Blank Diagram"**
4. Gunakan shapes dari sidebar kiri:
   - **Rounded Rectangle** untuk Start/End
   - **Rectangle** untuk Process
   - **Diamond** untuk Decision
   - **Arrow** untuk Flow

### Struktur Flowchart Admin:

```
START (Admin Login)
    ↓
Dashboard Admin
    ↓
Pilih Menu (Decision)
    ├─→ Manajemen User
    ├─→ Manajemen Pipeline
    ├─→ Manajemen Nasabah
    ├─→ Import Data
    ├─→ Laporan & Rekap
    └─→ Logout (END)
```

## Tips Editing di Diagrams.net:

- **Zoom**: Ctrl + Mouse Wheel
- **Pan**: Klik kanan + drag
- **Duplicate**: Ctrl + D
- **Align**: Pilih multiple shapes > Arrange > Align
- **Auto Layout**: Arrange > Layout > Vertical/Horizontal Flow
- **Change Colors**: Klik shape > Style tab di kanan
- **Add Text**: Double-click shape
- **Connect Shapes**: Drag dari connection point (titik biru)

## Export Options:

- **PNG**: File > Export as > PNG (untuk presentasi)
- **PDF**: File > Export as > PDF (untuk dokumen)
- **SVG**: File > Export as > SVG (untuk web, scalable)
- **JPEG**: File > Export as > JPEG
- **XML**: File > Save as (format native diagrams.net)

## Keuntungan Diagrams.net:

✅ Gratis dan open source
✅ Tidak perlu install, web-based
✅ Support banyak format (Mermaid, PlantUML, dll)
✅ Bisa save ke Google Drive, OneDrive, GitHub
✅ Kolaborasi real-time
✅ Export ke berbagai format
✅ Drag & drop yang mudah

## File yang Tersedia:

1. `admin-flowchart.drawio` - File native diagrams.net (bisa langsung dibuka)
2. `admin-flowchart.mmd` - Mermaid code (bisa diimport)
3. `admin-flowchart.md` - Dokumentasi lengkap

Pilih metode yang paling nyaman untuk Anda!
