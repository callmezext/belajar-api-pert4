@echo off
echo ==============================================
echo   Menjalankan Enchant AI Gateway & Laravel
echo ==============================================

echo Memulai Backend Express pada port 5000...
start "Express Backend (Port 5000)" cmd /k "cd /d %~dp0backend && node server.js"

timeout /t 2 /nobreak >nul

echo Memulai Frontend Laravel pada port 8001...
start "Laravel Frontend (Port 8001)" cmd /k "cd /d %~dp0frontend && php artisan serve --host=127.0.0.1 --port=8001"

echo.
echo Kedua server sedang berjalan!
echo Buka di browser: http://127.0.0.1:8001
echo ==============================================
pause
