param(
    [switch]$KeepDocker
)

$root = (Resolve-Path "$PSScriptRoot\..").Path

function Test-DockerDaemon {
    try {
        docker info *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

Write-Host "Stopping local Laravel artisan services..." -ForegroundColor Cyan

$ports = @(8001, 8002, 8003, 8004, 8005, 8090, 8010)
$stoppedPids = @{}

foreach ($port in $ports) {
    $connections = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue

    foreach ($connection in $connections) {
        $pidValue = $connection.OwningProcess

        if ($pidValue -and -not $stoppedPids.ContainsKey($pidValue)) {
            $process = Get-Process -Id $pidValue -ErrorAction SilentlyContinue

            if ($process) {
                if ($process.ProcessName -eq "php") {
                    Write-Host "Stopping Laravel service on port $port | PID $pidValue | $($process.ProcessName)" -ForegroundColor Yellow
                    Stop-Process -Id $pidValue -Force
                    $stoppedPids[$pidValue] = $true
                } else {
                    Write-Host "Skipping port $port | PID $pidValue | $($process.ProcessName). This is not a local PHP artisan process." -ForegroundColor DarkYellow
                }
            }
        }
    }
}

Write-Host ""
Write-Host "Stopping notification Redis listener..." -ForegroundColor Cyan

$workerProcesses = Get-CimInstance Win32_Process |
    Where-Object {
        $_.Name -eq "php.exe" -and
        $_.CommandLine -like "*artisan*" -and
        $_.CommandLine -like "*notifications:listen-redis*"
    }

foreach ($worker in $workerProcesses) {
    if ($worker.ProcessId -and -not $stoppedPids.ContainsKey($worker.ProcessId)) {
        Write-Host "Stopping notification worker | PID $($worker.ProcessId)" -ForegroundColor Yellow
        Stop-Process -Id $worker.ProcessId -Force
        $stoppedPids[$worker.ProcessId] = $true
    }
}

if ($stoppedPids.Count -eq 0) {
    Write-Host "No local PHP artisan service process found." -ForegroundColor Yellow
} else {
    Write-Host ""
    Write-Host "Stopped $($stoppedPids.Count) local process(es)." -ForegroundColor Green
}

if (-not $KeepDocker) {
    Write-Host ""
    Write-Host "Stopping local support containers: redis, hasura, hasura-db..." -ForegroundColor Cyan

    if (Test-DockerDaemon) {
        Set-Location $root
        docker compose stop redis hasura hasura-db

        if ($LASTEXITCODE -eq 0) {
            Write-Host "Support containers stopped." -ForegroundColor Green
        } else {
            Write-Host "Failed to stop support containers." -ForegroundColor Red
        }
    } else {
        Write-Host "Docker Desktop is not running. Skipping support container stop." -ForegroundColor Yellow
    }
} else {
    Write-Host ""
    Write-Host "KeepDocker enabled. Redis and Hasura containers are still running." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Local stop completed." -ForegroundColor Green