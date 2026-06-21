# Requirement Checklist

Dokumen ini berisi checklist pemenuhan kebutuhan project Retobluto Arena Microservices berdasarkan implementasi terbaru.

Checklist ini digunakan untuk memastikan bahwa project sudah mencakup kebutuhan utama final project, yaitu microservices, RESTful API, Docker, database terpisah, message broker, GraphQL manual, Hasura, Web Client, serta dokumentasi testing.

## Ringkasan Project

| Item              | Keterangan                          |
| ----------------- | ----------------------------------- |
| Nama project      | Retobluto Arena Microservices       |
| Jenis sistem      | Sistem booking lapangan olahraga    |
| Arsitektur        | Microservices                       |
| Framework utama   | Laravel                             |
| UI utama          | Web Client Laravel                  |
| API utama         | RESTful API per service             |
| GraphQL manual    | Laravel GraphQL Gateway             |
| GraphQL reporting | Hasura GraphQL Engine               |
| Message broker    | Redis                               |
| Deployment lokal  | Docker Compose dan Local/XAMPP Mode |

## Checklist Umum

| Requirement          | Status    | Bukti Implementasi                                                                              |
| -------------------- | --------- | ----------------------------------------------------------------------------------------------- |
| Microservices        | Terpenuhi | Terdapat auth-service, member-service, field-service, booking-service, dan notification-service |
| Docker Compose       | Terpenuhi | Terdapat `docker-compose.yml` untuk menjalankan seluruh service                                 |
| RESTful API          | Terpenuhi | Setiap service memiliki route API pada `routes/api.php`                                         |
| Database per service | Terpenuhi | Setiap service utama memiliki database masing-masing                                            |
| Message broker       | Terpenuhi | Redis digunakan untuk event OTP dan notifikasi                                                  |
| Notification worker  | Terpenuhi | Terdapat `notification-worker` untuk membaca event Redis                                        |
| Web Client           | Terpenuhi | Terdapat `web-client` sebagai UI admin dan member                                               |
| Admin role           | Terpenuhi | Admin dapat login dan mengelola data                                                            |
| Member role          | Terpenuhi | Member dapat register, verifikasi OTP, login, dan booking                                       |
| OTP email            | Terpenuhi | OTP dikirim melalui Notification Service                                                        |
| Email template       | Terpenuhi | OTP dan email manual menggunakan template HTML                                                  |
| Booking flow         | Terpenuhi | Member dapat membuat booking dan admin dapat approve/reject                                     |
| Notification log     | Terpenuhi | Log notifikasi disimpan pada Notification Service                                               |
| GraphQL manual       | Terpenuhi | Terdapat `graphql-gateway` berbasis Laravel                                                     |
| Hasura GraphQL       | Terpenuhi | Hasura berjalan pada port 8080 dengan PostgreSQL `hasura_db`                                    |
| Seeder demo          | Terpenuhi | Seeder tersedia pada auth, member, field, booking, dan notification service                     |
| Script helper        | Terpenuhi | Terdapat script PowerShell pada folder `scripts/`                                               |
| Dokumentasi final    | Terpenuhi | Terdapat dokumentasi pada folder `docs/`                                                        |

## Checklist Service

| Service              | Status    | Fungsi                                                                             |
| -------------------- | --------- | ---------------------------------------------------------------------------------- |
| auth-service         | Terpenuhi | Login admin, login member, register, OTP, resend OTP, verify OTP, validasi token   |
| member-service       | Terpenuhi | Data member, profil member, update status member, dashboard stats                  |
| field-service        | Terpenuhi | Data lapangan, status lapangan, available fields, jadwal lapangan, dashboard stats |
| booking-service      | Terpenuhi | Booking, booking request, approve, reject, cancel, popular fields, dashboard stats |
| notification-service | Terpenuhi | OTP email, email manual admin, log notifikasi, dashboard stats                     |
| notification-worker  | Terpenuhi | Membaca event Redis dan memproses notifikasi                                       |
| web-client           | Terpenuhi | UI admin dan member                                                                |
| graphql-gateway      | Terpenuhi | GraphQL manual berbasis Laravel                                                    |
| hasura               | Terpenuhi | GraphQL reporting berbasis Hasura                                                  |
| redis                | Terpenuhi | Message broker                                                                     |
| hasura-db            | Terpenuhi | PostgreSQL untuk Hasura reporting                                                  |

## Checklist Database

| Database        | Service              | Engine     | Status    |
| --------------- | -------------------- | ---------- | --------- |
| auth_db         | auth-service         | MySQL      | Terpenuhi |
| member_db       | member-service       | MySQL      | Terpenuhi |
| field_db        | field-service        | MySQL      | Terpenuhi |
| booking_db      | booking-service      | MySQL      | Terpenuhi |
| notification_db | notification-service | MySQL      | Terpenuhi |
| hasura_db       | hasura               | PostgreSQL | Terpenuhi |

## Checklist RESTful API

| Service              | REST API                                                                                                                        | Status    |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------------- | --------- |
| auth-service         | `/api/admin/login`, `/api/member/login`, `/api/member/register/*`, `/api/validate-token`, `/api/me`, dan lainnya                | Terpenuhi |
| member-service       | `/api/members`, `/api/profile`, `/api/members/dashboard-stats`, dan endpoint internal member                                    | Terpenuhi |
| field-service        | `/api/fields`, `/api/fields/available`, `/api/fields/dashboard-stats`, dan schedule field                                       | Terpenuhi |
| booking-service      | `/api/bookings`, `/api/admin/booking-requests`, `/api/admin/bookings/{id}/approve`, `/api/bookings/popular-fields`, dan lainnya | Terpenuhi |
| notification-service | `/api/notifications/logs`, `/api/notifications/send-email`, `/api/notifications/send-otp`, dan endpoint internal notification   | Terpenuhi |

## Checklist Auth dan Role

| Fitur            | Status    | Keterangan                                             |
| ---------------- | --------- | ------------------------------------------------------ |
| Login admin      | Terpenuhi | Admin login melalui `/login/master`                    |
| Login member     | Terpenuhi | Member login melalui `/login`                          |
| Register member  | Terpenuhi | Member dapat register melalui Web Client               |
| Request OTP      | Terpenuhi | OTP dikirim ke email member                            |
| Resend OTP       | Terpenuhi | Member dapat meminta OTP baru                          |
| Verify OTP       | Terpenuhi | Member dapat memverifikasi OTP                         |
| Token validation | Terpenuhi | Service dapat melakukan validasi token ke Auth Service |
| Logout           | Terpenuhi | Admin/member dapat logout                              |
| Admin role       | Terpenuhi | Admin dapat mengakses dashboard admin                  |
| Member role      | Terpenuhi | Member dapat mengakses dashboard member                |

## Checklist Member

| Fitur                    | Status    | Keterangan                                            |
| ------------------------ | --------- | ----------------------------------------------------- |
| List member              | Terpenuhi | Admin dapat melihat daftar member                     |
| Tambah member oleh admin | Terpenuhi | Admin dapat membuat member                            |
| Detail member            | Terpenuhi | Admin dapat melihat detail member                     |
| Update member            | Terpenuhi | Admin dapat mengubah data member                      |
| Update status member     | Terpenuhi | Admin dapat mengubah status active, inactive, blocked |
| Delete member            | Terpenuhi | Admin dapat menghapus member                          |
| Profil member            | Terpenuhi | Member dapat melihat profil                           |
| Update profil member     | Terpenuhi | Member dapat mengubah data profil                     |
| Dashboard stats member   | Terpenuhi | Member Service menyediakan statistik member           |
| Sync from Auth Service   | Terpenuhi | Auth Service dapat sinkron data ke Member Service     |

## Checklist Field

| Fitur                  | Status    | Keterangan                                                 |
| ---------------------- | --------- | ---------------------------------------------------------- |
| List lapangan          | Terpenuhi | Admin dan member dapat melihat daftar lapangan             |
| Tambah lapangan        | Terpenuhi | Admin dapat menambah lapangan                              |
| Detail lapangan        | Terpenuhi | Admin/member dapat melihat detail lapangan                 |
| Update lapangan        | Terpenuhi | Admin dapat mengubah data lapangan                         |
| Update status lapangan | Terpenuhi | Admin dapat mengubah status lapangan                       |
| Delete lapangan        | Terpenuhi | Admin dapat menghapus lapangan                             |
| Available fields       | Terpenuhi | Field Service menyediakan daftar lapangan available        |
| Jadwal lapangan        | Terpenuhi | Field Service menyediakan endpoint jadwal booking lapangan |
| Dashboard stats field  | Terpenuhi | Field Service menyediakan statistik lapangan               |

## Checklist Booking

| Fitur                                   | Status    | Keterangan                                                            |
| --------------------------------------- | --------- | --------------------------------------------------------------------- |
| Buat booking                            | Terpenuhi | Member active dapat membuat booking                                   |
| Booking pending                         | Terpenuhi | Booking baru masuk dengan status pending                              |
| Booking request admin                   | Terpenuhi | Admin dapat melihat daftar booking pending                            |
| Approve booking                         | Terpenuhi | Admin dapat menyetujui booking                                        |
| Reject booking                          | Terpenuhi | Admin dapat menolak booking dengan alasan                             |
| Cancel booking                          | Terpenuhi | Member dapat membatalkan booking                                      |
| Detail booking                          | Terpenuhi | Admin/member dapat melihat detail booking                             |
| Riwayat booking member                  | Terpenuhi | Member dapat melihat booking miliknya                                 |
| Validasi member active                  | Terpenuhi | Member inactive/blocked tidak dapat booking                           |
| Validasi status lapangan                | Terpenuhi | Booking hanya untuk lapangan yang valid                               |
| Validasi jam booking                    | Terpenuhi | Jam booking harus sesuai aturan                                       |
| Validasi konflik jadwal                 | Terpenuhi | Booking bentrok ditolak                                               |
| Validasi satu booking aktif per tanggal | Terpenuhi | Member tidak dapat membuat booking aktif ganda pada tanggal yang sama |
| Dashboard stats booking                 | Terpenuhi | Booking Service menyediakan statistik booking                         |
| Popular fields                          | Terpenuhi | Booking Service menyediakan `/api/bookings/popular-fields`            |

## Checklist Notification

| Fitur                        | Status    | Keterangan                                                                                           |
| ---------------------------- | --------- | ---------------------------------------------------------------------------------------------------- |
| Kirim OTP                    | Terpenuhi | OTP dikirim melalui email                                                                            |
| Resend OTP                   | Terpenuhi | OTP baru dapat dikirim ulang                                                                         |
| Email status booking         | Terpenuhi | Status booking dapat dikirim melalui notifikasi                                                      |
| Email manual admin           | Terpenuhi | Admin dapat mengirim email manual ke member                                                          |
| Template OTP HTML            | Terpenuhi | OTP menggunakan template email HTML                                                                  |
| Template email manual HTML   | Terpenuhi | Email manual menggunakan template HTML                                                               |
| Notification log             | Terpenuhi | Semua log notifikasi disimpan                                                                        |
| Detail log notification      | Terpenuhi | Admin dapat melihat detail log                                                                       |
| Dashboard stats notification | Terpenuhi | Notification Service menyediakan statistik                                                           |
| Worker Redis                 | Terpenuhi | Notification worker membaca event dari Redis                                                         |
| Fix timeout email manual     | Terpenuhi | Timeout Web Client diperpanjang agar UI tidak menampilkan error palsu ketika email berhasil terkirim |

## Checklist Dashboard

| Dashboard           | Status    | Keterangan                                                                                  |
| ------------------- | --------- | ------------------------------------------------------------------------------------------- |
| Dashboard admin     | Terpenuhi | Menampilkan ringkasan member, field, booking, dan notification                              |
| Dashboard member    | Terpenuhi | Menampilkan informasi member dan rekomendasi lapangan                                       |
| Rekomendasi pribadi | Terpenuhi | Berdasarkan riwayat booking member login                                                    |
| Lapangan terpopuler | Terpenuhi | Berdasarkan booking approved terbanyak secara global                                        |
| Badge rekomendasi   | Terpenuhi | Menampilkan alasan rekomendasi seperti “Pernah kamu booking” dan “Sesuai tipe favorit kamu” |

## Checklist Rekomendasi Lapangan

| Fitur                      | Status    | Keterangan                                                  |
| -------------------------- | --------- | ----------------------------------------------------------- |
| Rekomendasi pribadi        | Terpenuhi | Menggunakan riwayat booking member yang sedang login        |
| Lapangan pernah dibooking  | Terpenuhi | Lapangan diberi prioritas jika pernah dibooking member      |
| Tipe favorit pribadi       | Terpenuhi | Tipe lapangan dihitung dari riwayat booking member          |
| Lapangan terpopuler global | Terpenuhi | Dihitung dari total booking approved seluruh member         |
| Filter available           | Terpenuhi | Rekomendasi hanya menampilkan lapangan yang masih available |
| Endpoint popular fields    | Terpenuhi | Booking Service menyediakan `/api/bookings/popular-fields`  |

## Checklist GraphQL Gateway Manual

| Requirement                           | Status    | Bukti                                                                                                    |
| ------------------------------------- | --------- | -------------------------------------------------------------------------------------------------------- |
| GraphQL menggunakan backend framework | Terpenuhi | GraphQL Gateway dibuat menggunakan Laravel                                                               |
| Endpoint GraphQL                      | Terpenuhi | `POST /api/graphql`                                                                                      |
| Playground                            | Terpenuhi | `GET /playground`                                                                                        |
| Schema endpoint                       | Terpenuhi | `GET /api/graphql/schema`                                                                                |
| Health query                          | Terpenuhi | Query `health`                                                                                           |
| Field query/mutation                  | Terpenuhi | Query fields, availableFields, field, createField, updateField, updateFieldStatus, deleteField           |
| Booking query/mutation                | Terpenuhi | Query bookings, myBookings, bookingRequests, createBooking, approveBooking, rejectBooking, cancelBooking |
| Member query/mutation                 | Terpenuhi | Query members, member, myProfile, updateProfile, updateMember, updateMemberStatus                        |
| Notification query                    | Terpenuhi | Query notificationLogs dan notificationLog                                                               |
| Dashboard query                       | Terpenuhi | Query dashboardSummary                                                                                   |
| Validasi input                        | Terpenuhi | GraphQL Gateway melakukan validasi sebelum meneruskan request                                            |
| Integrasi REST API                    | Terpenuhi | Gateway meneruskan request ke service REST API                                                           |

## Checklist Hasura

| Requirement               | Status    | Bukti                                                                                                   |
| ------------------------- | --------- | ------------------------------------------------------------------------------------------------------- |
| Hasura tersedia           | Terpenuhi | Service `hasura` pada Docker Compose                                                                    |
| Hasura Console            | Terpenuhi | `http://localhost:8080`                                                                                 |
| PostgreSQL Hasura         | Terpenuhi | Service `hasura-db` dengan database `hasura_db`                                                         |
| SQL reporting schema      | Terpenuhi | `hasura/local/schema/reporting-schema.sql`                                                              |
| Table reporting           | Terpenuhi | `report_fields`, `report_members`, `report_bookings`, `report_notification_logs`                        |
| View reporting            | Terpenuhi | `v_dashboard_summary`, `v_field_report`, `v_member_report`, `v_booking_report`, `v_notification_report` |
| Query dashboard summary   | Terpenuhi | Query `v_dashboard_summary`                                                                             |
| Query field report        | Terpenuhi | Query `v_field_report`                                                                                  |
| Query member report       | Terpenuhi | Query `v_member_report`                                                                                 |
| Query booking report      | Terpenuhi | Query `v_booking_report`                                                                                |
| Query notification report | Terpenuhi | Query `v_notification_report`                                                                           |

## Checklist Redis dan Worker

| Requirement                  | Status    | Keterangan                                              |
| ---------------------------- | --------- | ------------------------------------------------------- |
| Redis tersedia               | Terpenuhi | Service `redis` tersedia pada Docker Compose            |
| Redis sebagai message broker | Terpenuhi | Redis digunakan untuk event notifikasi                  |
| Notification worker tersedia | Terpenuhi | Service `notification-worker` tersedia                  |
| Worker membaca event Redis   | Terpenuhi | Worker menjalankan command `notifications:listen-redis` |
| Event OTP                    | Terpenuhi | OTP dapat diproses melalui Notification Service         |
| Event booking                | Terpenuhi | Status booking dapat diproses sebagai notifikasi        |

## Checklist Docker

| Requirement                    | Status    | Keterangan                                   |
| ------------------------------ | --------- | -------------------------------------------- |
| Docker Compose tersedia        | Terpenuhi | File `docker-compose.yml` tersedia           |
| Dockerfile per service         | Terpenuhi | Service Laravel memiliki Dockerfile          |
| Container database per service | Terpenuhi | MySQL container tersedia untuk service utama |
| Container Redis                | Terpenuhi | Redis tersedia pada Docker Compose           |
| Container Hasura               | Terpenuhi | Hasura tersedia pada Docker Compose          |
| Container Hasura DB            | Terpenuhi | PostgreSQL tersedia untuk Hasura             |
| Port service                   | Terpenuhi | Setiap service memiliki port masing-masing   |
| Environment Docker             | Terpenuhi | Script mendukung `.env.docker`               |
| Environment Local/XAMPP        | Terpenuhi | Script mendukung `.env.xampp`                |

## Checklist Script

| Script                       | Status    | Fungsi                                       |
| ---------------------------- | --------- | -------------------------------------------- |
| `scripts/use-docker.ps1`     | Terpenuhi | Mengaktifkan Docker Mode                     |
| `scripts/migrate-docker.ps1` | Terpenuhi | Menjalankan migration dan seeder Docker      |
| `scripts/stop-docker.ps1`    | Terpenuhi | Menghentikan Docker Mode                     |
| `scripts/use-local.ps1`      | Terpenuhi | Mengaktifkan Local/XAMPP Mode                |
| `scripts/migrate-local.ps1`  | Terpenuhi | Menjalankan migration dan seeder Local/XAMPP |
| `scripts/start-local.ps1`    | Terpenuhi | Menjalankan service Laravel lokal            |
| `scripts/stop-local.ps1`     | Terpenuhi | Menghentikan service Laravel lokal           |

## Checklist Seeder Demo

| Service              | Seeder                     | Status    |
| -------------------- | -------------------------- | --------- |
| auth-service         | Admin dan akun member demo | Terpenuhi |
| member-service       | Profile member demo        | Terpenuhi |
| field-service        | Data lapangan demo         | Terpenuhi |
| booking-service      | Data booking demo          | Terpenuhi |
| notification-service | Log notifikasi demo        | Terpenuhi |

## Data Seeder Member

Akun member demo:

| Email                                    | Nama                        | Status   |
| ---------------------------------------- | --------------------------- | -------- |
| `wira123widodo@gmail.com`                | Ahmad Aziz Wira Widodo      | active   |
| `auraiftitahh@gmail.com`                 | Aura Iftitah                | active   |
| `muhammadagilhidayahtullah295@gmail.com` | Muhammad Agil Hidayahtullah | active   |
| `ryanalfin6@gmail.com`                   | Ryan Alvin Saputra          | active   |
| `nabila.member@example.com`              | Nabila Putri Ramadhani      | inactive |
| `dimas.member@example.com`               | Dimas Pratama Wijaya        | blocked  |

Password semua akun demo:

```text id="h1m32g"
password
```

## Checklist Dokumentasi

| Dokumen                         | Status    | Keterangan                  |
| ------------------------------- | --------- | --------------------------- |
| `README.md`                     | Disiapkan | Dokumentasi utama project   |
| `docs/architecture.md`          | Disiapkan | Dokumentasi arsitektur      |
| `docs/deployment-guide.md`      | Disiapkan | Panduan menjalankan project |
| `docs/api-endpoints.md`         | Disiapkan | Daftar endpoint             |
| `docs/final-testing.md`         | Disiapkan | Skenario testing final      |
| `docs/graphql-testing.md`       | Disiapkan | Testing GraphQL Gateway     |
| `docs/hasura-testing.md`        | Disiapkan | Testing Hasura              |
| `docs/requirement-checklist.md` | Disiapkan | Checklist requirement       |
| `docs/script-guide.md`          | Disiapkan | Panduan script project      |
| `hasura/local/README.md`        | Disiapkan | Dokumentasi Hasura local    |
| `hasura/local/setup-guide.md`   | Disiapkan | Setup Hasura local          |
| `hasura/local/queries/*.md`     | Disiapkan | Contoh query Hasura         |

## Requirement yang Tidak Dijadikan Fokus Saat Ini

Beberapa dokumentasi tambahan dapat dibuat di luar commit dokumentasi ini:

```text id="sc0kfx"
1. Diagram integrasi visual.
2. Dokumentasi Postman.
3. Link GitHub repo pada dokumen laporan.
4. Outline laporan PDF final.
```

Item tersebut tidak mengurangi fungsi sistem yang sudah berjalan, tetapi dapat ditambahkan untuk kebutuhan laporan akhir atau presentasi.

## Kesimpulan Checklist

Berdasarkan checklist ini, Retobluto Arena Microservices sudah memenuhi kebutuhan utama final project:

```text id="pb3pqq"
1. Sistem berbasis microservices.
2. Setiap service memiliki RESTful API.
3. Setiap service utama memiliki database terpisah.
4. Sistem berjalan menggunakan Docker Compose.
5. Redis digunakan sebagai message broker.
6. Notification worker tersedia.
7. Web Client tersedia untuk admin dan member.
8. OTP email berjalan.
9. Booking flow berjalan.
10. Email template HTML tersedia.
11. Rekomendasi lapangan tersedia.
12. GraphQL Gateway manual berbasis Laravel tersedia.
13. Hasura GraphQL reporting tersedia.
14. Seeder demo saling terhubung.
15. Script helper tersedia.
16. Dokumentasi final tersedia.
```
