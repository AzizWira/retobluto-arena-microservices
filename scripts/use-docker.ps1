$root = (Resolve-Path "$PSScriptRoot\..").Path

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
docker compose down
docker compose up -d --build

Write-Host ""
Write-Host "Clearing Laravel config/cache inside containers..." -ForegroundColor Yellow

docker exec -it retobluto_auth_service php artisan optimize:clear
docker exec -it retobluto_member_service php artisan optimize:clear
docker exec -it retobluto_field_service php artisan optimize:clear
docker exec -it retobluto_booking_service php artisan optimize:clear
docker exec -it retobluto_notification_service php artisan optimize:clear
docker exec -it retobluto_web_client php artisan optimize:clear
docker exec -it retobluto_graphql_gateway php artisan optimize:clear

Write-Host ""
Write-Host "DOCKER environment is active." -ForegroundColor Green
Write-Host "Open: http://localhost:8090"
Write-Host "GraphQL Gateway: http://localhost:8010"
Write-Host "Hasura: http://localhost:8080"
