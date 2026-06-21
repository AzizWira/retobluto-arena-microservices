$root = (Resolve-Path "$PSScriptRoot\..").Path

function Test-DockerDaemon {
    try {
        docker info *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Run-DockerArtisan {
    param (
        [string]$Container,
        [string]$Command
    )

    Write-Host ""
    Write-Host "Running in ${Container}: php artisan ${Command}" -ForegroundColor Yellow

    docker exec $Container sh -lc "php artisan $Command"

    if ($LASTEXITCODE -ne 0) {
        Write-Host ""
        Write-Host "Command failed in ${Container}: php artisan ${Command}" -ForegroundColor Red
        exit 1
    }
}

Set-Location $root

if (-not (Test-DockerDaemon)) {
    Write-Host "Docker Desktop is not running or Docker daemon is not ready." -ForegroundColor Red
    exit 1
}

Write-Host "Running DOCKER migrations..." -ForegroundColor Cyan

Run-DockerArtisan "retobluto_auth_service" "migrate:fresh --seed"
Run-DockerArtisan "retobluto_member_service" "migrate:fresh --seed"
Run-DockerArtisan "retobluto_field_service" "migrate:fresh --seed"
Run-DockerArtisan "retobluto_booking_service" "migrate:fresh --seed"

Run-DockerArtisan "retobluto_notification_service" "migrate:fresh"
Run-DockerArtisan "retobluto_notification_service" "db:seed"

Write-Host ""
Write-Host "Flushing Redis..." -ForegroundColor Yellow
docker exec retobluto_redis redis-cli FLUSHALL

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to flush Redis." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Restarting Docker services..." -ForegroundColor Yellow
docker compose restart auth-service member-service field-service booking-service notification-service notification-worker web-client graphql-gateway

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to restart Docker services." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "DOCKER migrations completed." -ForegroundColor Green