$root = (Resolve-Path "$PSScriptRoot\..").Path

Set-Location $root

Write-Host "Running DOCKER migrations..." -ForegroundColor Cyan

docker exec -it retobluto_auth_service php artisan migrate:fresh --seed
docker exec -it retobluto_member_service php artisan migrate:fresh --seed
docker exec -it retobluto_field_service php artisan migrate:fresh --seed
docker exec -it retobluto_booking_service php artisan migrate:fresh --seed
docker exec -it retobluto_notification_service php artisan migrate:fresh

Write-Host ""
Write-Host "Flushing Redis..." -ForegroundColor Yellow
docker exec -it retobluto_redis redis-cli FLUSHALL

Write-Host ""
Write-Host "Restarting Docker services..." -ForegroundColor Yellow
docker compose restart auth-service member-service field-service booking-service notification-service notification-worker web-client graphql-gateway

Write-Host ""
Write-Host "DOCKER migrations completed." -ForegroundColor Green
