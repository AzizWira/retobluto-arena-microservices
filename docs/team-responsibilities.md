# Team Responsibilities

Dokumen ini menjelaskan pembagian tanggung jawab anggota tim pada project Retobluto Arena Microservices berdasarkan riwayat commit dan domain fitur yang dikerjakan.

Pembagian tanggung jawab dibuat agar dokumentasi project memiliki pemetaan kerja yang jelas antara anggota tim, service, fitur, GraphQL Gateway, Hasura, dan dokumentasi final. Core system, integrasi final, booking utama, Web Client, Docker, script, seeder, dan finalisasi project berada pada Ahmad Aziz Wira Widodo sebagai penanggung jawab core project.

## Daftar Anggota Tim

|  No | Nama                        | NIM          | Peran Utama                                                                                                             |
| --: | --------------------------- | ------------ | ----------------------------------------------------------------------------------------------------------------------- |
|   1 | Ahmad Aziz Wira Widodo      | 102062400112 | Core System, Booking Service, Web Client, Docker, Script, Seeder, GraphQL Integration, Hasura Base, Final Documentation |
|   2 | Aura Iftitah                | 102062400107 | Auth Service, GraphQL Gateway Core Setup, Hasura Field-Member Query, dan perbaikan dokumentasi deployment               |
|   3 | Muhammad Agil Hidayahtullah | 102062400018 | Field Service, GraphQL Field Resolver, dan Hasura Booking Query                                                         |
|   4 | Ryan Alvin Saputra          | 102062400072 | Member Service, Notification Service, GraphQL Booking Resolver, dan Hasura Notification-Dashboard Query                 |

## Pembagian Tanggung Jawab Detail

| Anggota                     | Tanggung Jawab                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| --------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Ahmad Aziz Wira Widodo      | Bertanggung jawab pada core project, struktur awal project, setup base Laravel microservices, Booking Service utama, integrasi booking, validasi booking, Web Client admin dan member, integrasi dashboard, Docker Compose, Redis networking, environment setup, script PowerShell, seeder demo antar service, rekomendasi pribadi, lapangan terpopuler, Hasura base setup dan schema, organisasi file Hasura, dokumentasi utama, serta finalisasi project. |
| Aura Iftitah                | Bertanggung jawab pada Auth Service, meliputi login admin, login member, JWT, OTP, model OTP, route auth, serta konfigurasi autentikasi. Selain itu juga mengerjakan core setup GraphQL Gateway, Hasura field-member reporting query, perbaikan instruksi migration/seeder pada dokumentasi, dan perbaikan timeout email manual pada Web Client.                                                                                                            |
| Muhammad Agil Hidayahtullah | Bertanggung jawab pada Field Service, meliputi CRUD lapangan, model field, migration field, seeder field, route field, dan authorization admin pada pengelolaan lapangan. Selain itu juga mengerjakan GraphQL field queries/mutations serta Hasura booking reporting query.                                                                                                                                                                                 |
| Ryan Alvin Saputra          | Bertanggung jawab pada Member Service, Notification Service, Redis listener awal, notification log, route notification, dan migration notification log. Selain itu juga mengerjakan GraphQL booking queries/mutations serta Hasura notification-dashboard query dan query examples.                                                                                                                                                                         |

## Pembagian Berdasarkan Service

| Service              | Penanggung Jawab Utama      | Pendukung/Finalisasi                                                    | Keterangan                                                                                                                                                                                                          |
| -------------------- | --------------------------- | ----------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| auth-service         | Aura Iftitah                | Ahmad Aziz Wira Widodo                                                  | Aura mengerjakan Auth Service JWT dan OTP. Ahmad menambahkan resend OTP, admin member auth lifecycle, dan perbaikan lanjutan.                                                                                       |
| member-service       | Ryan Alvin Saputra          | Ahmad Aziz Wira Widodo                                                  | Ryan mengerjakan profile management member. Ahmad melakukan perbaikan sync dari Auth Service, optimasi request, dan seeder final.                                                                                   |
| field-service        | Muhammad Agil Hidayahtullah | Ahmad Aziz Wira Widodo                                                  | Agil mengerjakan CRUD field utama. Ahmad melakukan optimasi request, dashboard stats, dan seeder final.                                                                                                             |
| booking-service      | Ahmad Aziz Wira Widodo      | Ryan Alvin Saputra pada GraphQL booking resolver                        | Ahmad mengerjakan Booking Service utama, integrasi booking, validasi booking, processor name, endpoint popular fields, dan rekomendasi. Ryan mengerjakan GraphQL booking queries/mutations.                         |
| notification-service | Ryan Alvin Saputra          | Ahmad Aziz Wira Widodo dan Aura Iftitah                                 | Ryan mengerjakan Notification Service awal dan Redis listener. Ahmad menambahkan email template, Redis timeout fix, optimasi notification, dan seeder final. Aura memperbaiki timeout email manual pada Web Client. |
| notification-worker  | Ryan Alvin Saputra          | Ahmad Aziz Wira Widodo                                                  | Ryan membuat listener awal. Ahmad melakukan penyesuaian dan finalisasi integrasi Redis/worker.                                                                                                                      |
| web-client           | Ahmad Aziz Wira Widodo      | Aura Iftitah pada perbaikan timeout email manual                        | Ahmad mengerjakan Web Client admin/member secara utama. Aura memperbaiki timeout request pengiriman email manual.                                                                                                   |
| graphql-gateway      | Aura Iftitah                | Ahmad Aziz Wira Widodo, Muhammad Agil Hidayahtullah, Ryan Alvin Saputra | Aura mengerjakan core setup. Agil mengerjakan field resolver. Ryan mengerjakan booking resolver. Ahmad mengerjakan auth-member, notification, dashboard resolver, dan dokumentasi GraphQL.                          |
| hasura               | Ahmad Aziz Wira Widodo      | Aura Iftitah, Muhammad Agil Hidayahtullah, Ryan Alvin Saputra           | Ahmad membuat setup awal, schema base, dan organisasi file. Aura mengerjakan field-member query. Agil mengerjakan booking query. Ryan mengerjakan notification-dashboard query dan query examples.                  |
| scripts              | Ahmad Aziz Wira Widodo      | -                                                                       | Ahmad mengerjakan script Docker Mode, Local/XAMPP Mode, migration, start, stop, dan environment switching.                                                                                                          |
| docs                 | Ahmad Aziz Wira Widodo      | Aura Iftitah pada perbaikan migration/seeder docs                       | Ahmad mengerjakan dokumentasi arsitektur, deployment, API, final testing, GraphQL testing, Hasura testing, requirement checklist, dan final documentation update.                                                   |

## Pembagian Berdasarkan Fitur

| Fitur                                 | Penanggung Jawab            |
| ------------------------------------- | --------------------------- |
| Struktur awal project                 | Ahmad Aziz Wira Widodo      |
| Setup base Laravel microservices      | Ahmad Aziz Wira Widodo      |
| Docker Compose                        | Ahmad Aziz Wira Widodo      |
| Redis networking                      | Ahmad Aziz Wira Widodo      |
| Environment Docker dan Local/XAMPP    | Ahmad Aziz Wira Widodo      |
| Script PowerShell project             | Ahmad Aziz Wira Widodo      |
| Login admin                           | Aura Iftitah                |
| Login member                          | Aura Iftitah                |
| JWT authentication                    | Aura Iftitah                |
| Register member OTP                   | Aura Iftitah                |
| Resend OTP                            | Ahmad Aziz Wira Widodo      |
| Verify OTP                            | Aura Iftitah                |
| Admin member auth lifecycle           | Ahmad Aziz Wira Widodo      |
| Member profile management             | Ryan Alvin Saputra          |
| Sync member dari Auth Service         | Ahmad Aziz Wira Widodo      |
| CRUD lapangan                         | Muhammad Agil Hidayahtullah |
| Status lapangan                       | Muhammad Agil Hidayahtullah |
| Dashboard stats field                 | Ahmad Aziz Wira Widodo      |
| Booking Service utama                 | Ahmad Aziz Wira Widodo      |
| Create booking                        | Ahmad Aziz Wira Widodo      |
| Booking request                       | Ahmad Aziz Wira Widodo      |
| Approve booking                       | Ahmad Aziz Wira Widodo      |
| Reject booking                        | Ahmad Aziz Wira Widodo      |
| Cancel booking                        | Ahmad Aziz Wira Widodo      |
| Validasi konflik jadwal               | Ahmad Aziz Wira Widodo      |
| Validasi status member pada booking   | Ahmad Aziz Wira Widodo      |
| Validasi status lapangan pada booking | Ahmad Aziz Wira Widodo      |
| Popular fields endpoint               | Ahmad Aziz Wira Widodo      |
| Rekomendasi pribadi member            | Ahmad Aziz Wira Widodo      |
| Lapangan terpopuler                   | Ahmad Aziz Wira Widodo      |
| Notification Service awal             | Ryan Alvin Saputra          |
| Redis listener awal                   | Ryan Alvin Saputra          |
| Notification log                      | Ryan Alvin Saputra          |
| Email template HTML                   | Ahmad Aziz Wira Widodo      |
| Fix timeout email manual              | Aura Iftitah                |
| Web Client admin/member               | Ahmad Aziz Wira Widodo      |
| Dashboard admin                       | Ahmad Aziz Wira Widodo      |
| Dashboard member                      | Ahmad Aziz Wira Widodo      |
| GraphQL Gateway core setup            | Aura Iftitah                |
| GraphQL field query/mutation          | Muhammad Agil Hidayahtullah |
| GraphQL booking query/mutation        | Ryan Alvin Saputra          |
| GraphQL auth-member resolver          | Ahmad Aziz Wira Widodo      |
| GraphQL notification resolver         | Ahmad Aziz Wira Widodo      |
| GraphQL dashboard resolver            | Ahmad Aziz Wira Widodo      |
| Hasura setup awal                     | Ahmad Aziz Wira Widodo      |
| Hasura reporting schema base          | Ahmad Aziz Wira Widodo      |
| Hasura field-member query             | Aura Iftitah                |
| Hasura booking query                  | Muhammad Agil Hidayahtullah |
| Hasura notification-dashboard query   | Ryan Alvin Saputra          |
| Organisasi folder Hasura              | Ahmad Aziz Wira Widodo      |
| Seeder demo antar service             | Ahmad Aziz Wira Widodo      |
| Dokumentasi final project             | Ahmad Aziz Wira Widodo      |

## Pembagian Berdasarkan GraphQL Gateway

| Bagian GraphQL Gateway                         | Penanggung Jawab                                     |
| ---------------------------------------------- | ---------------------------------------------------- |
| Core setup GraphQL Gateway                     | Aura Iftitah                                         |
| GraphQL parser, executor, health resolver awal | Aura Iftitah                                         |
| Field resolver                                 | Muhammad Agil Hidayahtullah                          |
| Field service client                           | Muhammad Agil Hidayahtullah                          |
| Booking resolver                               | Ryan Alvin Saputra                                   |
| Booking service client                         | Ryan Alvin Saputra                                   |
| Auth-member resolver                           | Ahmad Aziz Wira Widodo                               |
| Notification resolver                          | Ahmad Aziz Wira Widodo                               |
| Dashboard resolver                             | Ahmad Aziz Wira Widodo                               |
| Auth service client                            | Ahmad Aziz Wira Widodo                               |
| Member service client                          | Ahmad Aziz Wira Widodo                               |
| Notification service client                    | Ahmad Aziz Wira Widodo                               |
| Schema GraphQL final                           | Seluruh anggota sesuai bagian resolver masing-masing |

## Pembagian Berdasarkan Hasura

| Bagian Hasura                                                      | Penanggung Jawab            |
| ------------------------------------------------------------------ | --------------------------- |
| Setup guide Hasura Local                                           | Ahmad Aziz Wira Widodo      |
| Reporting schema base                                              | Ahmad Aziz Wira Widodo      |
| Field-member reporting query                                       | Aura Iftitah                |
| Booking reporting query                                            | Muhammad Agil Hidayahtullah |
| Notification-dashboard query                                       | Ryan Alvin Saputra          |
| Query examples                                                     | Ryan Alvin Saputra          |
| Organisasi folder `hasura/local/queries` dan `hasura/local/schema` | Ahmad Aziz Wira Widodo      |
| Finalisasi schema reporting agar selaras dengan seeder terbaru     | Ahmad Aziz Wira Widodo      |

## Tanggung Jawab Core Project

Core project berada pada Ahmad Aziz Wira Widodo.

Bagian core project meliputi:

```text
1. Menyusun struktur awal project.
2. Menyiapkan base Laravel microservices.
3. Mengatur Docker Compose.
4. Mengatur environment Docker dan Local/XAMPP.
5. Membuat script PowerShell.
6. Mengembangkan Booking Service utama.
7. Mengembangkan Web Client admin dan member.
8. Mengintegrasikan service melalui REST API.
9. Mengatur Redis dan notification worker.
10. Menyusun GraphQL resolver lanjutan untuk auth-member, notification, dan dashboard.
11. Menyiapkan Hasura Local base dan reporting schema.
12. Menyelaraskan seeder demo antar service.
13. Mengoptimalkan service request dan dashboard performance.
14. Menambahkan fitur rekomendasi pribadi dan lapangan terpopuler.
15. Menyusun dokumentasi final project.
```

## Catatan Pembagian

Pembagian ini dibuat berdasarkan commit history project. Beberapa fitur mengalami perbaikan atau finalisasi oleh anggota lain setelah implementasi awal. Oleh karena itu, tabel membedakan antara penanggung jawab utama dan pendukung/finalisasi.

Contoh:

```text
1. Auth Service utama dikerjakan oleh Aura Iftitah, tetapi beberapa fitur lanjutan seperti resend OTP dan admin member auth lifecycle dikerjakan oleh Ahmad Aziz Wira Widodo.
2. Member Service utama dikerjakan oleh Ryan Alvin Saputra, tetapi sync dari Auth Service dan optimasi lanjutan dikerjakan oleh Ahmad Aziz Wira Widodo.
3. Notification Service awal dikerjakan oleh Ryan Alvin Saputra, tetapi email template dan finalisasi Redis/notification dilakukan oleh Ahmad Aziz Wira Widodo.
4. Booking Service utama dikerjakan oleh Ahmad Aziz Wira Widodo, sedangkan GraphQL booking resolver dikerjakan oleh Ryan Alvin Saputra.
5. Hasura dikerjakan bersama berdasarkan jenis query, tetapi setup awal dan organisasi akhir berada pada Ahmad Aziz Wira Widodo.
```

## Kesimpulan

Pembagian tanggung jawab pada project Retobluto Arena Microservices dilakukan berdasarkan domain service, fitur, dan riwayat commit. Ahmad Aziz Wira Widodo berperan sebagai penanggung jawab core system dan final integration. Aura Iftitah berfokus pada Auth Service dan GraphQL core setup. Muhammad Agil Hidayahtullah berfokus pada Field Service dan query terkait field/booking reporting. Ryan Alvin Saputra berfokus pada Member Service, Notification Service, GraphQL booking resolver, dan notification-dashboard reporting.

Dengan pembagian ini, dokumentasi project menjadi lebih sesuai dengan riwayat pengerjaan dan tetap menunjukkan bahwa project dikerjakan secara kolaboratif.
