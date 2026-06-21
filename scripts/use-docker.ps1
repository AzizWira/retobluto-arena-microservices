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

function Refresh-HasuraReporting {
    $sqlPath = "$root\hasura\local\schema\reporting-schema.sql"
    $hasuraDbContainer = "retobluto_hasura_db"
    $hasuraUrl = "http://127.0.0.1:8080"
    $hasuraAdminSecret = "retobluto_admin_secret"

    if (!(Test-Path $sqlPath)) {
        Write-Host "Hasura reporting SQL not found: $sqlPath" -ForegroundColor Red
        exit 1
    }

    Write-Host ""
    Write-Host "Waiting for Hasura DB..." -ForegroundColor Yellow

    $dbReady = $false

    for ($i = 1; $i -le 30; $i++) {
        docker exec $hasuraDbContainer pg_isready -U postgres -d hasura_db *> $null

        if ($LASTEXITCODE -eq 0) {
            $dbReady = $true
            break
        }

        Start-Sleep -Seconds 2
    }

    if (-not $dbReady) {
        Write-Host "Hasura DB is not ready." -ForegroundColor Red
        exit 1
    }

    Write-Host "Refreshing Hasura reporting schema..." -ForegroundColor Yellow

    Get-Content -Raw $sqlPath | docker exec -i $hasuraDbContainer psql -U hasura_user -d hasura_db

    if ($LASTEXITCODE -ne 0) {
        Write-Host "Failed to run Hasura reporting schema." -ForegroundColor Red
        exit 1
    }

    Write-Host "Waiting for Hasura metadata API..." -ForegroundColor Yellow

    $hasuraReady = $false

    for ($i = 1; $i -le 30; $i++) {
        try {
            Invoke-RestMethod -Method Get -Uri "$hasuraUrl/healthz" | Out-Null
            $hasuraReady = $true
            break
        } catch {
            Start-Sleep -Seconds 2
        }
    }

    if (-not $hasuraReady) {
        Write-Host "Hasura API is not ready." -ForegroundColor Red
        exit 1
    }

    $headers = @{
        "x-hasura-admin-secret" = $hasuraAdminSecret
        "Content-Type" = "application/json"
    }

    $objects = @(
        "report_fields",
        "report_members",
        "report_bookings",
        "report_notification_logs",
        "v_field_report",
        "v_member_report",
        "v_booking_report",
        "v_notification_report",
        "v_dashboard_summary"
    )

    Write-Host "Tracking Hasura tables/views..." -ForegroundColor Yellow

    foreach ($object in $objects) {
        $body = @{
            type = "pg_track_table"
            args = @{
                source = "default"
                table = @{
                    schema = "public"
                    name = $object
                }
            }
        } | ConvertTo-Json -Depth 10

        try {
            Invoke-RestMethod `
                -Method Post `
                -Uri "$hasuraUrl/v1/metadata" `
                -Headers $headers `
                -Body $body | Out-Null

            Write-Host "Tracked: $object" -ForegroundColor Green
        } catch {
            Write-Host "Skip/Already tracked: $object" -ForegroundColor DarkYellow
        }
    }

    Write-Host "Reloading Hasura metadata..." -ForegroundColor Yellow

    $reloadBody = @{
        type = "reload_metadata"
        args = @{}
    } | ConvertTo-Json -Depth 10

    Invoke-RestMethod `
        -Method Post `
        -Uri "$hasuraUrl/v1/metadata" `
        -Headers $headers `
        -Body $reloadBody | Out-Null

    Write-Host "Hasura reporting is ready." -ForegroundColor Green
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

Refresh-HasuraReporting

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