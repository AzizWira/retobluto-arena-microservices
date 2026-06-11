# Deployment Guide

Panduan ini digunakan untuk menjalankan Retobluto Arena Microservices secara lokal menggunakan Docker Compose.

## Prasyarat

Pastikan perangkat sudah memiliki:

- Docker
- Docker Compose
- Git
- Browser
- Terminal atau PowerShell

## 1. Masuk ke Folder Project

```bash
cd retobluto-arena-microservices
```

## 2. Siapkan File Environment

Setiap service Laravel membutuhkan file `.env`.

Jika `.env` belum ada, salin dari `.env.example`:

```bash
cp auth-service/.env.example auth-service/.env
cp member-service/.env.example member-service/.env
cp field-service/.env.example field-service/.env
cp booking-service/.env.example booking-service/.env
cp notification-service/.env.example notification-service/.env
cp web-client/.env.example web-client/.env
cp graphql-gateway/.env.example graphql-gateway/.env
```

Pada Windows PowerShell, gunakan:

```powershell
Copy-Item auth-service/.env.example auth-service/.env
Copy-Item member-service/.env.example member-service/.env
Copy-Item field-service/.env.example field-service/.env
Copy-Item booking-service/.env.example booking-service/.env
Copy-Item notification-service/.env.example notification-service/.env
Copy-Item web-client/.env.example web-client/.env
Copy-Item graphql-gateway/.env.example graphql-gateway/.env
```

## 3. Install Dependency Laravel

Karena folder service di-mount sebagai volume Docker, jalankan composer install untuk setiap service:

```bash
docker compose run --rm auth-service composer install
docker compose run --rm member-service composer install
docker compose run --rm field-service composer install
docker compose run --rm booking-service composer install
docker compose run --rm notification-service composer install
docker compose run --rm web-client composer install
docker compose run --rm graphql-gateway composer install
```

## 4. Generate APP_KEY

```bash
docker compose run --rm auth-service php artisan key:generate
docker compose run --rm member-service php artisan key:generate
docker compose run --rm field-service php artisan key:generate
docker compose run --rm booking-service php artisan key:generate
docker compose run --rm notification-service php artisan key:generate
docker compose run --rm web-client php artisan key:generate
docker compose run --rm graphql-gateway php artisan key:generate
```

## 5. Generate JWT Secret untuk Auth Service

```bash
docker compose run --rm auth-service php artisan jwt:secret
```

## 6. Build dan Jalankan Container

```bash
docker compose up -d --build
```

Cek container:

```bash
docker compose ps
```

Container penting yang harus berjalan:

```text
retobluto_auth_db
retobluto_member_db
retobluto_field_db
retobluto_booking_db
retobluto_notification_db
retobluto_redis
retobluto_auth_service
retobluto_member_service
retobluto_field_service
retobluto_booking_service
retobluto_notification_service
retobluto_notification_worker
retobluto_web_client
retobluto_graphql_gateway
retobluto_hasura_db
retobluto_hasura
```

## 7. Jalankan Migration dan Seeder

```bash
docker compose exec auth-service php artisan migrate --seed
docker compose exec member-service php artisan migrate --seed
docker compose exec field-service php artisan migrate --seed
docker compose exec booking-service php artisan migrate --seed
docker compose exec notification-service php artisan migrate --seed
```

## 8. Akun Admin Default

Seeder Auth Service membuat akun admin default:

```text
Email    : admin@retobluto.test
Password : password
Role     : admin
```

## 9. Akses Web Client

Buka:

```text
http://localhost:8090
```

Halaman admin:

```text
http://localhost:8090/login/master
```

Halaman member:

```text
http://localhost:8090/login
```

## 10. Akses GraphQL Gateway Manual

Playground:

```text
http://localhost:8010/playground
```

Endpoint API:

```text
http://localhost:8010/api/graphql
```

Schema:

```text
http://localhost:8010/api/graphql/schema
```

Health check:

```text
http://localhost:8010/api/health
```

## 11. Akses Hasura Local

Buka:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

Jalankan SQL schema Hasura dari file:

```text
hasura/local/schema/reporting-schema.sql
```

Langkah:

```text
Data -> SQL -> paste SQL -> Run
```

Setelah itu track table dan view pada menu:

```text
Data -> public
```

Track:

```text
report_fields
report_members
report_bookings
report_notification_logs
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

## 12. Port Service

| Service              | URL                   |
| -------------------- | --------------------- |
| Web Client           | http://localhost:8090 |
| Auth Service         | http://localhost:8001 |
| Member Service       | http://localhost:8002 |
| Field Service        | http://localhost:8003 |
| Booking Service      | http://localhost:8004 |
| Notification Service | http://localhost:8005 |
| GraphQL Gateway      | http://localhost:8010 |
| Hasura Console       | http://localhost:8080 |

## 13. Stop Project

```bash
docker compose down
```

Jika ingin menghapus volume database:

```bash
docker compose down -v
```

Gunakan `down -v` hanya jika ingin reset seluruh data.
