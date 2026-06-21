$root = (Resolve-Path "$PSScriptRoot\..").Path

function Test-DockerDaemon {
    try {
        docker info *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Remove-EnvLocalIfExists {
    param (
        [string]$Service
    )

    $envLocal = "$root\$Service\.env.local"
    $envXampp = "$root\$Service\.env.xampp"

    if (Test-Path $envLocal) {
        Write-Host ""
        Write-Host "Detected $Service\.env.local. This file can override Docker env when APP_ENV=local." -ForegroundColor Yellow

        if (!(Test-Path $envXampp)) {
            Move-Item $envLocal $envXampp -Force
            Write-Host "Renamed $Service\.env.local to $Service\.env.xampp" -ForegroundColor Green
        } else {
            Remove-Item $envLocal -Force
            Write-Host "Removed $Service\.env.local because $Service\.env.xampp already exists." -ForegroundColor Green
        }
    }
}

function Run-DockerArtisan {
    param (
        [string]$Container,
        [string]$Command
    )

    docker exec $Container sh -lc "php artisan $Command"

    if ($LASTEXITCODE -ne 0) {
        Write-Host "Warning: failed to run php artisan $Command in $Container" -ForegroundColor Yellow
    }
}

$services = @(
    "auth-service",
    "member-service",
    "field-service",
    "booking-service",
    "notification-service",
    "web-client",
    "graphql-gateway"
)

Write-Host "Switching to DOCKER environment..." -ForegroundColor Cyan

Set-Location $root

if (-not (Test-DockerDaemon)) {
    Write-Host ""
    Write-Host "Docker Desktop is not running or Docker daemon is not ready." -ForegroundColor Red
    Write-Host "Please open Docker Desktop and wait until it is fully running." -ForegroundColor Yellow
    Write-Host "Then run this command again:" -ForegroundColor Yellow
    Write-Host ".\scripts\use-docker.ps1"
    exit 1
}

foreach ($service in $services) {
    Remove-EnvLocalIfExists $service
}

foreach ($service in $services) {
    $source = "$root\$service\.env.docker"
    $target = "$root\$service\.env"

    if (!(Test-Path $source)) {
        Write-Host "Missing $source" -ForegroundColor Red
        exit 1
    }

    Copy-Item $source $target -Force
    Write-Host "Activated docker env: $service"
}

Write-Host ""
Write-Host "Starting full Docker stack..." -ForegroundColor Yellow

docker compose down --remove-orphans

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to stop Docker stack." -ForegroundColor Red
    exit 1
}

docker compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to start Docker stack." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Waiting containers to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

Write-Host ""
Write-Host "Clearing Laravel config/cache inside containers..." -ForegroundColor Yellow

Run-DockerArtisan "retobluto_auth_service" "optimize:clear"
Run-DockerArtisan "retobluto_member_service" "optimize:clear"
Run-DockerArtisan "retobluto_field_service" "optimize:clear"
Run-DockerArtisan "retobluto_booking_service" "optimize:clear"
Run-DockerArtisan "retobluto_notification_service" "optimize:clear"
Run-DockerArtisan "retobluto_web_client" "optimize:clear"
Run-DockerArtisan "retobluto_graphql_gateway" "optimize:clear"

Write-Host ""
Write-Host "DOCKER environment is active." -ForegroundColor Green
Write-Host "Open             : http://localhost:8090"
Write-Host "GraphQL Gateway  : http://localhost:8010"
Write-Host "Hasura           : http://localhost:8080"