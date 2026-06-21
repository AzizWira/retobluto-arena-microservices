$root = (Resolve-Path "$PSScriptRoot\..").Path

function Test-DockerDaemon {
    try {
        docker info *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Run-LocalArtisan {
    param (
        [string]$Service,
        [string]$Command
    )

    Push-Location "$root\$Service"

    $arguments = @("artisan") + ($Command -split " ")
    & php @arguments

    $exitCode = $LASTEXITCODE
    Pop-Location

    if ($exitCode -ne 0) {
        Write-Host "Warning: failed to run php artisan $Command in $Service" -ForegroundColor Yellow
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

Write-Host "Switching to LOCAL environment..." -ForegroundColor Cyan

Set-Location $root

if (-not (Test-DockerDaemon)) {
    Write-Host ""
    Write-Host "Docker Desktop is not running or Docker daemon is not ready." -ForegroundColor Red
    Write-Host "Local mode still needs Docker for Redis and Hasura." -ForegroundColor Yellow
    Write-Host "Please open Docker Desktop, wait until it is ready, then run this script again."
    exit 1
}

foreach ($service in $services) {
    $source = "$root\$service\.env.xampp"
    $target = "$root\$service\.env"

    if (!(Test-Path $source)) {
        Write-Host "Missing $source" -ForegroundColor Red
        Write-Host "Use .env.xampp for local XAMPP environment. Do not use .env.local." -ForegroundColor Yellow
        exit 1
    }

    Copy-Item $source $target -Force
    Write-Host "Activated local env: $service"
}

Write-Host ""
Write-Host "Stopping full Docker stack..." -ForegroundColor Yellow
docker compose down --remove-orphans

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to stop Docker stack." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Starting local support containers: redis, hasura-db, hasura..." -ForegroundColor Yellow
docker compose up -d redis hasura-db hasura

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to start local support containers." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Clearing Laravel config/cache..." -ForegroundColor Yellow

foreach ($service in $services) {
    Run-LocalArtisan $service "optimize:clear"
}

Write-Host ""
Write-Host "LOCAL environment is active." -ForegroundColor Green
Write-Host "Make sure XAMPP MySQL is running."
Write-Host "Run .\scripts\migrate-local.ps1 if database needs reset."
Write-Host "Run .\scripts\start-local.ps1 to start local services."