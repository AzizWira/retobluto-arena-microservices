$root = (Resolve-Path "$PSScriptRoot\..").Path

Write-Host "Starting local support containers..." -ForegroundColor Cyan
Set-Location $root
docker compose up -d redis hasura-db hasura

Write-Host ""
Write-Host "Starting local Laravel services in separate PowerShell windows..." -ForegroundColor Cyan

$commands = @(
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

foreach ($item in $commands) {
    $title = $item.Title
    $path = $item.Path
    $command = $item.Command

    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$path'; `$host.UI.RawUI.WindowTitle = '$title'; $command"
    Start-Sleep -Milliseconds 400
}

Write-Host ""
Write-Host "Local services started." -ForegroundColor Green
Write-Host "Web Client: http://127.0.0.1:8090"
Write-Host "GraphQL Gateway: http://127.0.0.1:8010"
Write-Host "Hasura: http://localhost:8080"
