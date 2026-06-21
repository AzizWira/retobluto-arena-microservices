$root = (Resolve-Path "$PSScriptRoot\..").Path

function Test-DockerDaemon {
    try {
        docker info *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

Write-Host "Stopping full Docker stack..." -ForegroundColor Cyan

Set-Location $root

if (-not (Test-DockerDaemon)) {
    Write-Host "Docker Desktop is not running or Docker daemon is not ready." -ForegroundColor Yellow
    Write-Host "Nothing to stop."
    exit 0
}

docker compose down --remove-orphans

if ($LASTEXITCODE -ne 0) {
    Write-Host "Failed to stop Docker stack." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Docker stack stopped." -ForegroundColor Green
Write-Host ""
Write-Host "If you want to switch to local mode, run:"
Write-Host ".\scripts\use-local.ps1"
Write-Host ".\scripts\start-local.ps1"