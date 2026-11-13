@echo off
setlocal
set "PHP_EXE=C:\xampp\php\php.exe"
if not exist "%PHP_EXE%" set "PHP_EXE=C:\xamp\php\php.exe"
if not exist "%PHP_EXE%" (
  echo No encuentro PHP de XAMPP. Edita iniciar_chatbot.bat e indica la ruta a php.exe.
  pause
  exit /b 1
)
cd /d "%~dp0"
echo Chatbot disponible en http://127.0.0.1:8787
start "" http://127.0.0.1:8787
"%PHP_EXE%" -S 127.0.0.1:8787 -t "%~dp0public"
pause
