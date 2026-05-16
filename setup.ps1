# One-time setup after git clone on Windows + XAMPP
$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "Created .env from .env.example"
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Error "Composer not found. Install from https://getcomposer.org"
}

composer install --no-interaction

php artisan config:clear
if (-not (Test-Path public\storage)) {
    php artisan storage:link
}

Write-Host ""
Write-Host "Done. Open http://localhost/GG-TP/"
Write-Host "Trader portal: http://localhost/GG-TP/trader-portal/login.php"
Write-Host "Ensure PHP OCI8 is enabled and Oracle at 192.168.1.64 is reachable."
