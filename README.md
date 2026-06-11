# Retobluto Arena Microservices

Retobluto Arena Microservices adalah sistem booking lapangan olahraga berbasis microservices. Sistem ini mendukung autentikasi admin dan member, registrasi member dengan OTP email, pengelolaan lapangan, pengelolaan member, proses booking lapangan, approval booking oleh admin, cancel booking oleh member, notifikasi berbasis Redis, RESTful API, GraphQL Gateway manual, Hasura GraphQL reporting, dan deployment menggunakan Docker.

## Fitur Utama

- Login admin
- Login member
- Register member menggunakan OTP email
- Resend OTP
- Manajemen member oleh admin
- Manajemen lapangan oleh admin
- List lapangan untuk member
- Booking lapangan oleh member
- Approval booking oleh admin
- Reject booking oleh admin dengan alasan
- Cancel booking oleh member
- Validasi konflik jadwal booking
- Validasi member inactive dan blocked
- Notification log
- RESTful API pada setiap service
- GraphQL Gateway manual berbasis Laravel
- Hasura GraphQL untuk reporting
- Docker Compose deployment
- Database terpisah per service
- Redis sebagai message broker

## Teknologi

- Laravel
- PHP 8.3
- MySQL
- PostgreSQL
- Redis
- Docker
- Docker Compose
- RESTful API
- GraphQL
- Hasura GraphQL Engine

## Struktur Project

```text
retobluto-arena-microservices/
├── auth-service/
├── member-service/
├── field-service/
├── booking-service/
├── notification-service/
├── web-client/
├── graphql-gateway/
├── hasura/
│   └── local/
│       ├── schema/
│       └── queries/
├── docs/
├── docker-compose.yml
└── README.md
```

## Daftar Service

| Service              | Port | Deskripsi                                                |
| -------------------- | ---: | -------------------------------------------------------- |
| auth-service         | 8001 | Login admin/member, register member, OTP, validasi token |
| member-service       | 8002 | Manajemen data member dan profil member                  |
| field-service        | 8003 | Manajemen data lapangan                                  |
| booking-service      | 8004 | Booking lapangan, approve, reject, cancel                |
| notification-service | 8005 | OTP email dan log notifikasi                             |
| notification-worker  |    - | Worker Redis untuk memproses notifikasi                  |
| web-client           | 8090 | UI admin dan member                                      |
| graphql-gateway      | 8010 | GraphQL Gateway manual berbasis Laravel                  |
| hasura               | 8080 | Hasura GraphQL untuk reporting                           |

## Database

| Service              | Database        | Engine     |
| -------------------- | --------------- | ---------- |
| auth-service         | auth_db         | MySQL      |
| member-service       | member_db       | MySQL      |
| field-service        | field_db        | MySQL      |
| booking-service      | booking_db      | MySQL      |
| notification-service | notification_db | MySQL      |
| hasura               | hasura_db       | PostgreSQL |

## Endpoint Penting

| Aplikasi           | URL                                |
| ------------------ | ---------------------------------- |
| Web Client         | http://localhost:8090              |
| Admin Login        | http://localhost:8090/login/master |
| Member Login       | http://localhost:8090/login        |
| Member Register    | http://localhost:8090/register     |
| GraphQL Playground | http://localhost:8010/playground   |
| GraphQL API        | http://localhost:8010/api/graphql  |
| Hasura Console     | http://localhost:8080              |

## Akun Admin Default

```text
Email    : admin@retobluto.test
Password : password
```

## Cara Menjalankan Project

### 1. Siapkan file `.env`

Copy `.env.example` menjadi `.env` pada setiap service:

```bash
cp auth-service/.env.example auth-service/.env
cp member-service/.env.example member-service/.env
cp field-service/.env.example field-service/.env
cp booking-service/.env.example booking-service/.env
cp notification-service/.env.example notification-service/.env
cp web-client/.env.example web-client/.env
cp graphql-gateway/.env.example graphql-gateway/.env
```

### 2. Install dependency

```bash
docker compose run --rm auth-service composer install
docker compose run --rm member-service composer install
docker compose run --rm field-service composer install
docker compose run --rm booking-service composer install
docker compose run --rm notification-service composer install
docker compose run --rm web-client composer install
docker compose run --rm graphql-gateway composer install
```

### 3. Generate key dan JWT secret

```bash
docker compose run --rm auth-service php artisan key:generate
docker compose run --rm member-service php artisan key:generate
docker compose run --rm field-service php artisan key:generate
docker compose run --rm booking-service php artisan key:generate
docker compose run --rm notification-service php artisan key:generate
docker compose run --rm web-client php artisan key:generate
docker compose run --rm graphql-gateway php artisan key:generate

docker compose run --rm auth-service php artisan jwt:secret
```

### 4. Jalankan Docker

```bash
docker compose up -d --build
```

### 5. Jalankan migration dan seeder

Jalankan migration untuk semua service utama:

```bash
docker compose exec auth-service php artisan migrate
docker compose exec member-service php artisan migrate
docker compose exec field-service php artisan migrate
docker compose exec booking-service php artisan migrate
docker compose exec notification-service php artisan migrate
```

Jalankan seeder hanya untuk service yang memiliki data awal:

```bash
docker compose exec auth-service php artisan db:seed
docker compose exec member-service php artisan db:seed
docker compose exec field-service php artisan db:seed
```

Seeder Auth Service membuat akun admin default. Seeder Member Service dan Field Service menyiapkan data contoh untuk kebutuhan testing awal.

### 6. Akses web

```text
http://localhost:8090
```

## Setup Hasura Local

Buka Hasura:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

Jalankan SQL dari file:

```text
hasura/local/schema/reporting-schema.sql
```

Lalu track table dan view:

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

## GraphQL Gateway Manual

GraphQL Gateway berjalan pada:

```text
http://localhost:8010
```

Playground:

```text
http://localhost:8010/playground
```

Endpoint:

```text
http://localhost:8010/api/graphql
```

Contoh query health:

```graphql
query {
  health {
    auth {
      ok
      status
      message
    }
    member {
      ok
      status
      message
    }
    field {
      ok
      status
      message
    }
    booking {
      ok
      status
      message
    }
    notification {
      ok
      status
      message
    }
  }
}
```

## Flow Utama Sistem

```text
1. Admin login.
2. Admin mengelola data lapangan.
3. Admin menambahkan member.
4. Member register atau login.
5. Member melakukan verifikasi OTP.
6. Member memilih lapangan.
7. Member membuat booking.
8. Booking masuk dengan status pending.
9. Admin approve atau reject booking.
10. Member melihat status booking.
11. Member dapat cancel booking.
12. Notification Service mencatat log notifikasi.
13. GraphQL Gateway digunakan untuk akses GraphQL manual.
14. Hasura digunakan untuk reporting.
```

## Dokumentasi

| Dokumen                       | Keterangan                                     |
| ----------------------------- | ---------------------------------------------- |
| docs/architecture.md          | Dokumentasi arsitektur                         |
| docs/deployment-guide.md      | Panduan menjalankan project                    |
| docs/api-endpoints.md         | Daftar endpoint REST, Web, GraphQL, dan Hasura |
| docs/final-testing.md         | Skenario testing final                         |
| docs/graphql-testing.md       | Panduan testing GraphQL Gateway                |
| docs/hasura-testing.md        | Panduan testing Hasura                         |
| docs/requirement-checklist.md | Checklist requirement final project            |

## Pembagian Tanggung Jawab Service

| Service              | Penanggung Jawab            |
| -------------------- | --------------------------- |
| auth-service         | Aura Iftitah                |
| member-service       | Ryan Alvin Saputra          |
| field-service        | Muhammad Agil Hidayahtullah |
| booking-service      | Ahmad Aziz Wira Widodo      |
| notification-service | Ryan Alvin Saputra          |
| graphql-gateway      | Tim                         |
| hasura               | Tim                         |

## Status Project

Project sudah mencakup:

```text
✅ Microservices
✅ RESTful API
✅ Docker Compose
✅ Database terpisah
✅ Redis message broker
✅ Web Client
✅ GraphQL Gateway manual berbasis Laravel
✅ Hasura GraphQL reporting
✅ Dokumentasi deployment
✅ Dokumentasi testing
```
