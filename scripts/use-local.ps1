$root = (Resolve-Path "$PSScriptRoot\..").Path

$services = @(
    "auth-service",
    "member-service",
    "field-service",
    "booking-service",
    "notification-service",
    "web-client",
    "graphql-gateway"
)

Write-Host "Switching to LOCAL environment..." -ForegroundColor Cyan

Set-Location $root

foreach ($service in $services) {
    $source = "$root\$service\.env.local"
    $target = "$root\$service\.env"

    if (!(Test-Path $source)) {
        Write-Host "Missing $source" -ForegroundColor Red
        exit 1
    }

    Copy-Item $source $target -Force
    Write-Host "Activated local env: $service"
}

Write-Host ""
Write-Host "Stopping full Docker stack..." -ForegroundColor Yellow
docker compose down

Write-Host ""
Write-Host "Starting local support containers: redis, hasura-db, hasura..." -ForegroundColor Yellow
docker compose up -d redis hasura-db hasura

Write-Host ""
Write-Host "Clearing Laravel config/cache..." -ForegroundColor Yellow

foreach ($service in $services) {
    Push-Location "$root\$service"
    php artisan optimize:clear
    Pop-Location
}

Write-Host ""
Write-Host "LOCAL environment is active." -ForegroundColor Green
Write-Host "Make sure XAMPP MySQL is running."
Write-Host "Run scripts\migrate-local.ps1 if database needs reset."
Write-Host "Run scripts\start-local.ps1 to start local services."
