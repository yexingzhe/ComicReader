@echo off
for %%i in (%0) do set DIR=%%~dpi
%DIR%php %DIR%pic.php --path=%1
%DIR%pic.hta