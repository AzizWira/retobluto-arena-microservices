$root = (Resolve-Path "$PSScriptRoot\..").Path

function Test-DockerDaemon {
    try {
        docker info *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Test-Port {
    param (
        [string]$HostName,
        [int]$Port
    )

    try {
        $client = New-Object System.Net.Sockets.TcpClient
        $async = $client.BeginConnect($HostName, $Port, $null, $null)
        $success = $async.AsyncWaitHandle.WaitOne(1000, $false)

        if ($success) {
            $client.EndConnect($async)
            $client.Close()
            return $true
        }

        $client.Close()
        return $false
    } catch {
        return $false
    }
}

Write-Host "Starting local support containers..." -ForegroundColor Cyan

Set-Location $root

if (-not (Test-DockerDaemon)) {
    Write-Host "Docker Desktop is not running or Docker daemon is not ready." -ForegroundColor Red
    Write-Host "Local mode needs Docker for Redis and Hasura." -ForegroundColor Yellow
    exit 1
}

docker compose up -d redis hasura-db hasura

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to start local support containers." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Checking Redis port mapping..." -ForegroundColor Yellow

$redisPort = docker port retobluto_redis 2>$null

if ($redisPort -notmatch "6379") {
    Write-Host "Redis port 6379 is not exposed to host. Recreating Redis container..." -ForegroundColor Yellow

    docker compose stop redis
    docker compose rm -f redis
    docker compose up -d --force-recreate redis
}

Write-Host ""
Write-Host "Waiting for Redis container and host port..." -ForegroundColor Yellow

$redisReady = $false

for ($i = 1; $i -le 30; $i++) {
    $containerPing = docker exec retobluto_redis redis-cli ping 2>$null
    $hostPortReady = Test-Port -HostName "127.0.0.1" -Port 6379

    if (($containerPing -match "PONG") -and $hostPortReady) {
        $redisReady = $true
        Write-Host "Redis is ready on 127.0.0.1:6379." -ForegroundColor Green
        break
    }

    Write-Host "Waiting Redis... attempt $i/30"
    Start-Sleep -Seconds 1
}

if (-not $redisReady) {
    Write-Host ""
    Write-Host "Redis is not ready on 127.0.0.1:6379." -ForegroundColor Red
    Write-Host "Check this command manually:" -ForegroundColor Yellow
    Write-Host "docker port retobluto_redis"
    Write-Host "Test-NetConnection 127.0.0.1 -Port 6379"
    exit 1
}

Write-Host ""
Write-Host "Starting local Laravel services in Windows Terminal tabs..." -ForegroundColor Cyan

if (-not (Get-Command wt -ErrorAction SilentlyContinue)) {
    Write-Host "Windows Terminal command 'wt' was not found." -ForegroundColor Red
    Write-Host "Install/open Windows Terminal, or run services manually." -ForegroundColor Yellow
    exit 1
}

$tabs = @(
    @{
        Title = "auth-service"
        Path = "$root\auth-service"
        Command = "php artisan serve --host=127.0.0.1 --port=8001"
    },
    @{
        Title = "member-service"
        Path = "$root\member-service"
        Command = "php artisan serve --host=127.0.0.1 --port=8002"
    },
    @{
        Title = "field-service"
        Path = "$root\field-service"
        Command = "php artisan serve --host=127.0.0.1 --port=8003"
    },
    @{
        Title = "booking-service"
        Path = "$root\booking-service"
        Command = "php artisan serve --host=127.0.0.1 --port=8004"
    },
    @{
        Title = "notification-service"
        Path = "$root\notification-service"
        Command = "php artisan serve --host=127.0.0.1 --port=8005"
    },
    @{
        Title = "web-client"
        Path = "$root\web-client"
        Command = "php artisan serve --host=127.0.0.1 --port=8090"
    },
    @{
        Title = "graphql-gateway"
        Path = "$root\graphql-gateway"
        Command = "php artisan serve --host=127.0.0.1 --port=8010"
    },
    @{
        Title = "notification-worker"
        Path = "$root\notification-service"
        Command = "php artisan notifications:listen-redis"
    }
)

$wtArgs = @()

for ($i = 0; $i -lt $tabs.Count; $i++) {
    if ($i -gt 0) {
        $wtArgs += ";"
    }

    $tab = $tabs[$i]
    $cmdCommand = "title $($tab.Title) && cd /d `"$($tab.Path)`" && $($tab.Command)"

    $wtArgs += @(
        "new-tab",
        "--title", $tab.Title,
        "cmd",
        "/k",
        $cmdCommand
    )
}

& wt -w 0 @wtArgs

Write-Host ""
Write-Host "Local services started in Windows Terminal tabs." -ForegroundColor Green
Write-Host "Web Client      : http://127.0.0.1:8090"
Write-Host "GraphQL Gateway : http://127.0.0.1:8010"
Write-Host "Hasura          : http://localhost:8080"
Write-Host ""
Write-Host "To stop local services and support containers, run:"
Write-Host ".\scripts\stop-local.ps1"