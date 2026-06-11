# Architecture Documentation

## Nama Project

Retobluto Arena Microservices

## Deskripsi Singkat

Retobluto Arena Microservices adalah sistem booking lapangan olahraga berbasis microservices. Sistem ini memiliki fitur autentikasi admin dan member, registrasi member menggunakan OTP email, pengelolaan lapangan, pengelolaan member, proses booking lapangan, approval booking oleh admin, cancel booking oleh member, notifikasi, RESTful API, GraphQL Gateway manual, serta Hasura GraphQL untuk reporting.

## Tujuan Arsitektur

Project ini dirancang dengan pendekatan microservices agar setiap domain utama memiliki service dan database masing-masing. Dengan pendekatan ini, setiap service memiliki tanggung jawab yang lebih jelas dan tidak bergantung secara langsung pada database service lain.

## Daftar Service

| Service              |     Port | Database         | Fungsi Utama                                             |
| -------------------- | -------: | ---------------- | -------------------------------------------------------- |
| auth-service         |     8001 | auth_db          | Login admin/member, register member, OTP, validasi token |
| member-service       |     8002 | member_db        | Manajemen data member dan profil member                  |
| field-service        |     8003 | field_db         | Manajemen data lapangan                                  |
| booking-service      |     8004 | booking_db       | Booking lapangan, approval, reject, cancel               |
| notification-service |     8005 | notification_db  | Pengiriman OTP dan log notifikasi                        |
| notification-worker  |        - | notification_db  | Worker Redis untuk memproses event notifikasi            |
| web-client           |     8090 | SQLite sementara | UI admin dan member                                      |
| graphql-gateway      |     8010 | SQLite sementara | GraphQL Gateway manual berbasis Laravel                  |
| hasura               |     8080 | hasura_db        | GraphQL otomatis untuk reporting                         |
| redis                | internal | -                | Message broker untuk OTP dan notifikasi                  |

## Database

Project ini menggunakan pemisahan database per service.

| Database        | Engine     | Digunakan Oleh       |
| --------------- | ---------- | -------------------- |
| auth_db         | MySQL      | auth-service         |
| member_db       | MySQL      | member-service       |
| field_db        | MySQL      | field-service        |
| booking_db      | MySQL      | booking-service      |
| notification_db | MySQL      | notification-service |
| hasura_db       | PostgreSQL | Hasura reporting     |

## Alasan Database Hasura Dipisah

Database Hasura sengaja dipisahkan dari database utama microservices. Setiap service tetap memiliki database masing-masing sesuai prinsip microservices, sedangkan Hasura menggunakan PostgreSQL khusus untuk kebutuhan reporting/read-only query.

Dengan pendekatan ini, Hasura tidak mengambil alih proses transaksi utama. Hasura hanya digunakan untuk membaca data reporting yang sudah disiapkan pada `hasura_db`.

## Komunikasi Antar Service

Komunikasi utama antar service menggunakan REST API.

Contoh komunikasi:

```text
web-client -> auth-service
web-client -> member-service
web-client -> field-service
web-client -> booking-service
web-client -> notification-service

booking-service -> field-service
booking-service -> member-service
booking-service -> notification-service

auth-service -> member-service
auth-service -> notification-service
```

## Message Broker

Project ini menggunakan Redis sebagai message broker.

Redis digunakan untuk mendukung proses notifikasi, terutama pengiriman OTP dan event notifikasi booking. Notification worker membaca event dari Redis lalu memproses pengiriman email/notifikasi dan menyimpan log ke database notification.

## RESTful API

Setiap service menyediakan RESTful API sesuai domain masing-masing. REST API digunakan oleh web-client dan juga GraphQL Gateway manual.

## GraphQL Gateway Manual

GraphQL Gateway manual dibuat menggunakan Laravel sebagai backend framework.

Endpoint:

```text
http://localhost:8010/api/graphql
```

Playground:

```text
http://localhost:8010/playground
```

GraphQL Gateway manual tidak menyimpan data utama. Gateway ini bertugas meneruskan request GraphQL ke REST API service terkait, lalu mengembalikan response dalam format GraphQL.

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

Hasura membaca tabel dan view reporting pada PostgreSQL `hasura_db`.

## Web Client

Web Client digunakan sebagai UI utama untuk admin dan member.

Endpoint:

```text
http://localhost:8090
```

Role yang tersedia:

- Admin
- Member

## Flow Utama Sistem

```text
1. Admin login melalui web-client.
2. Admin mengelola data lapangan.
3. Admin dapat menambahkan data member.
4. Member dapat register menggunakan OTP email.
5. Member melakukan verifikasi OTP.
6. Member login.
7. Member memilih lapangan.
8. Member membuat booking.
9. Booking masuk dengan status pending.
10. Admin approve atau reject booking.
11. Member dapat melihat status booking.
12. Member dapat cancel booking jika booking masih sesuai aturan.
13. Notification Service mencatat log notifikasi.
14. Hasura menampilkan data reporting.
```

## Status Booking

| Status   | Keterangan                        |
| -------- | --------------------------------- |
| pending  | Booking baru diajukan oleh member |
| approved | Booking disetujui admin           |
| rejected | Booking ditolak admin             |
| canceled | Booking dibatalkan member         |

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
