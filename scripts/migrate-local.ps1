$root = (Resolve-Path "$PSScriptRoot\..").Path

function Run-LocalArtisan {
    param (
        [string]$Service,
        [string]$Command
    )

    Write-Host ""
    Write-Host "Running in ${Service}: php artisan ${Command}" -ForegroundColor Yellow

    Push-Location "$root\$Service"

    $arguments = @("artisan") + ($Command -split " ")
    & php @arguments

    $exitCode = $LASTEXITCODE
    Pop-Location

    if ($exitCode -ne 0) {
        Write-Host ""
        Write-Host "Command failed in ${Service}: php artisan ${Command}" -ForegroundColor Red
        exit 1
    }
}

Write-Host "Running LOCAL migrations..." -ForegroundColor Cyan
Write-Host "Make sure XAMPP MySQL is running." -ForegroundColor Yellow

Run-LocalArtisan "auth-service" "optimize:clear"
Run-LocalArtisan "auth-service" "migrate:fresh --seed"

Run-LocalArtisan "member-service" "optimize:clear"
Run-LocalArtisan "member-service" "migrate:fresh --seed"

Run-LocalArtisan "field-service" "optimize:clear"
Run-LocalArtisan "field-service" "migrate:fresh --seed"

Run-LocalArtisan "booking-service" "optimize:clear"
Run-LocalArtisan "booking-service" "migrate:fresh --seed"

Run-LocalArtisan "notification-service" "optimize:clear"
Run-LocalArtisan "notification-service" "migrate:fresh"
Run-LocalArtisan "notification-service" "db:seed"

Run-LocalArtisan "web-client" "optimize:clear"
Run-LocalArtisan "graphql-gateway" "optimize:clear"

Write-Host ""
Write-Host "LOCAL migrations completed." -ForegroundColor Green