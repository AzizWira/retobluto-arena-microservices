# Retobluto Arena Microservices

Retobluto Arena Microservices adalah sistem booking lapangan olahraga berbasis microservices. Sistem ini menyediakan fitur autentikasi admin dan member, registrasi member menggunakan OTP email, pengelolaan data lapangan, pengelolaan data member, proses booking lapangan, approval dan reject booking oleh admin, cancel booking oleh member, notifikasi email, RESTful API, GraphQL Gateway manual berbasis Laravel, Hasura GraphQL untuk reporting, Redis message broker, serta deployment menggunakan Docker Compose.

Sistem ini memiliki Web Client sebagai antarmuka utama untuk admin dan member.

## Fitur Utama

```text
1. Login admin.
2. Login member.
3. Register member menggunakan OTP email.
4. Resend OTP.
5. Verifikasi OTP.
6. Manajemen member oleh admin.
7. Manajemen lapangan oleh admin.
8. List lapangan untuk member.
9. Detail lapangan.
10. Booking lapangan oleh member.
11. Booking request dengan status pending.
12. Approval booking oleh admin.
13. Reject booking oleh admin dengan alasan.
14. Cancel booking oleh member.
15. Validasi konflik jadwal booking.
16. Validasi member inactive dan blocked.
17. Validasi status lapangan.
18. Validasi jam booking sesuai jam operasional lapangan.
19. Notification log.
20. Email OTP dengan template HTML.
21. Email manual admin ke member dengan template HTML.
22. Dashboard admin.
23. Dashboard member.
24. Rekomendasi pribadi berdasarkan riwayat booking member.
25. Lapangan terpopuler berdasarkan booking approved terbanyak.
26. RESTful API pada setiap service.
27. GraphQL Gateway manual berbasis Laravel.
28. Hasura GraphQL untuk reporting.
29. Redis sebagai message broker.
30. Docker Compose deployment.
31. Script PowerShell untuk Docker Mode dan Local/XAMPP Mode.
32. Dokumentasi pembagian tanggung jawab tim.
```

## Teknologi

```text
Laravel
PHP 8.3
MySQL
PostgreSQL
Redis
Docker
Docker Compose
RESTful API
GraphQL
Hasura GraphQL Engine
PowerShell Script
```

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
│       ├── README.md
│       ├── setup-guide.md
│       ├── schema/
│       │   └── reporting-schema.sql
│       └── queries/
│           ├── booking-queries.md
│           ├── field-member-queries.md
│           ├── notification-dashboard-queries.md
│           └── query-examples.md
├── docs/
│   ├── architecture.md
│   ├── deployment-guide.md
│   ├── api-endpoints.md
│   ├── final-testing.md
│   ├── graphql-testing.md
│   ├── hasura-testing.md
│   ├── requirement-checklist.md
│   ├── script-guide.md
│   └── team-responsibilities.md
├── scripts/
│   ├── migrate-docker.ps1
│   ├── migrate-local.ps1
│   ├── start-local.ps1
│   ├── stop-docker.ps1
│   ├── stop-local.ps1
│   ├── use-docker.ps1
│   └── use-local.ps1
├── docker-compose.yml
├── .env.example
├── .gitignore
└── README.md
```

## Daftar Service

| Service              | Port | Deskripsi                                                                            |
| -------------------- | ---: | ------------------------------------------------------------------------------------ |
| auth-service         | 8001 | Login admin/member, register member, OTP, resend OTP, verifikasi OTP, validasi token |
| member-service       | 8002 | Manajemen data member dan profil member                                              |
| field-service        | 8003 | Manajemen data lapangan, status lapangan, dan jadwal lapangan                        |
| booking-service      | 8004 | Booking lapangan, booking request, approve, reject, cancel, dan popular fields       |
| notification-service | 8005 | OTP email, email manual admin, email status booking, dan log notifikasi              |
| notification-worker  |    - | Worker Redis untuk memproses event notifikasi                                        |
| web-client           | 8090 | UI utama untuk admin dan member                                                      |
| graphql-gateway      | 8010 | GraphQL Gateway manual berbasis Laravel                                              |
| hasura               | 8080 | Hasura GraphQL Engine untuk reporting                                                |
| redis                | 6379 | Message broker untuk event OTP dan notifikasi                                        |
| hasura-db            | 5433 | PostgreSQL database untuk Hasura reporting                                           |

## Database

| Service              | Database        | Engine     |
| -------------------- | --------------- | ---------- |
| auth-service         | auth_db         | MySQL      |
| member-service       | member_db       | MySQL      |
| field-service        | field_db        | MySQL      |
| booking-service      | booking_db      | MySQL      |
| notification-service | notification_db | MySQL      |
| hasura               | hasura_db       | PostgreSQL |

## Prinsip Database Per Service

Project ini menggunakan prinsip database per service. Setiap service memiliki database masing-masing sesuai domainnya.

```text
auth-service          -> auth_db
member-service        -> member_db
field-service         -> field_db
booking-service       -> booking_db
notification-service  -> notification_db
hasura                -> hasura_db
```

Service tidak mengambil data langsung dari database service lain. Jika membutuhkan data dari service lain, komunikasi dilakukan melalui REST API.

Hasura menggunakan database PostgreSQL terpisah bernama `hasura_db` untuk kebutuhan reporting/read-only query.

## Endpoint Penting

| Aplikasi           | URL                                      |
| ------------------ | ---------------------------------------- |
| Web Client         | http://localhost:8090                    |
| Admin Login        | http://localhost:8090/login/master       |
| Member Login       | http://localhost:8090/login              |
| Member Register    | http://localhost:8090/register           |
| GraphQL Playground | http://localhost:8010/playground         |
| GraphQL API        | http://localhost:8010/api/graphql        |
| GraphQL Schema     | http://localhost:8010/api/graphql/schema |
| GraphQL Health     | http://localhost:8010/api/health         |
| Hasura Console     | http://localhost:8080                    |

## Akun Demo

### Admin

```text
Email    : admin@retobluto.test
Password : password
Role     : admin
```

### Member

Password semua akun member demo:

```text
password
```

| Email                                                                                   | Nama                        | Status   |
| --------------------------------------------------------------------------------------- | --------------------------- | -------- |
| [wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)                               | Ahmad Aziz Wira Widodo      | active   |
| [auraiftitahh@gmail.com](mailto:auraiftitahh@gmail.com)                                 | Aura Iftitah                | active   |
| [muhammadagilhidayahtullah295@gmail.com](mailto:muhammadagilhidayahtullah295@gmail.com) | Muhammad Agil Hidayahtullah | active   |
| [ryanalfin6@gmail.com](mailto:ryanalfin6@gmail.com)                                     | Ryan Alvin Saputra          | active   |
| [nabila.member@example.com](mailto:nabila.member@example.com)                           | Nabila Putri Ramadhani      | inactive |
| [dimas.member@example.com](mailto:dimas.member@example.com)                             | Dimas Pratama Wijaya        | blocked  |

## Pembagian Tanggung Jawab Tim

Project ini dikerjakan oleh empat anggota tim dengan pembagian berdasarkan domain service, fitur, GraphQL Gateway, Hasura, dan dokumentasi. Berdasarkan riwayat commit, core system, Booking Service utama, Web Client, Docker, script, seeder demo, rekomendasi lapangan, Hasura base setup, dan finalisasi project berada pada Ahmad Aziz Wira Widodo sebagai penanggung jawab core project.

|  No | Nama                        | NIM          | Peran Utama                                                                                                             |
| --: | --------------------------- | ------------ | ----------------------------------------------------------------------------------------------------------------------- |
|   1 | Ahmad Aziz Wira Widodo      | 102062400112 | Core System, Booking Service, Web Client, Docker, Script, Seeder, GraphQL Integration, Hasura Base, Final Documentation |
|   2 | Aura Iftitah                | 102062400107 | Auth Service, GraphQL Gateway Core Setup, Hasura Field-Member Query, dan perbaikan dokumentasi deployment               |
|   3 | Muhammad Agil Hidayahtullah | 102062400018 | Field Service, GraphQL Field Resolver, dan Hasura Booking Query                                                         |
|   4 | Ryan Alvin Saputra          | 102062400072 | Member Service, Notification Service, GraphQL Booking Resolver, dan Hasura Notification-Dashboard Query                 |

Dokumentasi lengkap pembagian tanggung jawab tersedia pada:

```text
docs/team-responsibilities.md
```

## Cara Menjalankan Project

Project dapat dijalankan menggunakan dua mode:

```text
1. Docker Mode
2. Local/XAMPP Mode
```

Docker Mode direkomendasikan untuk demo final karena seluruh service berjalan melalui Docker Compose.

Local/XAMPP Mode digunakan untuk development lokal dengan PHP dan XAMPP MySQL dari host Windows, sedangkan Redis dan Hasura tetap berjalan melalui Docker.

## Persiapan Environment

Pada repository, file environment utama yang tersedia adalah:

```text
.env.example
```

File environment aktif seperti `.env`, `.env.docker`, dan `.env.xampp` tidak disimpan ke repository karena berisi konfigurasi lokal.

Script environment membutuhkan:

```text
.env.docker
.env.xampp
```

Keterangan:

| File          | Fungsi                                          |
| ------------- | ----------------------------------------------- |
| `.env.docker` | Environment untuk Docker Mode                   |
| `.env.xampp`  | Environment untuk Local/XAMPP Mode              |
| `.env`        | Environment aktif yang sedang digunakan service |

Jika file `.env.docker` atau `.env.xampp` belum tersedia, buat dari `.env.example` lalu sesuaikan konfigurasi database, service URL, Redis, dan mail.

## Docker Mode

Jalankan dari root project:

```powershell
.\scripts\use-docker.ps1
```

Lalu jalankan migration dan seeder:

```powershell
.\scripts\migrate-docker.ps1
```

Setelah selesai, buka:

```text
http://localhost:8090
```

Script `migrate-docker.ps1` menjalankan reset database dan seeder pada service:

```text
auth-service
member-service
field-service
booking-service
notification-service
```

Catatan:

```text
migrate-docker.ps1 menggunakan migrate:fresh sehingga seluruh data lama akan dihapus dan dibuat ulang.
```

Untuk menghentikan Docker Mode:

```powershell
.\scripts\stop-docker.ps1
```

## Local/XAMPP Mode

Pastikan XAMPP MySQL sudah berjalan.

Aktifkan Local/XAMPP Mode:

```powershell
.\scripts\use-local.ps1
```

Jalankan migration dan seeder:

```powershell
.\scripts\migrate-local.ps1
```

Jalankan semua service Laravel lokal:

```powershell
.\scripts\start-local.ps1
```

Setelah selesai, buka:

```text
http://127.0.0.1:8090
```

Untuk menghentikan Local/XAMPP Mode:

```powershell
.\scripts\stop-local.ps1
```

Jika ingin menghentikan service Laravel lokal tetapi tetap membiarkan Redis dan Hasura berjalan:

```powershell
.\scripts\stop-local.ps1 -KeepDocker
```

## Script Project

Folder `scripts/` berisi script PowerShell berikut:

| Script               | Fungsi                                                     |
| -------------------- | ---------------------------------------------------------- |
| `use-docker.ps1`     | Mengaktifkan Docker Mode dan menjalankan stack Docker      |
| `migrate-docker.ps1` | Menjalankan migration dan seeder pada Docker Mode          |
| `stop-docker.ps1`    | Menghentikan seluruh container Docker project              |
| `use-local.ps1`      | Mengaktifkan Local/XAMPP Mode                              |
| `migrate-local.ps1`  | Menjalankan migration dan seeder pada Local/XAMPP Mode     |
| `start-local.ps1`    | Menjalankan semua service Laravel lokal                    |
| `stop-local.ps1`     | Menghentikan service Laravel lokal dan container pendukung |

Dokumentasi lengkap script tersedia pada:

```text
docs/script-guide.md
```

## REST API

Setiap service menyediakan RESTful API sesuai domain masing-masing.

Base URL Docker Mode:

| Service              | Base URL                  |
| -------------------- | ------------------------- |
| Auth Service         | http://localhost:8001/api |
| Member Service       | http://localhost:8002/api |
| Field Service        | http://localhost:8003/api |
| Booking Service      | http://localhost:8004/api |
| Notification Service | http://localhost:8005/api |

Endpoint penting:

```text
POST /api/admin/login
POST /api/member/login
POST /api/member/register/request-otp
POST /api/member/register/verify
POST /api/member/register/resend-otp
POST /api/validate-token

GET  /api/members
GET  /api/members/dashboard-stats
PATCH /api/members/{id}/status

GET  /api/fields
GET  /api/fields/available
GET  /api/fields/dashboard-stats
PATCH /api/fields/{id}/status

POST /api/bookings
GET  /api/bookings
GET  /api/bookings/dashboard-stats
GET  /api/bookings/popular-fields
GET  /api/admin/booking-requests
POST /api/admin/bookings/{id}/approve
POST /api/admin/bookings/{id}/reject
POST /api/member/bookings/{id}/cancel

GET  /api/notifications/logs
POST /api/notifications/send-email
POST /api/notifications/send-otp
GET  /api/notifications/dashboard-stats
```

Dokumentasi endpoint lengkap tersedia pada:

```text
docs/api-endpoints.md
```

## GraphQL Gateway Manual

GraphQL Gateway manual berjalan pada:

```text
http://localhost:8010
```

Playground:

```text
http://localhost:8010/playground
```

Endpoint GraphQL:

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

Catatan penting:

```text
Endpoint /api/graphql hanya menerima request POST.
Jika dibuka langsung melalui browser menggunakan GET, Laravel akan menampilkan MethodNotAllowed.
Untuk testing melalui browser, gunakan /playground.
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

Dokumentasi testing GraphQL Gateway tersedia pada:

```text
docs/graphql-testing.md
```

## Hasura GraphQL Reporting

Hasura Console berjalan pada:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

Hasura menggunakan database:

```text
hasura_db
```

SQL reporting berada pada:

```text
hasura/local/schema/reporting-schema.sql
```

Table reporting:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

View reporting:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

Contoh query dashboard summary:

```graphql
query DashboardSummary {
  v_dashboard_summary {
    fields_total
    members_total
    bookings_total
    notifications_total
    approved_revenue_total
  }
}
```

Dokumentasi Hasura tersedia pada:

```text
docs/hasura-testing.md
hasura/local/README.md
hasura/local/setup-guide.md
hasura/local/queries/query-examples.md
```

## Flow Utama Sistem

```text
1. Admin login melalui Web Client.
2. Admin mengelola data lapangan.
3. Admin mengelola data member.
4. Member melakukan register.
5. Auth Service membuat OTP.
6. Notification Service mengirim OTP email menggunakan template HTML.
7. Member melakukan verifikasi OTP.
8. Member login ke Web Client.
9. Member melihat daftar lapangan available.
10. Member membuat booking.
11. Booking Service melakukan validasi member, lapangan, waktu, dan konflik jadwal.
12. Booking berhasil dibuat dengan status pending.
13. Admin melihat booking request.
14. Admin approve atau reject booking.
15. Notification Service mengirim email status booking.
16. Member melihat status booking terbaru.
17. Member dapat cancel booking sesuai aturan.
18. Dashboard member menampilkan rekomendasi pribadi dan lapangan terpopuler.
19. GraphQL Gateway menyediakan akses GraphQL manual.
20. Hasura menyediakan query reporting.
```

## Status Booking

| Status   | Keterangan                                                     |
| -------- | -------------------------------------------------------------- |
| pending  | Booking baru diajukan oleh member dan menunggu keputusan admin |
| approved | Booking disetujui admin                                        |
| rejected | Booking ditolak admin dengan alasan                            |
| canceled | Booking dibatalkan member                                      |

## Status Member

| Status   | Keterangan                                            |
| -------- | ----------------------------------------------------- |
| active   | Member aktif dan dapat melakukan booking              |
| inactive | Member belum aktif atau belum dapat melakukan booking |
| blocked  | Member diblokir dan tidak dapat melakukan booking     |

## Status Lapangan

| Status      | Keterangan                      |
| ----------- | ------------------------------- |
| available   | Lapangan tersedia untuk booking |
| maintenance | Lapangan sedang maintenance     |
| inactive    | Lapangan tidak aktif            |

## Rekomendasi Lapangan

Dashboard member memiliki dua bagian rekomendasi.

### Rekomendasi Pribadi

Rekomendasi pribadi dibuat berdasarkan riwayat booking member yang sedang login.

Dasar rekomendasi:

```text
1. Lapangan yang pernah dibooking member.
2. Tipe lapangan yang sering dibooking member.
```

Badge yang ditampilkan:

```text
Pernah kamu booking
Sesuai tipe favorit kamu
```

### Lapangan Terpopuler

Lapangan terpopuler dibuat berdasarkan jumlah booking `approved` terbanyak dari semua member.

Endpoint yang digunakan:

```text
GET /api/bookings/popular-fields
```

Hanya booking dengan status `approved` yang dihitung agar rekomendasi berdasarkan booking yang benar-benar disetujui.

## Email Template

Notification Service menggunakan template HTML untuk:

```text
1. OTP registrasi member.
2. Email manual admin ke member.
3. Notifikasi status booking.
```

Lokasi template email:

```text
notification-service/resources/views/emails/
```

File template:

```text
otp.blade.php
notification.blade.php
```

Service pembuat template:

```text
notification-service/app/Services/EmailTemplateService.php
```

## Seeder Demo

Seeder tersedia pada service berikut:

```text
auth-service
member-service
field-service
booking-service
notification-service
```

Seeder dibuat saling terhubung menggunakan ID tetap agar data antar service tidak terputus.

Contoh keterhubungan data:

```text
Auth Service user member -> Member Service profile
Member Service member    -> Booking Service booking
Field Service field      -> Booking Service booking
Notification Service log -> Email dan aktivitas booking/member
```

Untuk reset data demo Docker Mode:

```powershell
.\scripts\migrate-docker.ps1
```

Untuk reset data demo Local/XAMPP Mode:

```powershell
.\scripts\migrate-local.ps1
```

## Dokumentasi Project

| Dokumen                                    | Keterangan                                         |
| ------------------------------------------ | -------------------------------------------------- |
| `docs/architecture.md`                     | Dokumentasi arsitektur sistem                      |
| `docs/deployment-guide.md`                 | Panduan menjalankan project                        |
| `docs/api-endpoints.md`                    | Daftar endpoint Web, REST API, GraphQL, dan Hasura |
| `docs/final-testing.md`                    | Skenario testing final                             |
| `docs/graphql-testing.md`                  | Panduan testing GraphQL Gateway                    |
| `docs/hasura-testing.md`                   | Panduan testing Hasura                             |
| `docs/requirement-checklist.md`            | Checklist requirement final project                |
| `docs/script-guide.md`                     | Panduan script PowerShell project                  |
| `docs/team-responsibilities.md`            | Pembagian tanggung jawab anggota tim               |
| `hasura/local/README.md`                   | Dokumentasi Hasura Local                           |
| `hasura/local/setup-guide.md`              | Panduan setup Hasura Local                         |
| `hasura/local/queries/*.md`                | Contoh query Hasura                                |
| `hasura/local/schema/reporting-schema.sql` | SQL schema reporting Hasura                        |

## Testing Cepat

Setelah project berjalan, lakukan testing berikut:

```text
1. Login admin melalui /login/master.
2. Cek dashboard admin.
3. Cek data member.
4. Cek data lapangan.
5. Login member active.
6. Cek dashboard member.
7. Cek rekomendasi pribadi.
8. Cek lapangan terpopuler.
9. Buat booking baru.
10. Approve atau reject booking dari admin.
11. Cek log notifikasi.
12. Kirim email manual admin.
13. Test GraphQL Gateway dari /playground.
14. Test Hasura dari http://localhost:8080.
```

Panduan testing lengkap tersedia pada:

```text
docs/final-testing.md
```

## Troubleshooting Singkat

### Docker belum berjalan

Buka Docker Desktop, tunggu sampai Docker ready, lalu jalankan ulang script.

### Port sudah digunakan

Port yang digunakan project:

```text
8001 auth-service
8002 member-service
8003 field-service
8004 booking-service
8005 notification-service
8090 web-client
8010 graphql-gateway
8080 hasura
6379 redis
```

Hentikan service menggunakan:

```powershell
.\scripts\stop-docker.ps1
```

atau:

```powershell
.\scripts\stop-local.ps1
```

### GraphQL API menampilkan MethodNotAllowed

Endpoint `/api/graphql` hanya menerima POST. Gunakan playground:

```text
http://localhost:8010/playground
```

### Data Hasura tidak sama dengan data Web Client

Hasura menggunakan database reporting terpisah `hasura_db`. Data Hasura berasal dari:

```text
hasura/local/schema/reporting-schema.sql
```

Jika ingin memperbarui data reporting, jalankan ulang SQL tersebut di Hasura Console.

## Status Project

Project sudah mencakup:

```text
✅ Microservices
✅ RESTful API
✅ Docker Compose
✅ Database terpisah per service
✅ Redis message broker
✅ Notification worker
✅ Web Client admin dan member
✅ OTP email
✅ Email template HTML
✅ Booking flow
✅ Admin approval/reject
✅ Member cancel booking
✅ Notification log
✅ Dashboard admin
✅ Dashboard member
✅ Rekomendasi pribadi
✅ Lapangan terpopuler
✅ GraphQL Gateway manual berbasis Laravel
✅ Hasura GraphQL reporting
✅ Seeder demo saling terhubung
✅ Script Docker Mode dan Local/XAMPP Mode
✅ Dokumentasi final
✅ Pembagian tanggung jawab tim
```

## Kesimpulan

Retobluto Arena Microservices adalah sistem booking lapangan olahraga yang menerapkan microservices dengan pemisahan service dan database berdasarkan domain. Sistem ini menyediakan Web Client, RESTful API, Redis message broker, notification worker, GraphQL Gateway manual, Hasura reporting, serta dokumentasi final untuk deployment dan testing.

Project ini dapat dijalankan melalui Docker Mode untuk demo final atau Local/XAMPP Mode untuk pengembangan lokal.
