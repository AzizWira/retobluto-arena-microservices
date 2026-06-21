$root = (Resolve-Path "$PSScriptRoot\..").Path

Write-Host "Running LOCAL migrations..." -ForegroundColor Cyan
Write-Host "Make sure XAMPP MySQL is running." -ForegroundColor Yellow

$servicesWithSeed = @(
    "auth-service",
    "member-service",
    "field-service",
    "booking-service",
    "notification-service"
)

foreach ($service in $servicesWithSeed) {
    Write-Host ""
    Write-Host "Migrating $service..." -ForegroundColor Yellow
    Push-Location "$root\$service"
    php artisan optimize:clear
    php artisan migrate:fresh --seed
    Pop-Location
}

Write-Host ""
Write-Host "Migrating notification-service..." -ForegroundColor Yellow
Push-Location "$root\notification-service"
php artisan optimize:clear
php artisan migrate:fresh --seed
Pop-Location

Write-Host ""
Write-Host "Clearing web-client..." -ForegroundColor Yellow
Push-Location "$root\web-client"
php artisan optimize:clear
Pop-Location

Write-Host ""
Write-Host "Clearing graphql-gateway..." -ForegroundColor Yellow
Push-Location "$root\graphql-gateway"
php artisan optimize:clear
Pop-Location

Write-Host ""
Write-Host "LOCAL migrations completed." -ForegroundColor Green
