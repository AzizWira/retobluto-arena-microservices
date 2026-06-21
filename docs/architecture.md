# Architecture Documentation

## Nama Project

Retobluto Arena Microservices

## Deskripsi Singkat

Retobluto Arena Microservices adalah sistem booking lapangan olahraga berbasis microservices. Sistem ini menyediakan fitur autentikasi admin dan member, registrasi member menggunakan OTP email, pengelolaan data lapangan, pengelolaan member, proses booking lapangan, approval dan reject booking oleh admin, cancel booking oleh member, notifikasi email, RESTful API, Laravel GraphQL Gateway manual, serta Hasura GraphQL untuk kebutuhan reporting.

Sistem ini memiliki web client sebagai antarmuka utama untuk admin dan member. Admin dapat mengelola lapangan, member, booking request, dan log notifikasi. Member dapat melakukan registrasi, verifikasi OTP, login, melihat lapangan, membuat booking, melihat status booking, membatalkan booking, dan melihat rekomendasi lapangan.

## Tujuan Arsitektur

Project ini dirancang menggunakan pendekatan microservices agar setiap domain utama memiliki service, tanggung jawab, dan database masing-masing. Dengan pemisahan ini, setiap service dapat dikembangkan, diuji, dan dijalankan secara lebih terpisah.

Tujuan utama arsitektur ini adalah:

```text
1. Memisahkan tanggung jawab berdasarkan domain sistem.
2. Menghindari satu aplikasi besar yang menangani seluruh proses.
3. Menyediakan RESTful API pada setiap service.
4. Menyediakan GraphQL Gateway manual berbasis Laravel.
5. Menyediakan Hasura GraphQL untuk reporting.
6. Menggunakan Redis sebagai message broker untuk proses notifikasi.
7. Menjalankan sistem secara konsisten menggunakan Docker Compose.
```

## Daftar Service

| Service              | Port Docker | Fungsi Utama                                                               |
| -------------------- | ----------: | -------------------------------------------------------------------------- |
| auth-service         |        8001 | Autentikasi admin/member, register member, OTP, validasi token             |
| member-service       |        8002 | Manajemen data member dan profil member                                    |
| field-service        |        8003 | Manajemen data lapangan                                                    |
| booking-service      |        8004 | Booking lapangan, approval, reject, cancel, dan statistik booking          |
| notification-service |        8005 | Pengiriman OTP, email manual admin, notifikasi booking, dan log notifikasi |
| notification-worker  |           - | Worker Redis untuk memproses event notifikasi                              |
| web-client           |        8090 | UI utama untuk admin dan member                                            |
| graphql-gateway      |        8010 | GraphQL Gateway manual berbasis Laravel                                    |
| hasura               |        8080 | Hasura GraphQL Engine untuk reporting                                      |
| redis                |    internal | Message broker untuk OTP dan notifikasi                                    |
| hasura-db            |        5433 | PostgreSQL untuk database reporting Hasura                                 |

## Database Per Service

| Service              | Database        | Engine     | Keterangan                                                           |
| -------------------- | --------------- | ---------- | -------------------------------------------------------------------- |
| auth-service         | auth_db         | MySQL      | Menyimpan user admin/member, credential, role, dan status verifikasi |
| member-service       | member_db       | MySQL      | Menyimpan profil dan status member                                   |
| field-service        | field_db        | MySQL      | Menyimpan data lapangan                                              |
| booking-service      | booking_db      | MySQL      | Menyimpan data booking dan status booking                            |
| notification-service | notification_db | MySQL      | Menyimpan log notifikasi                                             |
| hasura               | hasura_db       | PostgreSQL | Menyimpan data reporting untuk Hasura                                |

## Alasan Database Dipisah

Setiap service menggunakan database masing-masing untuk mengikuti prinsip database per service pada arsitektur microservices. Dengan begitu, Auth Service tidak langsung mengakses database Booking Service, Booking Service tidak langsung mengakses database Member Service, dan seterusnya.

Jika suatu service membutuhkan data dari service lain, komunikasi dilakukan melalui API, bukan dengan query langsung ke database service lain.

## Alasan Database Hasura Terpisah

Hasura menggunakan database PostgreSQL terpisah bernama `hasura_db`. Database ini digunakan untuk kebutuhan reporting/read-only query.

Hasura tidak digunakan untuk menggantikan proses transaksi utama seperti login, OTP, booking, approve, reject, atau cancel. Proses utama tetap berjalan melalui Laravel microservices. Hasura hanya digunakan sebagai GraphQL Engine untuk membaca data reporting yang disiapkan pada `hasura_db`.

## Komunikasi Antar Service

Komunikasi antar service dilakukan menggunakan REST API.

Alur komunikasi utama:

```text
web-client -> auth-service
web-client -> member-service
web-client -> field-service
web-client -> booking-service
web-client -> notification-service

graphql-gateway -> auth-service
graphql-gateway -> member-service
graphql-gateway -> field-service
graphql-gateway -> booking-service
graphql-gateway -> notification-service

booking-service -> member-service
booking-service -> field-service
booking-service -> notification-service

auth-service -> member-service
auth-service -> notification-service

notification-worker -> notification-service
```

## Message Broker

Project ini menggunakan Redis sebagai message broker.

Redis digunakan untuk mendukung proses notifikasi, terutama event OTP dan event status booking. Event dikirim ke Redis, kemudian `notification-worker` membaca event tersebut dan memproses pengiriman email melalui Notification Service.

Contoh event yang diproses:

```text
OTP registration
Booking approved
Booking rejected
Booking canceled
Manual email admin
```

## RESTful API

Setiap service menyediakan RESTful API sesuai domain masing-masing.

Service yang memiliki REST API:

```text
auth-service
member-service
field-service
booking-service
notification-service
```

REST API digunakan oleh:

```text
web-client
graphql-gateway
service lain yang membutuhkan komunikasi antar service
```

## GraphQL Gateway Manual

GraphQL Gateway manual dibuat menggunakan Laravel.

Endpoint:

```text
http://localhost:8010/api/graphql
```

Playground:

```text
http://localhost:8010/playground
```

Schema:

```text
http://localhost:8010/api/graphql/schema
```

Catatan penting:

```text
Endpoint /api/graphql hanya menerima request dengan method POST.
Jika dibuka langsung melalui browser menggunakan GET, Laravel akan menampilkan pesan MethodNotAllowed.
Untuk testing lewat browser, gunakan /playground.
```

GraphQL Gateway manual tidak menyimpan data utama. Gateway ini meneruskan query dan mutation GraphQL ke REST API service terkait, lalu mengembalikan response dalam format GraphQL.

## Hasura GraphQL

Hasura digunakan sebagai GraphQL Engine otomatis untuk kebutuhan reporting.

Endpoint:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

Hasura membaca tabel dan view reporting dari PostgreSQL `hasura_db`.

Tabel reporting:

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

## Web Client

Web Client adalah antarmuka utama sistem.

Endpoint Docker Mode:

```text
http://localhost:8090
```

Endpoint Local/XAMPP Mode:

```text
http://127.0.0.1:8090
```

Role yang tersedia:

```text
admin
member
```

## Fitur Admin

Admin dapat melakukan:

```text
1. Login melalui halaman admin.
2. Melihat dashboard admin.
3. Mengelola data lapangan.
4. Mengelola data member.
5. Mengubah status member menjadi active, inactive, atau blocked.
6. Melihat booking request.
7. Approve booking.
8. Reject booking dengan alasan.
9. Melihat semua data booking.
10. Melihat log notifikasi.
11. Mengirim email manual ke member.
```

## Fitur Member

Member dapat melakukan:

```text
1. Register akun.
2. Menerima OTP email.
3. Resend OTP.
4. Verifikasi OTP.
5. Login sebagai member.
6. Melihat dashboard member.
7. Melihat daftar lapangan.
8. Melihat detail lapangan.
9. Membuat booking lapangan.
10. Melihat status booking.
11. Membatalkan booking.
12. Mengubah profil.
13. Melihat rekomendasi pribadi berdasarkan riwayat booking.
14. Melihat lapangan terpopuler berdasarkan booking approved terbanyak.
```

## Flow Utama Sistem

```text
1. Admin login melalui web-client.
2. Admin mengelola data lapangan.
3. Admin menambahkan atau mengelola member.
4. Member melakukan register.
5. Auth Service membuat OTP dan mengirim event ke Notification Service.
6. Notification Service mengirim email OTP menggunakan template HTML.
7. Member melakukan verifikasi OTP.
8. Member login ke web-client.
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

## Seeder Demo

Project memiliki seeder demo pada service berikut:

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

Akun demo member yang digunakan:

```text
wira123widodo@gmail.com
auraiftitahh@gmail.com
muhammadagilhidayahtullah295@gmail.com
ryanalfin6@gmail.com
nabila.member@example.com
dimas.member@example.com
```

Password demo:

```text
password
```

## Rekomendasi Lapangan

Dashboard member memiliki dua jenis rekomendasi.

### 1. Rekomendasi Pribadi

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

### 2. Lapangan Terpopuler

Lapangan terpopuler dibuat berdasarkan jumlah booking `approved` terbanyak dari semua member.

Data dihitung melalui Booking Service pada endpoint:

```text
GET /api/bookings/popular-fields
```

Hanya booking dengan status `approved` yang dihitung agar rekomendasi berdasarkan booking yang benar-benar disetujui.

## Email Template

Notification Service menggunakan template email HTML untuk:

```text
1. OTP register member.
2. Email manual admin ke member.
3. Notifikasi status booking.
```

Template email berada pada:

```text
notification-service/resources/views/emails/
```

Service pembuat template berada pada:

```text
notification-service/app/Services/EmailTemplateService.php
```

## Script Project

Project menyediakan script PowerShell pada folder:

```text
scripts/
```

Script digunakan untuk mempermudah:

```text
1. Menjalankan Docker Mode.
2. Menjalankan Local/XAMPP Mode.
3. Menjalankan migration dan seeder.
4. Menghentikan service.
5. Berpindah environment .env.
```

Dokumentasi script tersedia pada:

```text
docs/script-guide.md
```
