# API Endpoints Documentation

Dokumen ini berisi daftar endpoint utama pada Retobluto Arena Microservices.

## Web Client

Base URL:

```text
http://localhost:8090
```

### Public Route

| Method | Endpoint              | Keterangan                        |
| ------ | --------------------- | --------------------------------- |
| GET    | /                     | Redirect berdasarkan session      |
| GET    | /dashboard            | Redirect ke dashboard sesuai role |
| GET    | /login                | Halaman login member              |
| POST   | /login                | Proses login member               |
| GET    | /register             | Halaman register member           |
| POST   | /register/request-otp | Request OTP register member       |
| GET    | /verify-otp           | Halaman verifikasi OTP            |
| POST   | /verify-otp           | Proses verifikasi OTP             |
| POST   | /verify-otp/resend    | Kirim ulang OTP                   |
| GET    | /login/master         | Halaman login admin               |
| POST   | /login/master         | Proses login admin                |
| POST   | /logout               | Logout                            |

### Admin Route

| Method | Endpoint                     | Keterangan             |
| ------ | ---------------------------- | ---------------------- |
| GET    | /admin/dashboard             | Dashboard admin        |
| GET    | /admin/fields                | List lapangan          |
| GET    | /admin/fields/create         | Form tambah lapangan   |
| POST   | /admin/fields                | Simpan lapangan        |
| GET    | /admin/fields/{id}           | Detail lapangan        |
| GET    | /admin/fields/{id}/edit      | Form edit lapangan     |
| PUT    | /admin/fields/{id}           | Update lapangan        |
| PATCH  | /admin/fields/{id}/status    | Update status lapangan |
| DELETE | /admin/fields/{id}           | Hapus lapangan         |
| GET    | /admin/members               | List member            |
| GET    | /admin/members/create        | Form tambah member     |
| POST   | /admin/members               | Simpan member          |
| GET    | /admin/members/{id}          | Detail member          |
| GET    | /admin/members/{id}/edit     | Form edit member       |
| PUT    | /admin/members/{id}          | Update member          |
| PATCH  | /admin/members/{id}/status   | Update status member   |
| DELETE | /admin/members/{id}          | Hapus member           |
| GET    | /admin/booking-requests      | List request booking   |
| GET    | /admin/bookings              | List semua booking     |
| GET    | /admin/bookings/{id}         | Detail booking         |
| POST   | /admin/bookings/{id}/approve | Approve booking        |
| POST   | /admin/bookings/{id}/reject  | Reject booking         |
| GET    | /admin/notifications         | List log notifikasi    |
| GET    | /admin/notifications/{id}    | Detail log notifikasi  |

### Member Route

| Method | Endpoint                     | Keterangan                 |
| ------ | ---------------------------- | -------------------------- |
| GET    | /member/home                 | Dashboard member           |
| GET    | /member/fields               | List lapangan untuk member |
| GET    | /member/fields/{id}          | Detail lapangan            |
| GET    | /member/bookings             | List booking member        |
| GET    | /member/bookings/create      | Form booking               |
| POST   | /member/bookings             | Simpan booking             |
| GET    | /member/bookings/{id}        | Detail booking             |
| POST   | /member/bookings/{id}/cancel | Cancel booking             |
| GET    | /member/profile              | Detail profil              |
| GET    | /member/profile/edit         | Form edit profil           |
| PUT    | /member/profile              | Update profil              |

## Auth Service

Base URL:

```text
http://localhost:8001/api
```

| Method | Endpoint                     | Keterangan                   |
| ------ | ---------------------------- | ---------------------------- |
| GET    | /health                      | Health check                 |
| POST   | /admin/login                 | Login admin                  |
| POST   | /member/login                | Login member                 |
| POST   | /member/register/request-otp | Request OTP register member  |
| POST   | /member/register/verify      | Verifikasi OTP member        |
| POST   | /member/register/resend-otp  | Kirim ulang OTP              |
| POST   | /validate-token              | Validasi token               |
| GET    | /me                          | Detail user login            |
| POST   | /refresh                     | Refresh token                |
| POST   | /logout                      | Logout                       |
| POST   | /admin/members               | Admin membuat member         |
| DELETE | /admin/members/auth-account  | Admin hapus akun auth member |

## Member Service

Base URL:

```text
http://localhost:8002/api
```

| Method | Endpoint                         | Keterangan                          |
| ------ | -------------------------------- | ----------------------------------- |
| GET    | /health                          | Health check                        |
| GET    | /profile                         | Profil member login                 |
| PUT    | /profile                         | Update profil member                |
| GET    | /members                         | List member                         |
| POST   | /members                         | Tambah member                       |
| GET    | /members/user/{userId}           | Ambil member berdasarkan user_id    |
| PATCH  | /members/{id}/status             | Update status member                |
| GET    | /members/{id}                    | Detail member                       |
| PUT    | /members/{id}                    | Update member                       |
| DELETE | /members/{id}                    | Hapus member                        |
| POST   | /internal/members/sync-from-auth | Sinkron member dari Auth Service    |
| GET    | /internal/members/{id}           | Internal detail member              |
| GET    | /internal/members/user/{userId}  | Internal member berdasarkan user_id |

## Field Service

Base URL:

```text
http://localhost:8003/api
```

| Method | Endpoint                      | Keterangan              |
| ------ | ----------------------------- | ----------------------- |
| GET    | /health                       | Health check            |
| GET    | /fields/available             | List lapangan available |
| GET    | /fields                       | List lapangan           |
| POST   | /fields                       | Tambah lapangan         |
| GET    | /fields/{id}/detail           | Detail lapangan lengkap |
| GET    | /fields/{id}/booking-schedule | Jadwal booking lapangan |
| PATCH  | /fields/{id}/status           | Update status lapangan  |
| GET    | /fields/{id}                  | Detail lapangan         |
| PUT    | /fields/{id}                  | Update lapangan         |
| DELETE | /fields/{id}                  | Hapus lapangan          |

## Booking Service

Base URL:

```text
http://localhost:8004/api
```

| Method | Endpoint                           | Keterangan                          |
| ------ | ---------------------------------- | ----------------------------------- |
| GET    | /health                            | Health check                        |
| POST   | /bookings                          | Membuat booking                     |
| GET    | /bookings                          | List booking                        |
| GET    | /member/bookings                   | Booking milik member login          |
| GET    | /member/bookings/history           | Riwayat booking member              |
| POST   | /member/bookings/{id}/cancel       | Cancel booking member               |
| GET    | /admin/booking-requests            | Request booking pending untuk admin |
| POST   | /admin/bookings/{id}/approve       | Approve booking                     |
| POST   | /admin/bookings/{id}/reject        | Reject booking                      |
| GET    | /bookings/field/{fieldId}/schedule | Jadwal booking berdasarkan lapangan |
| GET    | /bookings/member/{memberId}        | Booking berdasarkan member          |
| GET    | /bookings/{id}                     | Detail booking                      |

## Notification Service

Base URL:

```text
http://localhost:8005/api
```

| Method | Endpoint                               | Keterangan                    |
| ------ | -------------------------------------- | ----------------------------- |
| GET    | /health                                | Health check                  |
| GET    | /notifications/logs                    | List log notifikasi           |
| GET    | /notifications/logs/{id}               | Detail log notifikasi         |
| POST   | /notifications/send-email              | Kirim email manual            |
| POST   | /notifications/send-otp                | Kirim OTP manual/API          |
| POST   | /internal/notifications/otp            | Internal event OTP            |
| POST   | /internal/notifications/booking-status | Internal event status booking |

## GraphQL Gateway Manual

Base URL:

```text
http://localhost:8010
```

| Method | Endpoint            | Keterangan             |
| ------ | ------------------- | ---------------------- |
| GET    | /playground         | GraphQL playground     |
| GET    | /api/health         | Health check gateway   |
| POST   | /api/graphql        | Endpoint GraphQL       |
| GET    | /api/graphql/schema | Melihat schema GraphQL |

## Hasura

Base URL:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

Hasura digunakan untuk query reporting dari PostgreSQL `hasura_db`.

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
