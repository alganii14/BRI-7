@echo off
echo ========================================
echo Import Non Debitur Vol Besar CASA Kecil
echo ========================================
echo.

REM Cek apakah file CSV ada
if not exist "Non Debitur Vol Besar CASA Kecil.csv" (
    echo ERROR: File "Non Debitur Vol Besar CASA Kecil.csv" tidak ditemukan!
    echo Pastikan file CSV ada di folder yang sama dengan file .bat ini
    pause
    exit /b 1
)

echo File CSV ditemukan: Non Debitur Vol Besar CASA Kecil.csv
echo.
echo Memulai import...
echo.

REM Jalankan command import
php artisan import:non-debitur-vol-besar "Non Debitur Vol Besar CASA Kecil.csv"

echo.
echo ========================================
echo Import selesai!
echo ========================================
pause
