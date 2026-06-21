# API Endpoints Documentation

Dokumen ini berisi daftar endpoint utama pada project Retobluto Arena Microservices berdasarkan route yang tersedia pada masing-masing service.

## Catatan Umum

Project ini memiliki beberapa jenis endpoint:

```text
1. Web route pada web-client
2. REST API pada setiap microservice
3. GraphQL endpoint pada graphql-gateway
4. Hasura GraphQL endpoint untuk reporting
```

Beberapa endpoint membutuhkan token autentikasi dengan format:

```text
Authorization: Bearer <access_token>
```

Base URL Docker Mode:

| Service              | Base URL                    |
| -------------------- | --------------------------- |
| Web Client           | `http://localhost:8090`     |
| Auth Service         | `http://localhost:8001/api` |
| Member Service       | `http://localhost:8002/api` |
| Field Service        | `http://localhost:8003/api` |
| Booking Service      | `http://localhost:8004/api` |
| Notification Service | `http://localhost:8005/api` |
| GraphQL Gateway      | `http://localhost:8010`     |
| Hasura               | `http://localhost:8080`     |

Base URL Local/XAMPP Mode:

| Service              | Base URL                    |
| -------------------- | --------------------------- |
| Web Client           | `http://127.0.0.1:8090`     |
| Auth Service         | `http://127.0.0.1:8001/api` |
| Member Service       | `http://127.0.0.1:8002/api` |
| Field Service        | `http://127.0.0.1:8003/api` |
| Booking Service      | `http://127.0.0.1:8004/api` |
| Notification Service | `http://127.0.0.1:8005/api` |
| GraphQL Gateway      | `http://127.0.0.1:8010`     |
| Hasura               | `http://localhost:8080`     |

---

# 1. Web Client Routes

Base URL Docker:

```text
http://localhost:8090
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8090
```

## Public Routes

| Method | Endpoint                | Keterangan                        |
| ------ | ----------------------- | --------------------------------- |
| GET    | `/`                     | Redirect berdasarkan session user |
| GET    | `/dashboard`            | Redirect ke dashboard sesuai role |
| GET    | `/login`                | Halaman login member              |
| POST   | `/login`                | Proses login member               |
| GET    | `/register`             | Halaman register member           |
| POST   | `/register/request-otp` | Request OTP registrasi member     |
| GET    | `/verify-otp`           | Halaman verifikasi OTP            |
| POST   | `/verify-otp`           | Proses verifikasi OTP             |
| POST   | `/verify-otp/resend`    | Kirim ulang OTP                   |
| GET    | `/login/master`         | Halaman login admin               |
| POST   | `/login/master`         | Proses login admin                |
| POST   | `/logout`               | Logout user                       |

## Admin Routes

Prefix:

```text
/admin
```

| Method | Endpoint                          | Keterangan                     |
| ------ | --------------------------------- | ------------------------------ |
| GET    | `/admin/dashboard`                | Dashboard admin                |
| GET    | `/admin/fields`                   | Daftar lapangan                |
| GET    | `/admin/fields/create`            | Form tambah lapangan           |
| POST   | `/admin/fields`                   | Simpan lapangan baru           |
| GET    | `/admin/fields/{id}`              | Detail lapangan                |
| GET    | `/admin/fields/{id}/edit`         | Form edit lapangan             |
| PUT    | `/admin/fields/{id}`              | Update lapangan                |
| PATCH  | `/admin/fields/{id}/status`       | Update status lapangan         |
| DELETE | `/admin/fields/{id}`              | Hapus lapangan                 |
| GET    | `/admin/members`                  | Daftar member                  |
| GET    | `/admin/members/create`           | Form tambah member             |
| POST   | `/admin/members`                  | Simpan member baru             |
| GET    | `/admin/members/{id}`             | Detail member                  |
| GET    | `/admin/members/{id}/edit`        | Form edit member               |
| PUT    | `/admin/members/{id}`             | Update member                  |
| PATCH  | `/admin/members/{id}/status`      | Update status member           |
| DELETE | `/admin/members/{id}`             | Hapus member                   |
| GET    | `/admin/booking-requests`         | Daftar booking request pending |
| GET    | `/admin/bookings`                 | Daftar semua booking           |
| GET    | `/admin/bookings/{id}`            | Detail booking                 |
| POST   | `/admin/bookings/{id}/approve`    | Approve booking                |
| POST   | `/admin/bookings/{id}/reject`     | Reject booking                 |
| GET    | `/admin/notifications/send-email` | Form kirim email manual admin  |
| POST   | `/admin/notifications/send-email` | Kirim email manual admin       |
| GET    | `/admin/notifications`            | Daftar log notifikasi          |
| GET    | `/admin/notifications/{id}`       | Detail log notifikasi          |

## Member Routes

Prefix:

```text
/member
```

| Method | Endpoint                       | Keterangan                   |
| ------ | ------------------------------ | ---------------------------- |
| GET    | `/member/home`                 | Dashboard member             |
| GET    | `/member/fields`               | Daftar lapangan untuk member |
| GET    | `/member/fields/{id}`          | Detail lapangan              |
| GET    | `/member/bookings`             | Daftar booking milik member  |
| GET    | `/member/bookings/create`      | Form buat booking            |
| POST   | `/member/bookings`             | Simpan booking baru          |
| GET    | `/member/bookings/{id}`        | Detail booking member        |
| POST   | `/member/bookings/{id}/cancel` | Cancel booking member        |
| GET    | `/member/profile`              | Detail profil member         |
| GET    | `/member/profile/edit`         | Form edit profil member      |
| PUT    | `/member/profile`              | Update profil member         |

---

# 2. Auth Service API

Base URL Docker:

```text
http://localhost:8001/api
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8001/api
```

## Public Endpoint

| Method | Endpoint                       | Keterangan                    |
| ------ | ------------------------------ | ----------------------------- |
| GET    | `/health`                      | Health check Auth Service     |
| POST   | `/admin/login`                 | Login admin                   |
| POST   | `/member/login`                | Login member                  |
| POST   | `/member/register/request-otp` | Request OTP registrasi member |
| POST   | `/member/register/verify`      | Verifikasi OTP member         |
| POST   | `/member/register/resend-otp`  | Kirim ulang OTP registrasi    |
| POST   | `/validate-token`              | Validasi token                |

## Protected Endpoint

Endpoint berikut membutuhkan token.

| Method | Endpoint                      | Keterangan                       |
| ------ | ----------------------------- | -------------------------------- |
| GET    | `/me`                         | Mengambil data user login        |
| POST   | `/refresh`                    | Refresh token                    |
| POST   | `/logout`                     | Logout user                      |
| POST   | `/admin/members`              | Admin membuat akun member        |
| DELETE | `/admin/members/auth-account` | Admin menghapus akun auth member |

## Fungsi Auth Service

Auth Service bertanggung jawab untuk:

```text
1. Login admin.
2. Login member.
3. Register member dengan OTP.
4. Resend OTP.
5. Verifikasi OTP.
6. Validasi token antar service.
7. Membuat akun member dari admin.
8. Menghapus akun auth member ketika member dihapus.
```

---

# 3. Member Service API

Base URL Docker:

```text
http://localhost:8002/api
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8002/api
```

| Method | Endpoint                           | Keterangan                                 |
| ------ | ---------------------------------- | ------------------------------------------ |
| GET    | `/health`                          | Health check Member Service                |
| GET    | `/profile`                         | Mengambil profil member login              |
| PUT    | `/profile`                         | Update profil member login                 |
| GET    | `/members/dashboard-stats`         | Statistik dashboard member untuk admin     |
| GET    | `/members`                         | Daftar member                              |
| POST   | `/members`                         | Tambah member                              |
| GET    | `/members/user/{userId}`           | Mengambil member berdasarkan user_id       |
| PATCH  | `/members/{id}/status`             | Update status member                       |
| GET    | `/members/{id}`                    | Detail member                              |
| PUT    | `/members/{id}`                    | Update member                              |
| DELETE | `/members/{id}`                    | Hapus member                               |
| POST   | `/internal/members/sync-from-auth` | Sinkronisasi data member dari Auth Service |
| GET    | `/internal/members/{id}`           | Internal detail member                     |
| GET    | `/internal/members/user/{userId}`  | Internal member berdasarkan user_id        |

## Fungsi Member Service

Member Service bertanggung jawab untuk:

```text
1. Menyimpan data profil member.
2. Mengelola status member active, inactive, dan blocked.
3. Menyediakan data member untuk admin.
4. Menyediakan profil member untuk member login.
5. Menyediakan endpoint internal untuk sinkronisasi dari Auth Service.
```

---

# 4. Field Service API

Base URL Docker:

```text
http://localhost:8003/api
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8003/api
```

| Method | Endpoint                        | Keterangan                              |
| ------ | ------------------------------- | --------------------------------------- |
| GET    | `/health`                       | Health check Field Service              |
| GET    | `/fields/available`             | Daftar lapangan dengan status available |
| GET    | `/fields/dashboard-stats`       | Statistik dashboard lapangan            |
| GET    | `/fields`                       | Daftar semua lapangan                   |
| POST   | `/fields`                       | Tambah lapangan                         |
| GET    | `/fields/{id}/detail`           | Detail lapangan lengkap                 |
| GET    | `/fields/{id}/booking-schedule` | Jadwal booking berdasarkan lapangan     |
| PATCH  | `/fields/{id}/status`           | Update status lapangan                  |
| GET    | `/fields/{id}`                  | Detail lapangan                         |
| PUT    | `/fields/{id}`                  | Update lapangan                         |
| DELETE | `/fields/{id}`                  | Hapus lapangan                          |

## Fungsi Field Service

Field Service bertanggung jawab untuk:

```text
1. Menyimpan data lapangan.
2. Menyediakan daftar lapangan available untuk member.
3. Menyediakan detail lapangan.
4. Mengelola status lapangan available, maintenance, dan inactive.
5. Menyediakan jadwal booking lapangan.
6. Menyediakan statistik lapangan untuk dashboard.
```

---

# 5. Booking Service API

Base URL Docker:

```text
http://localhost:8004/api
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8004/api
```

| Method | Endpoint                             | Keterangan                                                 |
| ------ | ------------------------------------ | ---------------------------------------------------------- |
| GET    | `/health`                            | Health check Booking Service                               |
| POST   | `/bookings`                          | Membuat booking baru                                       |
| GET    | `/bookings/dashboard-stats`          | Statistik dashboard booking                                |
| GET    | `/bookings`                          | Daftar semua booking                                       |
| GET    | `/member/bookings`                   | Daftar booking milik member login                          |
| GET    | `/member/bookings/history`           | Riwayat booking member                                     |
| POST   | `/member/bookings/{id}/cancel`       | Cancel booking member                                      |
| GET    | `/admin/booking-requests`            | Daftar booking request pending untuk admin                 |
| POST   | `/admin/bookings/{id}/approve`       | Approve booking                                            |
| POST   | `/admin/bookings/{id}/reject`        | Reject booking                                             |
| GET    | `/bookings/field/{fieldId}/schedule` | Jadwal booking berdasarkan field_id                        |
| GET    | `/bookings/member/{memberId}`        | Daftar booking berdasarkan member_id                       |
| GET    | `/bookings/popular-fields`           | Statistik lapangan terpopuler berdasarkan booking approved |
| GET    | `/bookings/{id}`                     | Detail booking                                             |

## Fungsi Booking Service

Booking Service bertanggung jawab untuk:

```text
1. Membuat booking lapangan.
2. Validasi member active sebelum booking.
3. Validasi status lapangan.
4. Validasi jam booking sesuai open_time dan close_time lapangan.
5. Validasi konflik jadwal booking.
6. Mencegah member memiliki lebih dari satu booking aktif pada tanggal yang sama.
7. Menyediakan booking request untuk admin.
8. Approve booking.
9. Reject booking dengan alasan.
10. Cancel booking oleh member.
11. Menyediakan statistik booking.
12. Menyediakan data lapangan populer berdasarkan booking approved terbanyak.
```

## Endpoint Lapangan Terpopuler

Endpoint:

```text
GET /api/bookings/popular-fields
```

Endpoint ini digunakan oleh dashboard member untuk menampilkan bagian:

```text
Lapangan Terpopuler
```

Dasar perhitungan:

```text
Jumlah booking dengan status approved pada setiap lapangan.
```

Contoh query parameter:

```text
/api/bookings/popular-fields?limit=10
```

Response berisi data agregasi:

```json
[
  {
    "field_id": 1,
    "field_name": "Lapangan Futsal A",
    "field_type": "Futsal",
    "booking_count": 3
  }
]
```

---

# 6. Notification Service API

Base URL Docker:

```text
http://localhost:8005/api
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8005/api
```

| Method | Endpoint                                 | Keterangan                        |
| ------ | ---------------------------------------- | --------------------------------- |
| GET    | `/health`                                | Health check Notification Service |
| GET    | `/notifications/dashboard-stats`         | Statistik dashboard notification  |
| GET    | `/notifications/logs`                    | Daftar log notifikasi             |
| GET    | `/notifications/logs/{id}`               | Detail log notifikasi             |
| POST   | `/notifications/send-email`              | Mengirim email manual             |
| POST   | `/notifications/send-otp`                | Mengirim OTP manual/API           |
| POST   | `/internal/notifications/otp`            | Internal event OTP                |
| POST   | `/internal/notifications/booking-status` | Internal event status booking     |

## Fungsi Notification Service

Notification Service bertanggung jawab untuk:

```text
1. Mengirim email OTP.
2. Mengirim email manual dari admin ke member.
3. Mengirim email status booking.
4. Mencatat log notifikasi.
5. Menyediakan detail log notifikasi.
6. Menyediakan statistik notifikasi.
```

## Template Email

Notification Service menggunakan template HTML untuk email.

Lokasi template:

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

Jenis email yang menggunakan template:

```text
1. Email OTP registrasi member.
2. Email manual admin ke member.
3. Email notifikasi status booking.
```

---

# 7. GraphQL Gateway API

Base URL Docker:

```text
http://localhost:8010
```

Base URL Local/XAMPP:

```text
http://127.0.0.1:8010
```

| Method | Endpoint              | Keterangan                          |
| ------ | --------------------- | ----------------------------------- |
| GET    | `/api/health`         | Health check GraphQL Gateway        |
| POST   | `/api/graphql`        | Endpoint utama GraphQL              |
| GET    | `/api/graphql/schema` | Melihat schema GraphQL              |
| GET    | `/playground`         | GraphQL Playground berbasis browser |

## Catatan GraphQL Endpoint

Endpoint berikut hanya menerima method POST:

```text
/api/graphql
```

Jika endpoint tersebut dibuka langsung melalui browser, browser akan mengirim GET dan Laravel akan menampilkan pesan:

```text
The GET method is not supported for route api/graphql. Supported methods: POST.
```

Hal tersebut normal. Untuk testing melalui browser, gunakan:

```text
/playground
```

## Fungsi GraphQL Gateway

GraphQL Gateway manual bertanggung jawab untuk:

```text
1. Menyediakan GraphQL API manual berbasis Laravel.
2. Meneruskan query dan mutation ke REST API service.
3. Menggabungkan akses data dari beberapa service.
4. Menyediakan playground untuk testing GraphQL.
```

## Contoh Query Health

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

---

# 8. Hasura GraphQL

Base URL:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

## Fungsi Hasura

Hasura digunakan sebagai GraphQL Engine otomatis untuk kebutuhan reporting.

Hasura tidak digunakan untuk proses transaksi utama seperti:

```text
login
register
OTP
booking
approve booking
reject booking
cancel booking
```

Proses tersebut tetap dilakukan melalui Laravel microservices.

## Database Hasura

Hasura menggunakan database:

```text
hasura_db
```

Engine:

```text
PostgreSQL
```

Schema reporting disimpan pada:

```text
hasura/local/schema/reporting-schema.sql
```

## Table Reporting

```text
report_fields
report_members
report_bookings
report_notification_logs
```

## View Reporting

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

## Contoh Query Hasura

```graphql
query {
  v_dashboard_summary {
    fields_total
    members_total
    bookings_total
    notifications_total
    approved_revenue_total
  }
}
```

---

# 9. Internal Endpoint

Beberapa endpoint memiliki prefix `/internal`. Endpoint ini digunakan untuk komunikasi antar service, bukan untuk digunakan langsung oleh user dari UI.

Endpoint internal yang tersedia:

| Service              | Method | Endpoint                                 | Keterangan                            |
| -------------------- | ------ | ---------------------------------------- | ------------------------------------- |
| Member Service       | POST   | `/internal/members/sync-from-auth`       | Sinkron data member dari Auth Service |
| Member Service       | GET    | `/internal/members/{id}`                 | Internal detail member                |
| Member Service       | GET    | `/internal/members/user/{userId}`        | Internal member berdasarkan user_id   |
| Notification Service | POST   | `/internal/notifications/otp`            | Internal event OTP                    |
| Notification Service | POST   | `/internal/notifications/booking-status` | Internal event status booking         |

## Catatan Internal Endpoint

Endpoint internal digunakan agar service dapat saling berkomunikasi tanpa mengakses database service lain secara langsung.

Contoh:

```text
Auth Service membuat user member
Auth Service memanggil Member Service untuk sinkron profile member
Auth Service mengirim event OTP ke Notification Service
Booking Service mengirim event status booking ke Notification Service
```

---

# 10. Ringkasan Endpoint Penting Untuk Demo

Endpoint yang biasanya dipakai saat demo:

| Kebutuhan           | Endpoint                                   |
| ------------------- | ------------------------------------------ |
| Web utama           | `http://localhost:8090`                    |
| Login admin         | `http://localhost:8090/login/master`       |
| Login member        | `http://localhost:8090/login`              |
| Register member     | `http://localhost:8090/register`           |
| GraphQL Playground  | `http://localhost:8010/playground`         |
| GraphQL API         | `http://localhost:8010/api/graphql`        |
| GraphQL Schema      | `http://localhost:8010/api/graphql/schema` |
| Hasura Console      | `http://localhost:8080`                    |
| Auth health         | `http://localhost:8001/api/health`         |
| Member health       | `http://localhost:8002/api/health`         |
| Field health        | `http://localhost:8003/api/health`         |
| Booking health      | `http://localhost:8004/api/health`         |
| Notification health | `http://localhost:8005/api/health`         |
