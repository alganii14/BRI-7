@echo off
echo ========================================
echo Import Brilink Saldo Kurang Dari 10 Juta
echo ========================================
echo.

php artisan migrate --path=database/migrations/2025_11_28_120000_create_brilinks_table.php

echo.
echo Migrasi selesai!
echo.
pause
