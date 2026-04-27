@echo off
echo Restarting VS Code...
taskkill /f /im Code.exe >nul 2>&1
timeout /t 2 >nul
start "" "C:\Users\%USERNAME%\AppData\Local\Programs\Microsoft VS Code\Code.exe" "%~dp0.."