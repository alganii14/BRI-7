@echo off
echo ========================================
echo Import Data Strategi 8 - Wingback
echo ========================================
echo.

REM Set path to wingback.csv
set CSV_FILE=wingback.csv

REM Check if CSV file exists
if not exist "%CSV_FILE%" (
    echo [ERROR] File %CSV_FILE% tidak ditemukan!
    echo Pastikan file %CSV_FILE% ada di folder ini.
    pause
    exit /b 1
)

echo [INFO] File CSV ditemukan: %CSV_FILE%
echo [INFO] Memulai proses import...
echo.

REM Run artisan command
php artisan db:seed --class=ImportWingbackSeeder

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo [SUCCESS] Import data berhasil!
    echo ========================================
) else (
    echo.
    echo ========================================
    echo [ERROR] Import data gagal!
    echo ========================================
)

echo.
pause
