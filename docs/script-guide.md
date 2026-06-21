# Script Guide

Dokumen ini menjelaskan penggunaan script PowerShell pada project Retobluto Arena Microservices.

Folder `scripts/` digunakan untuk mempermudah proses menjalankan project, berpindah environment, menjalankan migration dan seeder, serta menghentikan service.

## Daftar Script

```text id="r5ql2t"
scripts/
├── migrate-docker.ps1
├── migrate-local.ps1
├── start-local.ps1
├── stop-docker.ps1
├── stop-local.ps1
├── use-docker.ps1
└── use-local.ps1
```

## Fungsi Umum Script

| Script               | Fungsi                                                                        |
| -------------------- | ----------------------------------------------------------------------------- |
| `use-docker.ps1`     | Mengaktifkan Docker Mode dan menjalankan semua service melalui Docker Compose |
| `migrate-docker.ps1` | Reset database Docker menggunakan migration dan seeder                        |
| `stop-docker.ps1`    | Menghentikan seluruh container Docker project                                 |
| `use-local.ps1`      | Mengaktifkan Local/XAMPP Mode                                                 |
| `migrate-local.ps1`  | Reset database Local/XAMPP menggunakan migration dan seeder                   |
| `start-local.ps1`    | Menjalankan semua service Laravel lokal menggunakan `php artisan serve`       |
| `stop-local.ps1`     | Menghentikan service Laravel lokal dan container pendukung                    |

## Mode Environment

Project ini mendukung dua mode:

```text id="ra4flk"
1. Docker Mode
2. Local/XAMPP Mode
```

### Docker Mode

Docker Mode menjalankan seluruh service menggunakan Docker Compose.

Cocok untuk:

```text id="lub7zx"
1. Demo final.
2. Testing seluruh service secara bersamaan.
3. Menjalankan database, Redis, Hasura, worker, Web Client, dan GraphQL Gateway dalam container.
```

### Local/XAMPP Mode

Local/XAMPP Mode menjalankan service Laravel menggunakan PHP lokal dan XAMPP MySQL.

Redis dan Hasura tetap berjalan melalui Docker.

Cocok untuk:

```text id="fbahye"
1. Pengembangan lokal.
2. Debugging service Laravel.
3. Menjalankan service langsung dari terminal Windows.
```

---

# 1. Persiapan Sebelum Menjalankan Script

## Prasyarat Umum

Pastikan perangkat sudah memiliki:

```text id="ugwdfw"
Docker Desktop
Docker Compose
PHP
Composer
PowerShell
Windows Terminal
XAMPP MySQL
```

Catatan:

```text id="c9v91b"
Windows Terminal dibutuhkan untuk menjalankan start-local.ps1 karena script tersebut membuka beberapa tab terminal untuk service Laravel.
```

## Jalankan Script dari Root Project

Seluruh script dijalankan dari root project.

Contoh:

```powershell id="c22ql3"
cd "D:\KAMPUS\Page - 4\EAI\TUBES\retobluto-arena-microservices"
```

Lalu jalankan script:

```powershell id="n6xjpl"
.\scripts\nama-script.ps1
```

## File Environment yang Dibutuhkan

Script environment menggunakan file:

```text id="0v7gko"
.env.docker
.env.xampp
.env
```

Keterangan:

| File          | Fungsi                                          |
| ------------- | ----------------------------------------------- |
| `.env.docker` | Environment untuk Docker Mode                   |
| `.env.xampp`  | Environment untuk Local/XAMPP Mode              |
| `.env`        | Environment aktif yang sedang digunakan service |

Catatan penting:

```text id="lwtrn8"
File .env, .env.docker, dan .env.xampp termasuk file environment lokal.
File tersebut tidak disimpan ke repository karena berisi konfigurasi environment.
```

Pada repository, file yang disediakan sebagai acuan adalah:

```text id="8itx2d"
.env.example
```

Jika `.env.docker` atau `.env.xampp` belum ada, buat dari `.env.example` lalu sesuaikan konfigurasi database, service URL, Redis, dan mail.

---

# 2. Script use-docker.ps1

## Fungsi

Script ini digunakan untuk mengaktifkan Docker Mode.

Command:

```powershell id="8yo7dt"
.\scripts\use-docker.ps1
```

## Proses yang Dilakukan

Script ini menjalankan proses:

```text id="j05aho"
1. Mengecek apakah Docker Desktop sudah berjalan.
2. Mengatur environment service ke mode Docker.
3. Menggunakan file .env.docker sebagai .env aktif.
4. Menghentikan stack Docker lama jika ada.
5. Menjalankan docker compose up -d --build.
6. Membersihkan cache Laravel pada container service.
7. Menampilkan endpoint utama project.
```

## Service yang Dijalankan

Docker Mode menjalankan service:

```text id="rbud9n"
auth-service
member-service
field-service
booking-service
notification-service
notification-worker
web-client
graphql-gateway
redis
hasura-db
hasura
database MySQL masing-masing service
```

## URL Setelah Docker Mode Aktif

```text id="a0k1po"
Web Client      : http://localhost:8090
Auth Service    : http://localhost:8001
Member Service  : http://localhost:8002
Field Service   : http://localhost:8003
Booking Service : http://localhost:8004
Notification    : http://localhost:8005
GraphQL Gateway : http://localhost:8010
Hasura          : http://localhost:8080
```

## Catatan

```text id="bdkc5a"
use-docker.ps1 akan mengganti .env aktif pada setiap service menggunakan konfigurasi Docker.
Pastikan .env.docker sudah tersedia pada service yang membutuhkan.
```

---

# 3. Script migrate-docker.ps1

## Fungsi

Script ini digunakan untuk menjalankan reset database, migration, dan seeder pada Docker Mode.

Command:

```powershell id="l7yfeo"
.\scripts\migrate-docker.ps1
```

## Proses yang Dilakukan

Script ini menjalankan migration dan seeder pada container Laravel.

Service yang diproses:

```text id="x3u1rb"
auth-service
member-service
field-service
booking-service
notification-service
```

## Perintah Inti

Script menjalankan proses berikut:

```text id="fn4dzy"
auth-service          -> php artisan migrate:fresh --seed
member-service        -> php artisan migrate:fresh --seed
field-service         -> php artisan migrate:fresh --seed
booking-service       -> php artisan migrate:fresh --seed
notification-service  -> php artisan migrate:fresh
notification-service  -> php artisan db:seed
```

Setelah itu script juga melakukan:

```text id="pimct3"
1. Flush Redis.
2. Restart service Laravel.
3. Restart notification-worker.
4. Restart web-client.
5. Restart graphql-gateway.
```

## Warning

```text id="jp2v0s"
migrate-docker.ps1 menggunakan migrate:fresh.
Artinya seluruh data lama pada database akan dihapus dan dibuat ulang.
Gunakan script ini ketika ingin reset data demo dari awal.
```

## Kapan Digunakan

Gunakan script ini ketika:

```text id="7c975t"
1. Pertama kali menjalankan project.
2. Data demo ingin dibuat ulang.
3. Seeder terbaru ingin diterapkan.
4. Database antar service perlu diselaraskan ulang.
5. Akan melakukan demo dari kondisi data yang bersih.
```

---

# 4. Script stop-docker.ps1

## Fungsi

Script ini digunakan untuk menghentikan Docker Mode.

Command:

```powershell id="pvzhaf"
.\scripts\stop-docker.ps1
```

## Proses yang Dilakukan

Script ini menjalankan:

```text id="58b3fr"
docker compose down --remove-orphans
```

## Efek Script

Service yang dihentikan:

```text id="4e8na4"
auth-service
member-service
field-service
booking-service
notification-service
notification-worker
web-client
graphql-gateway
redis
hasura
hasura-db
database container service
```

## Kapan Digunakan

Gunakan script ini ketika:

```text id="5icd5y"
1. Selesai melakukan demo/testing.
2. Ingin menghentikan seluruh container project.
3. Ingin berpindah dari Docker Mode ke Local/XAMPP Mode.
```

---

# 5. Script use-local.ps1

## Fungsi

Script ini digunakan untuk mengaktifkan Local/XAMPP Mode.

Command:

```powershell id="8c6xpp"
.\scripts\use-local.ps1
```

## Proses yang Dilakukan

Script ini menjalankan proses:

```text id="9wfhmb"
1. Mengecek Docker Desktop.
2. Mengatur environment service ke mode Local/XAMPP.
3. Menggunakan file .env.xampp sebagai .env aktif.
4. Menghentikan stack Docker penuh.
5. Menjalankan container pendukung Redis, Hasura DB, dan Hasura.
6. Membersihkan cache Laravel secara lokal.
```

## Container yang Tetap Berjalan

Pada Local/XAMPP Mode, container yang tetap digunakan:

```text id="5ess8v"
redis
hasura-db
hasura
```

Service Laravel tidak dijalankan sebagai container, tetapi dijalankan langsung menggunakan `php artisan serve`.

## Catatan

```text id="t7zj1a"
XAMPP MySQL harus aktif sebelum menjalankan migration local.
Pastikan database MySQL untuk setiap service sudah tersedia pada XAMPP.
```

Database yang dibutuhkan:

```text id="oydztp"
auth_db
member_db
field_db
booking_db
notification_db
```

---

# 6. Script migrate-local.ps1

## Fungsi

Script ini digunakan untuk menjalankan migration dan seeder pada Local/XAMPP Mode.

Command:

```powershell id="b7oif9"
.\scripts\migrate-local.ps1
```

## Proses yang Dilakukan

Script menjalankan migration dan seeder menggunakan PHP lokal.

Service yang diproses:

```text id="ekgeas"
auth-service
member-service
field-service
booking-service
notification-service
web-client
graphql-gateway
```

## Perintah Inti

Script menjalankan:

```text id="13vupm"
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

## Warning

```text id="ue8prr"
migrate-local.ps1 menggunakan migrate:fresh.
Seluruh data lama pada database local/XAMPP akan dihapus dan dibuat ulang.
```

## Kapan Digunakan

Gunakan script ini ketika:

```text id="y7mr7h"
1. Pertama kali menjalankan project di Local/XAMPP Mode.
2. Seeder terbaru ingin diterapkan.
3. Database local ingin direset.
4. Data demo local ingin disamakan ulang antar service.
```

---

# 7. Script start-local.ps1

## Fungsi

Script ini digunakan untuk menjalankan semua service Laravel secara lokal menggunakan `php artisan serve`.

Command:

```powershell id="0ktjs5"
.\scripts\start-local.ps1
```

## Proses yang Dilakukan

Script ini menjalankan:

```text id="h951p2"
1. Mengecek Docker Desktop.
2. Menjalankan container redis, hasura-db, dan hasura.
3. Mengecek koneksi Redis container.
4. Mengecek koneksi Redis pada host 127.0.0.1:6379.
5. Membuka Windows Terminal.
6. Menjalankan setiap service Laravel pada tab terminal berbeda.
7. Menjalankan notification-worker.
```

## Service yang Dijalankan

```text id="4t5q6x"
auth-service          -> php artisan serve --host=127.0.0.1 --port=8001
member-service        -> php artisan serve --host=127.0.0.1 --port=8002
field-service         -> php artisan serve --host=127.0.0.1 --port=8003
booking-service       -> php artisan serve --host=127.0.0.1 --port=8004
notification-service  -> php artisan serve --host=127.0.0.1 --port=8005
web-client            -> php artisan serve --host=127.0.0.1 --port=8090
graphql-gateway       -> php artisan serve --host=127.0.0.1 --port=8010
notification-worker   -> php artisan notifications:listen-redis
```

## URL Local Setelah Service Berjalan

```text id="sx2x0t"
Web Client      : http://127.0.0.1:8090
Auth Service    : http://127.0.0.1:8001
Member Service  : http://127.0.0.1:8002
Field Service   : http://127.0.0.1:8003
Booking Service : http://127.0.0.1:8004
Notification    : http://127.0.0.1:8005
GraphQL Gateway : http://127.0.0.1:8010
Hasura          : http://localhost:8080
```

## Catatan Redis

Script `start-local.ps1` memastikan Redis dapat diakses dari:

```text id="j0veua"
127.0.0.1:6379
```

Jika Redis belum memiliki port mapping yang benar, script akan mencoba memperbaiki container Redis agar Redis dapat digunakan oleh service Laravel lokal.

---

# 8. Script stop-local.ps1

## Fungsi

Script ini digunakan untuk menghentikan Local/XAMPP Mode.

Command:

```powershell id="7j0e9g"
.\scripts\stop-local.ps1
```

## Proses yang Dihentikan

Script menghentikan service Laravel yang berjalan pada port:

```text id="3rwpk4"
8001 auth-service
8002 member-service
8003 field-service
8004 booking-service
8005 notification-service
8090 web-client
8010 graphql-gateway
```

Script juga menghentikan:

```text id="x04sbb"
notification-worker
redis
hasura
hasura-db
```

## Menjaga Redis dan Hasura Tetap Aktif

Jika ingin menghentikan Laravel service tetapi tetap membiarkan Redis dan Hasura berjalan, gunakan:

```powershell id="b6qf6d"
.\scripts\stop-local.ps1 -KeepDocker
```

Dengan parameter `-KeepDocker`, container pendukung tidak dihentikan.

## Kapan Digunakan

Gunakan script ini ketika:

```text id="21fsli"
1. Selesai testing Local/XAMPP Mode.
2. Ingin menghentikan semua server php artisan serve.
3. Ingin membersihkan terminal service lokal.
4. Ingin berpindah kembali ke Docker Mode.
```

---

# 9. Alur Penggunaan Docker Mode

Gunakan alur berikut untuk menjalankan project penuh menggunakan Docker:

```powershell id="hsgs74"
.\scripts\use-docker.ps1
.\scripts\migrate-docker.ps1
```

Setelah selesai, buka:

```text id="db742e"
http://localhost:8090
```

Untuk menghentikan:

```powershell id="7ibclh"
.\scripts\stop-docker.ps1
```

## Alur Docker Mode untuk Demo

```text id="rbmdll"
1. Buka Docker Desktop.
2. Jalankan .\scripts\use-docker.ps1.
3. Jalankan .\scripts\migrate-docker.ps1.
4. Buka http://localhost:8090.
5. Login admin/member menggunakan akun demo.
6. Jalankan demo fitur.
7. Setelah selesai, jalankan .\scripts\stop-docker.ps1.
```

---

# 10. Alur Penggunaan Local/XAMPP Mode

Gunakan alur berikut untuk menjalankan project melalui PHP lokal:

```powershell id="d23xgt"
.\scripts\use-local.ps1
.\scripts\migrate-local.ps1
.\scripts\start-local.ps1
```

Setelah selesai, buka:

```text id="x0rwaf"
http://127.0.0.1:8090
```

Untuk menghentikan:

```powershell id="go90wi"
.\scripts\stop-local.ps1
```

## Alur Local/XAMPP Mode untuk Development

```text id="ux14ed"
1. Buka XAMPP.
2. Start MySQL.
3. Buka Docker Desktop.
4. Jalankan .\scripts\use-local.ps1.
5. Jalankan .\scripts\migrate-local.ps1.
6. Jalankan .\scripts\start-local.ps1.
7. Buka http://127.0.0.1:8090.
8. Lakukan development/testing.
9. Setelah selesai, jalankan .\scripts\stop-local.ps1.
```

---

# 11. Perbedaan Docker Mode dan Local/XAMPP Mode

| Aspek            | Docker Mode                 | Local/XAMPP Mode          |
| ---------------- | --------------------------- | ------------------------- |
| Laravel service  | Berjalan di container       | Berjalan di host Windows  |
| Database MySQL   | Container MySQL per service | XAMPP MySQL               |
| Redis            | Container Docker            | Container Docker          |
| Hasura           | Container Docker            | Container Docker          |
| Web Client       | Container Docker            | `php artisan serve`       |
| GraphQL Gateway  | Container Docker            | `php artisan serve`       |
| Cocok untuk      | Demo dan full stack testing | Development dan debugging |
| Script awal      | `use-docker.ps1`            | `use-local.ps1`           |
| Script migration | `migrate-docker.ps1`        | `migrate-local.ps1`       |
| Script stop      | `stop-docker.ps1`           | `stop-local.ps1`          |

---

# 12. Catatan Environment

## Docker Mode

Pada Docker Mode, service Laravel berkomunikasi menggunakan nama service Docker.

Contoh:

```text id="ubphpy"
auth-service
member-service
field-service
booking-service
notification-service
redis
```

Database menggunakan host container database.

## Local/XAMPP Mode

Pada Local/XAMPP Mode, service Laravel berkomunikasi menggunakan localhost atau `127.0.0.1`.

Contoh:

```text id="zgxmfu"
http://127.0.0.1:8001
http://127.0.0.1:8002
http://127.0.0.1:8003
http://127.0.0.1:8004
http://127.0.0.1:8005
http://127.0.0.1:8090
http://127.0.0.1:8010
```

Database menggunakan XAMPP MySQL.

---

# 13. Troubleshooting Script

## Docker Desktop Belum Berjalan

Masalah:

```text id="m31ykh"
Script gagal karena Docker tidak aktif.
```

Solusi:

```text id="dkk1at"
1. Buka Docker Desktop.
2. Tunggu sampai Docker status ready.
3. Jalankan ulang script.
```

## File .env.docker Tidak Ditemukan

Masalah:

```text id="ey8w81"
use-docker.ps1 membutuhkan file .env.docker tetapi file tidak tersedia.
```

Solusi:

```text id="mq452n"
1. Copy .env.example menjadi .env.docker.
2. Sesuaikan konfigurasi untuk Docker.
3. Jalankan ulang use-docker.ps1.
```

## File .env.xampp Tidak Ditemukan

Masalah:

```text id="xjkbab"
use-local.ps1 membutuhkan file .env.xampp tetapi file tidak tersedia.
```

Solusi:

```text id="rhgumd"
1. Copy .env.example menjadi .env.xampp.
2. Sesuaikan konfigurasi untuk XAMPP/local.
3. Jalankan ulang use-local.ps1.
```

## XAMPP MySQL Belum Aktif

Masalah:

```text id="da8pg5"
migrate-local.ps1 gagal karena database local tidak dapat diakses.
```

Solusi:

```text id="xmcz56"
1. Buka XAMPP Control Panel.
2. Start MySQL.
3. Pastikan database service sudah dibuat.
4. Jalankan ulang migrate-local.ps1.
```

## Windows Terminal Tidak Tersedia

Masalah:

```text id="94j5pe"
start-local.ps1 tidak dapat membuka tab service.
```

Solusi:

```text id="27e9ds"
1. Install Windows Terminal.
2. Pastikan command wt dapat dipanggil dari PowerShell.
3. Jalankan ulang start-local.ps1.
```

## Port Sudah Digunakan

Masalah:

```text id="0a67lc"
Service gagal start karena port sudah digunakan.
```

Port yang digunakan project:

```text id="eu3fqp"
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

Solusi:

```text id="6pn0wf"
1. Jalankan .\scripts\stop-local.ps1 jika menggunakan Local/XAMPP Mode.
2. Jalankan .\scripts\stop-docker.ps1 jika menggunakan Docker Mode.
3. Jalankan ulang mode yang diinginkan.
```

## Redis Tidak Terhubung Pada Local Mode

Masalah:

```text id="ejdm1p"
Service lokal tidak dapat mengakses Redis.
```

Solusi:

```text id="h1nzpl"
1. Jalankan .\scripts\use-local.ps1.
2. Jalankan .\scripts\start-local.ps1.
3. Pastikan Redis dapat diakses pada 127.0.0.1:6379.
```

## Data Tidak Sesuai Setelah Ganti Mode

Masalah:

```text id="9wr5hp"
Data Docker Mode dan Local/XAMPP Mode berbeda.
```

Penyebab:

```text id="8fjg77"
Docker Mode menggunakan database container.
Local/XAMPP Mode menggunakan database XAMPP.
```

Solusi:

```text id="5mq5dg"
1. Untuk Docker Mode, jalankan .\scripts\migrate-docker.ps1.
2. Untuk Local/XAMPP Mode, jalankan .\scripts\migrate-local.ps1.
```

---

# 14. Rekomendasi Penggunaan

## Untuk Demo Final

Gunakan Docker Mode:

```powershell id="9is2ni"
.\scripts\use-docker.ps1
.\scripts\migrate-docker.ps1
```

Buka:

```text id="u8sy1f"
http://localhost:8090
```

## Untuk Development

Gunakan Local/XAMPP Mode:

```powershell id="gofds3"
.\scripts\use-local.ps1
.\scripts\migrate-local.ps1
.\scripts\start-local.ps1
```

Buka:

```text id="kc2ccw"
http://127.0.0.1:8090
```

## Untuk Reset Data Demo Docker

```powershell id="ogtxdw"
.\scripts\migrate-docker.ps1
```

## Untuk Reset Data Demo Local/XAMPP

```powershell id="xjyqd0"
.\scripts\migrate-local.ps1
```

## Untuk Stop Semua Docker

```powershell id="75q6jd"
.\scripts\stop-docker.ps1
```

## Untuk Stop Local Service

```powershell id="cuaczp"
.\scripts\stop-local.ps1
```

---

# 15. Kesimpulan

Script PowerShell pada folder `scripts/` membantu menjalankan project Retobluto Arena Microservices secara lebih cepat dan konsisten.

Dengan script ini, proses yang sebelumnya harus dilakukan manual dapat dijalankan lebih mudah, seperti:

```text id="u3mo9v"
1. Berpindah antara Docker Mode dan Local/XAMPP Mode.
2. Menjalankan seluruh service.
3. Menjalankan migration dan seeder.
4. Membersihkan cache Laravel.
5. Menjalankan Redis dan Hasura.
6. Menjalankan notification worker.
7. Menghentikan service.
```

Script yang direkomendasikan untuk demo final adalah:

```powershell id="fyo0kv"
.\scripts\use-docker.ps1
.\scripts\migrate-docker.ps1
```
