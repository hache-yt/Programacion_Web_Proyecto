@echo off
setlocal
set "PROJECT_DIR=%~dp0"
for %%I in ("%PROJECT_DIR%..\..") do set "XAMPP_DIR=%%~fI"

if not exist "%XAMPP_DIR%\php\php.exe" (
    echo No se encontro PHP en "%XAMPP_DIR%\php".
    exit /b 1
)

set "PATH=%XAMPP_DIR%\php;%XAMPP_DIR%\mysql\bin;%PATH%"
cd /d "%PROJECT_DIR%"

if not exist ".env" (
    copy ".env.example" ".env" >nul
    if errorlevel 1 (
        echo No se pudo crear .env desde .env.example.
        exit /b 1
    )
)

if exist "%APPDATA%\Composer\latest.phar" (
    if exist "%XAMPP_DIR%\php\ext\php_zip.dll" (
        php -d extension=zip "%APPDATA%\Composer\latest.phar" install
    ) else (
        php "%APPDATA%\Composer\latest.phar" install
    )
) else (
    where composer >nul 2>nul
    if errorlevel 1 (
        echo No se encontro Composer. Instala Composer o agrega composer al PATH.
        exit /b 1
    ) else (
        call composer install
    )
)
if errorlevel 1 (
    echo Fallo la instalacion de dependencias con Composer.
    exit /b 1
)

findstr /r /c:"^APP_KEY=$" ".env" >nul
if not errorlevel 1 (
    php artisan key:generate
    if errorlevel 1 (
        echo No se pudo generar APP_KEY en .env.
        exit /b 1
    )
)

if not exist "%XAMPP_DIR%\mysql\bin\mysql.exe" (
    echo No se encontro MySQL en "%XAMPP_DIR%\mysql\bin".
    exit /b 1
)
"%XAMPP_DIR%\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS riego_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
if errorlevel 1 (
    echo No se pudo crear la base de datos. Verifica que MySQL este iniciado.
    exit /b 1
)

php artisan migrate --seed --force
if errorlevel 1 (
    echo Fallaron las migraciones. Verifica la configuracion de .env y MySQL.
    exit /b 1
)
php artisan serve
pause
