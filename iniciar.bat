@echo off
setlocal
set "PROJECT_DIR=%~dp0"
for %%I in ("%PROJECT_DIR%..\..") do set "XAMPP_DIR=%%~fI"

if not exist "%PROJECT_DIR%vendor\autoload.php" (
    echo No se encontraron las dependencias de Laravel.
    echo Ejecuta instalar.bat para instalarlas.
    pause
    exit /b 1
)

set "PATH=%XAMPP_DIR%\php;%PATH%"
cd /d "%PROJECT_DIR%"
php artisan serve
pause
