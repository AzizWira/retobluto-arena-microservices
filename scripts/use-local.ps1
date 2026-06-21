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

Refresh-HasuraReporting

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