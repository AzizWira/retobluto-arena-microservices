# Deployment Guide

Dokumen ini menjelaskan cara menjalankan project Retobluto Arena Microservices secara lokal menggunakan Docker Mode maupun Local/XAMPP Mode.

## Mode Menjalankan Project

Project ini mendukung dua mode environment:

```text
1. Docker Mode
2. Local/XAMPP Mode
```

Docker Mode digunakan untuk menjalankan seluruh service melalui Docker Compose. Mode ini direkomendasikan untuk demo final karena seluruh service berjalan dalam environment yang sama.

Local/XAMPP Mode digunakan untuk menjalankan service Laravel langsung dari host Windows menggunakan `php artisan serve`, sedangkan Redis dan Hasura tetap dijalankan melalui Docker.

## Prasyarat

Pastikan perangkat sudah memiliki:

```text
Docker Desktop
Docker Compose
Git
PHP
Composer
PowerShell
Browser
Windows Terminal
XAMPP MySQL
```

Catatan:

```text
Windows Terminal dibutuhkan jika ingin menjalankan start-local.ps1 karena script tersebut membuka beberapa tab terminal service secara otomatis.
```

## Struktur Service

| Service              | Docker URL            | Local/XAMPP URL       | Fungsi                            |
| -------------------- | --------------------- | --------------------- | --------------------------------- |
| auth-service         | http://localhost:8001 | http://127.0.0.1:8001 | Auth, login, register, OTP, token |
| member-service       | http://localhost:8002 | http://127.0.0.1:8002 | Data member dan profil            |
| field-service        | http://localhost:8003 | http://127.0.0.1:8003 | Data lapangan                     |
| booking-service      | http://localhost:8004 | http://127.0.0.1:8004 | Booking, approve, reject, cancel  |
| notification-service | http://localhost:8005 | http://127.0.0.1:8005 | Email, OTP, dan log notifikasi    |
| web-client           | http://localhost:8090 | http://127.0.0.1:8090 | UI admin dan member               |
| graphql-gateway      | http://localhost:8010 | http://127.0.0.1:8010 | GraphQL Gateway manual            |
| hasura               | http://localhost:8080 | http://localhost:8080 | Hasura GraphQL reporting          |

## Database Service

| Service              | Database        | Engine     |
| -------------------- | --------------- | ---------- |
| auth-service         | auth_db         | MySQL      |
| member-service       | member_db       | MySQL      |
| field-service        | field_db        | MySQL      |
| booking-service      | booking_db      | MySQL      |
| notification-service | notification_db | MySQL      |
| hasura               | hasura_db       | PostgreSQL |

## Akun Demo

Seeder project menyediakan akun admin dan beberapa akun member demo.

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

Daftar akun member demo:

```text
wira123widodo@gmail.com
auraiftitahh@gmail.com
muhammadagilhidayahtullah295@gmail.com
ryanalfin6@gmail.com
nabila.member@example.com
dimas.member@example.com
```

Catatan status member:

```text
Ahmad Aziz Wira Widodo       -> active
Aura Iftitah                 -> active
Muhammad Agil Hidayahtullah  -> active
Ryan Alvin Saputra           -> active
Nabila Putri Ramadhani       -> inactive
Dimas Pratama Wijaya         -> blocked
```

Data member, field, booking, dan notification pada seeder dibuat saling terhubung menggunakan ID tetap agar data antar service tidak terputus.

---

# A. Docker Mode

Docker Mode menjalankan seluruh service melalui Docker Compose.

Mode ini direkomendasikan untuk:

```text
Demo final project
Pengujian environment penuh
Menjalankan semua service tanpa XAMPP MySQL
Menguji Redis, worker, GraphQL Gateway, dan Hasura secara bersamaan
```

## 1. Masuk ke Root Project

```powershell
cd "D:\KAMPUS\Page - 4\EAI\TUBES\retobluto-arena-microservices"
```

## 2. Jalankan Docker Mode Menggunakan Script

Gunakan script:

```powershell
.\scripts\use-docker.ps1
```

Script ini menjalankan proses:

```text
1. Mengecek Docker Desktop.
2. Mengaktifkan file environment Docker.
3. Menghentikan stack lama jika ada.
4. Menjalankan docker compose up -d --build.
5. Membersihkan cache/config Laravel pada container.
6. Menampilkan endpoint utama project.
```

Setelah selesai, akses:

```text
Web Client      : http://localhost:8090
GraphQL Gateway : http://localhost:8010
Hasura          : http://localhost:8080
```

## 3. Jalankan Migration dan Seeder Docker

Gunakan script:

```powershell
.\scripts\migrate-docker.ps1
```

Script ini menjalankan reset database dan seeder untuk service utama.

Perintah inti yang dijalankan:

```text
auth-service          -> php artisan migrate:fresh --seed
member-service        -> php artisan migrate:fresh --seed
field-service         -> php artisan migrate:fresh --seed
booking-service       -> php artisan migrate:fresh --seed
notification-service  -> php artisan migrate:fresh
notification-service  -> php artisan db:seed
```

Setelah migration dan seeder selesai, script juga melakukan:

```text
1. Flush Redis.
2. Restart service Laravel.
3. Restart notification-worker.
4. Restart web-client.
5. Restart graphql-gateway.
```

Catatan penting:

```text
migrate-docker.ps1 menggunakan migrate:fresh sehingga seluruh data lama akan dihapus dan dibuat ulang.
Gunakan script ini ketika ingin menyiapkan data demo dari awal.
```

## 4. Cek Container Docker

```powershell
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

## 5. Akses Web Client

Buka:

```text
http://localhost:8090
```

Halaman login admin:

```text
http://localhost:8090/login/master
```

Halaman login member:

```text
http://localhost:8090/login
```

## 6. Akses GraphQL Gateway

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

Catatan:

```text
Endpoint /api/graphql hanya menerima POST.
Jika dibuka langsung di browser dengan GET, Laravel akan menampilkan pesan MethodNotAllowed.
Gunakan /playground untuk testing melalui browser.
```

## 7. Akses Hasura

Buka:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

Jalankan SQL reporting dari file:

```text
hasura/local/schema/reporting-schema.sql
```

Langkah pada Hasura Console:

```text
Data -> SQL -> paste isi reporting-schema.sql -> Run
```

Setelah itu track table:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

Track view:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

## 8. Stop Docker Mode

Gunakan script:

```powershell
.\scripts\stop-docker.ps1
```

Script ini menghentikan seluruh stack Docker project.

---

# B. Local/XAMPP Mode

Local/XAMPP Mode menjalankan service Laravel langsung menggunakan PHP lokal. Database MySQL menggunakan XAMPP, sedangkan Redis dan Hasura tetap menggunakan Docker.

Mode ini cocok untuk:

```text
Pengembangan lokal
Debugging service Laravel
Menjalankan service satu per satu dari host Windows
Menggunakan MySQL XAMPP
```

## 1. Pastikan XAMPP MySQL Berjalan

Sebelum menjalankan Local/XAMPP Mode, pastikan MySQL pada XAMPP sudah aktif.

Database yang dibutuhkan:

```text
auth_db
member_db
field_db
booking_db
notification_db
```

## 2. Aktifkan Local/XAMPP Mode

Gunakan script:

```powershell
.\scripts\use-local.ps1
```

Script ini menjalankan proses:

```text
1. Mengecek Docker Desktop.
2. Mengaktifkan file environment Local/XAMPP.
3. Menghentikan stack Docker penuh.
4. Menjalankan container pendukung Redis, Hasura DB, dan Hasura.
5. Membersihkan cache/config Laravel pada setiap service.
```

Container pendukung yang tetap berjalan:

```text
redis
hasura-db
hasura
```

## 3. Jalankan Migration dan Seeder Local

Gunakan script:

```powershell
.\scripts\migrate-local.ps1
```

Script ini menjalankan migration dan seeder menggunakan PHP lokal.

Perintah inti yang dijalankan:

```text
auth-service          -> optimize:clear
auth-service          -> migrate:fresh --seed

member-service        -> optimize:clear
member-service        -> migrate:fresh --seed

field-service         -> optimize:clear
field-service         -> migrate:fresh --seed

booking-service       -> optimize:clear
booking-service       -> migrate:fresh --seed

notification-service  -> optimize:clear
notification-service  -> migrate:fresh
notification-service  -> db:seed

web-client            -> optimize:clear
graphql-gateway       -> optimize:clear
```

Catatan penting:

```text
migrate-local.ps1 menggunakan migrate:fresh sehingga seluruh data lama akan dihapus dan dibuat ulang.
Gunakan script ini ketika ingin reset data demo pada environment Local/XAMPP.
```

## 4. Jalankan Service Local

Gunakan script:

```powershell
.\scripts\start-local.ps1
```

Script ini membuka Windows Terminal tab untuk menjalankan service Laravel.

Service yang dijalankan:

```text
auth-service          -> php artisan serve --host=127.0.0.1 --port=8001
member-service        -> php artisan serve --host=127.0.0.1 --port=8002
field-service         -> php artisan serve --host=127.0.0.1 --port=8003
booking-service       -> php artisan serve --host=127.0.0.1 --port=8004
notification-service  -> php artisan serve --host=127.0.0.1 --port=8005
web-client            -> php artisan serve --host=127.0.0.1 --port=8090
graphql-gateway       -> php artisan serve --host=127.0.0.1 --port=8010
notification-worker   -> php artisan notifications:listen-redis
```

Setelah script selesai, akses:

```text
Web Client      : http://127.0.0.1:8090
GraphQL Gateway : http://127.0.0.1:8010
Hasura          : http://localhost:8080
```

## 5. Stop Local/XAMPP Mode

Gunakan script:

```powershell
.\scripts\stop-local.ps1
```

Script ini menghentikan:

```text
Service Laravel pada port 8001, 8002, 8003, 8004, 8005, 8090, dan 8010
Notification worker
Container pendukung Redis, Hasura, dan Hasura DB
```

Jika ingin menghentikan service Laravel lokal tetapi tetap membiarkan Redis dan Hasura berjalan:

```powershell
.\scripts\stop-local.ps1 -KeepDocker
```

---

# C. Manual Docker Command

Jika tidak menggunakan script, Docker Mode juga bisa dijalankan manual.

## 1. Build dan Jalankan Container

```powershell
docker compose up -d --build
```

## 2. Jalankan Migration

```powershell
docker compose exec auth-service php artisan migrate
docker compose exec member-service php artisan migrate
docker compose exec field-service php artisan migrate
docker compose exec booking-service php artisan migrate
docker compose exec notification-service php artisan migrate
```

## 3. Jalankan Seeder

```powershell
docker compose exec auth-service php artisan db:seed
docker compose exec member-service php artisan db:seed
docker compose exec field-service php artisan db:seed
docker compose exec booking-service php artisan db:seed
docker compose exec notification-service php artisan db:seed
```

## 4. Reset Database Jika Dibutuhkan

Jika ingin menghapus seluruh data dan membuat ulang data demo:

```powershell
docker compose exec auth-service php artisan migrate:fresh --seed
docker compose exec member-service php artisan migrate:fresh --seed
docker compose exec field-service php artisan migrate:fresh --seed
docker compose exec booking-service php artisan migrate:fresh --seed
docker compose exec notification-service php artisan migrate:fresh
docker compose exec notification-service php artisan db:seed
```

Catatan:

```text
Command migrate:fresh akan menghapus seluruh data lama pada database service terkait.
```

## 5. Bersihkan Cache Laravel

```powershell
docker compose exec auth-service php artisan optimize:clear
docker compose exec member-service php artisan optimize:clear
docker compose exec field-service php artisan optimize:clear
docker compose exec booking-service php artisan optimize:clear
docker compose exec notification-service php artisan optimize:clear
docker compose exec web-client php artisan optimize:clear
docker compose exec graphql-gateway php artisan optimize:clear
```

## 6. Restart Service

```powershell
docker compose restart auth-service member-service field-service booking-service notification-service notification-worker web-client graphql-gateway
```

---

# D. Manual Local Command

Jika tidak menggunakan script, setiap service lokal dapat dijalankan manual.

## 1. Jalankan Service Auth

```powershell
cd auth-service
php artisan serve --host=127.0.0.1 --port=8001
```

## 2. Jalankan Service Member

```powershell
cd member-service
php artisan serve --host=127.0.0.1 --port=8002
```

## 3. Jalankan Service Field

```powershell
cd field-service
php artisan serve --host=127.0.0.1 --port=8003
```

## 4. Jalankan Service Booking

```powershell
cd booking-service
php artisan serve --host=127.0.0.1 --port=8004
```

## 5. Jalankan Service Notification

```powershell
cd notification-service
php artisan serve --host=127.0.0.1 --port=8005
```

## 6. Jalankan Notification Worker

```powershell
cd notification-service
php artisan notifications:listen-redis
```

## 7. Jalankan Web Client

```powershell
cd web-client
php artisan serve --host=127.0.0.1 --port=8090
```

## 8. Jalankan GraphQL Gateway

```powershell
cd graphql-gateway
php artisan serve --host=127.0.0.1 --port=8010
```

---

# E. Setup Hasura Reporting

Hasura tidak otomatis membaca database MySQL service utama. Hasura menggunakan database PostgreSQL `hasura_db` untuk reporting.

## 1. Buka Hasura Console

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

## 2. Jalankan SQL Reporting

Buka:

```text
Data -> SQL
```

Jalankan isi file:

```text
hasura/local/schema/reporting-schema.sql
```

## 3. Track Table dan View

Track table:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

Track view:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

## 4. Test Query Hasura

Masuk ke menu:

```text
API
```

Jalankan:

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

# F. Testing Cepat Setelah Deployment

Setelah service berjalan, lakukan testing cepat berikut:

```text
1. Login admin melalui http://localhost:8090/login/master
2. Cek data member dari menu admin.
3. Cek data lapangan dari menu admin.
4. Cek booking request dari menu admin.
5. Login member menggunakan salah satu akun demo.
6. Cek dashboard member.
7. Cek rekomendasi pribadi.
8. Cek lapangan terpopuler.
9. Buat booking baru.
10. Approve atau reject booking dari admin.
11. Cek log notifikasi.
12. Test GraphQL Gateway dari /playground.
13. Test Hasura dari http://localhost:8080.
```

## Testing Email

Untuk menguji email OTP:

```text
1. Buka halaman register member.
2. Isi data member baru.
3. Submit register.
4. Cek email OTP yang masuk.
```

Untuk menguji email manual admin:

```text
1. Login sebagai admin.
2. Masuk menu Notification.
3. Buka form kirim email.
4. Isi email tujuan, subject, dan message.
5. Kirim.
6. Cek email masuk dan log notifikasi.
```

---

# G. Troubleshooting

## 1. Docker Desktop Belum Berjalan

Jika script menampilkan Docker belum siap, buka Docker Desktop terlebih dahulu lalu tunggu sampai status Docker ready.

Setelah itu jalankan ulang script.

## 2. Port Sudah Digunakan

Jika port service sudah digunakan, cek proses pada port terkait.

Port yang digunakan:

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

## 3. Email Terkirim Tetapi UI Menampilkan Error

Jika email berhasil masuk tetapi UI menampilkan pesan error, pastikan timeout pada web-client sudah menggunakan timeout yang cukup untuk request ke Notification Service.

File terkait:

```text
web-client/app/Http/Controllers/Web/BaseWebController.php
web-client/app/Http/Controllers/Web/Admin/NotificationController.php
```

## 4. GraphQL Endpoint Menampilkan MethodNotAllowed

Jika membuka:

```text
http://localhost:8010/api/graphql
```

dan muncul pesan:

```text
GET method is not supported for route api/graphql. Supported methods: POST.
```

Itu normal karena endpoint tersebut hanya menerima POST.

Gunakan:

```text
http://localhost:8010/playground
```

untuk testing melalui browser.

## 5. Hasura Tidak Menampilkan Table

Jika table atau view tidak muncul di Hasura:

```text
1. Pastikan hasura-db dan hasura berjalan.
2. Jalankan ulang reporting-schema.sql.
3. Masuk Data -> public.
4. Track table dan view secara manual.
```

## 6. Data Hasura Tidak Sama dengan Data Web

Hasura menggunakan database reporting terpisah. Data Hasura berasal dari `reporting-schema.sql`, bukan langsung dari database MySQL service utama.

Jika ingin data sama, update isi `reporting-schema.sql` atau lakukan sinkronisasi data reporting.
