@echo off
echo Membuka port 8000 untuk Laravel...
netsh advfirewall firewall delete rule name="Laravel Dev Server" protocol=TCP localport=8000
netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8000
echo.
echo Port 8000 sudah dibuka!
echo Sekarang jalankan: php artisan serve --host=0.0.0.0 --port=8000
pause
