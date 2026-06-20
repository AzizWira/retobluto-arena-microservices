$root = (Resolve-Path "$PSScriptRoot\..").Path

Write-Host "Stopping local PHP artisan processes for this project..." -ForegroundColor Cyan

$processes = Get-CimInstance Win32_Process |
    Where-Object {
        $_.Name -eq "php.exe" -and
        $_.CommandLine -like "*artisan*" -and
        $_.CommandLine -like "*retobluto-arena-microservices*"
    }

if ($processes.Count -eq 0) {
    Write-Host "No local artisan process found." -ForegroundColor Yellow
} else {
    foreach ($process in $processes) {
        Write-Host "Stopping PID $($process.ProcessId): $($process.CommandLine)"
        Stop-Process -Id $process.ProcessId -Force
    }
}

Write-Host ""
Write-Host "Local artisan processes stopped." -ForegroundColor Green
