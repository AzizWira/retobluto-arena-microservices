# Final Testing Scenario

Dokumen ini berisi skenario pengujian final untuk project Retobluto Arena Microservices. Pengujian dilakukan untuk memastikan seluruh fitur utama berjalan sesuai flow sistem, baik melalui Web Client, REST API service, GraphQL Gateway, Hasura, Redis, Notification Worker, maupun data seeder demo.

## Tujuan Pengujian

Tujuan pengujian final adalah memastikan bahwa:

```text
1. Semua service berjalan dengan baik.
2. Web Client dapat digunakan oleh admin dan member.
3. Auth, member, field, booking, dan notification service saling terhubung.
4. OTP email berjalan.
5. Booking flow berjalan dari member sampai admin.
6. Validasi booking berjalan.
7. Notification log tercatat.
8. Email template HTML berhasil digunakan.
9. Rekomendasi pribadi dan lapangan terpopuler tampil pada dashboard member.
10. GraphQL Gateway manual berjalan.
11. Hasura reporting berjalan.
12. Seeder demo antar service saling terhubung.
```

## Persiapan Pengujian

Pastikan project sudah berjalan.

Jika menggunakan Docker Mode:

```powershell
.\scripts\use-docker.ps1
.\scripts\migrate-docker.ps1
```

Jika menggunakan Local/XAMPP Mode:

```powershell
.\scripts\use-local.ps1
.\scripts\migrate-local.ps1
.\scripts\start-local.ps1
```

Cek container:

```powershell
docker compose ps
```

Service penting yang harus aktif pada Docker Mode:

```text
retobluto_auth_service
retobluto_member_service
retobluto_field_service
retobluto_booking_service
retobluto_notification_service
retobluto_notification_worker
retobluto_web_client
retobluto_graphql_gateway
retobluto_redis
retobluto_hasura
retobluto_hasura_db
```

## Akun Demo

### Admin

```text
Email    : admin@retobluto.test
Password : password
```

### Member Active

```text
Email    : wira123widodo@gmail.com
Password : password
Status   : active
```

```text
Email    : auraiftitahh@gmail.com
Password : password
Status   : active
```

```text
Email    : muhammadagilhidayahtullah295@gmail.com
Password : password
Status   : active
```

```text
Email    : ryanalfin6@gmail.com
Password : password
Status   : active
```

### Member Inactive

```text
Email    : nabila.member@example.com
Password : password
Status   : inactive
```

### Member Blocked

```text
Email    : dimas.member@example.com
Password : password
Status   : blocked
```

## Akses Utama

Docker Mode:

```text
Web Client       : http://localhost:8090
Admin Login      : http://localhost:8090/login/master
Member Login     : http://localhost:8090/login
Member Register  : http://localhost:8090/register
GraphQL Gateway  : http://localhost:8010/playground
Hasura Console   : http://localhost:8080
```

Local/XAMPP Mode:

```text
Web Client       : http://127.0.0.1:8090
Admin Login      : http://127.0.0.1:8090/login/master
Member Login     : http://127.0.0.1:8090/login
Member Register  : http://127.0.0.1:8090/register
GraphQL Gateway  : http://127.0.0.1:8010/playground
Hasura Console   : http://localhost:8080
```

---

# Test 1 — Health Check Service

## Langkah

Buka endpoint health masing-masing service:

```text
http://localhost:8001/api/health
http://localhost:8002/api/health
http://localhost:8003/api/health
http://localhost:8004/api/health
http://localhost:8005/api/health
http://localhost:8010/api/health
```

## Expected Result

```text
Setiap service menampilkan response health check berhasil.
```

---

# Test 2 — Admin Login

## Langkah

```text
1. Buka halaman http://localhost:8090/login/master.
2. Masukkan email admin@retobluto.test.
3. Masukkan password password.
4. Klik login.
```

## Expected Result

```text
Admin berhasil login dan diarahkan ke dashboard admin.
```

---

# Test 3 — Dashboard Admin

## Langkah

```text
1. Login sebagai admin.
2. Buka dashboard admin.
3. Periksa ringkasan data member, field, booking, dan notification.
```

## Expected Result

```text
Dashboard admin tampil dan menampilkan data dari service terkait.
```

---

# Test 4 — Admin Melihat Data Lapangan Seeder

## Langkah

```text
1. Login sebagai admin.
2. Masuk ke menu Fields/Lapangan.
3. Periksa daftar lapangan hasil seeder.
```

## Expected Result

```text
Data lapangan dari Field Service tampil pada halaman admin.
```

---

# Test 5 — Admin Tambah Lapangan

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Fields/Lapangan.
3. Klik tambah lapangan.
4. Isi nama lapangan, tipe, lokasi, harga per jam, status, open_time, dan close_time.
5. Simpan data.
```

## Expected Result

```text
Lapangan berhasil ditambahkan dan muncul pada daftar lapangan.
```

---

# Test 6 — Admin Update Status Lapangan

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Fields/Lapangan.
3. Pilih salah satu lapangan.
4. Ubah status menjadi available, maintenance, atau inactive.
5. Simpan perubahan.
```

## Expected Result

```text
Status lapangan berhasil berubah sesuai input admin.
```

---

# Test 7 — Admin Melihat Data Member Seeder

## Langkah

```text
1. Login sebagai admin.
2. Masuk ke menu Members.
3. Periksa data member hasil seeder.
```

## Expected Result

```text
Data member dari Member Service tampil dan saling terhubung dengan akun Auth Service.
```

---

# Test 8 — Admin Tambah Member

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Members.
3. Klik tambah member.
4. Isi nama, email, password, phone, dan address.
5. Simpan data.
```

## Expected Result

```text
Member berhasil dibuat.
Data akun dibuat pada Auth Service.
Data profil dibuat pada Member Service.
```

---

# Test 9 — Admin Update Status Member

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Members.
3. Pilih salah satu member.
4. Ubah status menjadi active, inactive, atau blocked.
5. Simpan perubahan.
```

## Expected Result

```text
Status member berhasil diperbarui.
Status tersebut digunakan oleh Booking Service saat validasi booking.
```

---

# Test 10 — Member Register Request OTP

## Langkah

```text
1. Buka halaman http://localhost:8090/register.
2. Isi nama, email, password, dan data register yang dibutuhkan.
3. Submit register.
```

## Expected Result

```text
Sistem membuat request OTP.
Email OTP dikirim menggunakan template HTML.
Member diarahkan ke halaman verifikasi OTP.
Notification log tercatat pada Notification Service.
```

---

# Test 11 — Resend OTP

## Langkah

```text
1. Setelah request OTP, buka halaman verifikasi OTP.
2. Klik resend OTP.
3. Cek email terbaru yang masuk.
```

## Expected Result

```text
OTP baru berhasil dikirim.
Email OTP menggunakan template HTML.
Log OTP baru tercatat pada Notification Service.
```

---

# Test 12 — Member Verifikasi OTP

## Langkah

```text
1. Buka halaman verifikasi OTP.
2. Masukkan email member.
3. Masukkan kode OTP yang diterima.
4. Submit verifikasi.
```

## Expected Result

```text
Member berhasil diverifikasi.
Status akun Auth Service menjadi verified.
Data member tersinkron dengan Member Service.
Member dapat login.
```

---

# Test 13 — Member Login Active

## Langkah

```text
1. Buka halaman http://localhost:8090/login.
2. Login menggunakan akun member active, misalnya wira123widodo@gmail.com.
3. Masukkan password password.
```

## Expected Result

```text
Member berhasil login dan diarahkan ke dashboard member.
```

---

# Test 14 — Member Inactive Tidak Bisa Booking

## Langkah

```text
1. Login menggunakan akun nabila.member@example.com.
2. Masuk ke halaman booking.
3. Coba membuat booking.
```

## Expected Result

```text
Sistem menolak booking karena status member inactive.
```

---

# Test 15 — Member Blocked Tidak Bisa Booking

## Langkah

```text
1. Login menggunakan akun dimas.member@example.com.
2. Masuk ke halaman booking.
3. Coba membuat booking.
```

## Expected Result

```text
Sistem menolak booking karena status member blocked.
```

---

# Test 16 — Member Melihat Daftar Lapangan

## Langkah

```text
1. Login sebagai member active.
2. Masuk menu Fields/Lapangan.
3. Buka salah satu detail lapangan.
```

## Expected Result

```text
Member dapat melihat daftar lapangan dan detail lapangan.
```

---

# Test 17 — Member Membuat Booking

## Langkah

```text
1. Login sebagai member active.
2. Masuk menu Bookings.
3. Klik buat booking.
4. Pilih lapangan available.
5. Pilih tanggal setelah hari pengujian.
6. Isi start_time dan end_time.
7. Submit booking.
```

## Expected Result

```text
Booking berhasil dibuat dengan status pending.
Booking tercatat di Booking Service.
Member dapat melihat booking tersebut pada daftar booking miliknya.
```

---

# Test 18 — Validasi Konflik Jadwal Booking

## Langkah

```text
1. Buat booking pertama pada lapangan, tanggal, dan jam tertentu.
2. Buat booking kedua pada lapangan, tanggal, dan jam yang sama atau bertabrakan.
```

## Expected Result

```text
Booking kedua ditolak karena jadwal bertabrakan.
```

---

# Test 19 — Validasi Satu Member Satu Booking Aktif Pada Tanggal Sama

## Langkah

```text
1. Login sebagai member active.
2. Buat booking pada tanggal tertentu.
3. Buat booking lain pada tanggal yang sama ketika booking pertama masih aktif.
```

## Expected Result

```text
Sistem menolak booking kedua jika member sudah memiliki booking aktif pada tanggal yang sama.
```

---

# Test 20 — Admin Melihat Booking Request

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Booking Requests.
3. Periksa booking dengan status pending.
```

## Expected Result

```text
Booking yang dibuat member tampil sebagai booking request dengan status pending.
```

---

# Test 21 — Admin Approve Booking

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Booking Requests.
3. Pilih booking pending.
4. Klik approve.
```

## Expected Result

```text
Status booking berubah menjadi approved.
Nama admin tercatat pada approved_by.
Notification Service mengirim email status booking.
Notification log tercatat.
```

---

# Test 22 — Admin Reject Booking

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Booking Requests.
3. Pilih booking pending.
4. Isi alasan reject.
5. Klik reject.
```

## Expected Result

```text
Status booking berubah menjadi rejected.
Alasan reject tersimpan.
Nama admin tercatat pada rejected_by.
Notification Service mengirim email status booking.
Notification log tercatat.
```

---

# Test 23 — Member Cancel Booking

## Langkah

```text
1. Login sebagai member active.
2. Masuk menu Bookings.
3. Pilih booking yang dapat dibatalkan.
4. Klik cancel.
```

## Expected Result

```text
Status booking berubah menjadi canceled.
Nama member tercatat pada canceled_by.
Notification log tercatat jika event notifikasi diproses.
```

---

# Test 24 — Admin Melihat Semua Booking

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Bookings.
3. Periksa daftar booking dengan status pending, approved, rejected, dan canceled.
```

## Expected Result

```text
Admin dapat melihat seluruh booking dari semua member.
```

---

# Test 25 — Notification Log

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Notifications.
3. Buka daftar log notifikasi.
4. Buka detail salah satu log.
```

## Expected Result

```text
Log OTP, email manual, dan status booking tampil.
Detail log notifikasi dapat dibuka.
```

---

# Test 26 — Email OTP Template HTML

## Langkah

```text
1. Register member baru.
2. Request OTP.
3. Cek email OTP yang diterima.
```

## Expected Result

```text
Email OTP tampil menggunakan template HTML yang rapi.
Email menampilkan brand ARENALO/Retobluto Arena.
Email menampilkan kode OTP dengan jelas.
```

---

# Test 27 — Email Manual Admin Template HTML

## Langkah

```text
1. Login sebagai admin.
2. Masuk menu Notifications.
3. Buka halaman kirim email.
4. Isi email tujuan member, subject, dan message.
5. Kirim email.
6. Cek email masuk.
```

## Expected Result

```text
Email manual berhasil dikirim.
UI menampilkan pesan sukses.
Email menggunakan template HTML.
Notification log tercatat.
```

---

# Test 28 — Timeout Email Manual

## Langkah

```text
1. Login sebagai admin.
2. Kirim email manual ke member.
3. Tunggu sampai proses selesai.
```

## Expected Result

```text
Jika email berhasil dikirim, UI menampilkan success.
UI tidak menampilkan pesan Notification Service tidak dapat dihubungi ketika email sebenarnya berhasil dikirim.
```

---

# Test 29 — Rekomendasi Pribadi Member

## Langkah

```text
1. Login sebagai member active yang memiliki riwayat booking dari seeder.
2. Buka dashboard member.
3. Periksa bagian Rekomendasi Pribadi.
```

## Expected Result

```text
Dashboard member menampilkan rekomendasi berdasarkan riwayat booking member tersebut.
Jika lapangan pernah dibooking, muncul badge Pernah kamu booking.
Jika tipe lapangan sesuai riwayat booking member, muncul badge Sesuai tipe favorit kamu.
```

---

# Test 30 — Lapangan Terpopuler

## Langkah

```text
1. Login sebagai member active.
2. Buka dashboard member.
3. Periksa bagian Lapangan Terpopuler.
```

## Expected Result

```text
Dashboard member menampilkan lapangan yang paling banyak memiliki booking approved secara global.
Data diambil dari Booking Service endpoint /api/bookings/popular-fields.
Lapangan yang ditampilkan tetap harus berstatus available.
```

---

# Test 31 — GraphQL Gateway Health

## Langkah

Buka:

```text
http://localhost:8010/playground
```

Jalankan query:

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

## Expected Result

```text
GraphQL Gateway berhasil menampilkan status health dari service yang tersedia.
```

---

# Test 32 — GraphQL Gateway Query Fields

## Langkah

Buka:

```text
http://localhost:8010/playground
```

Jalankan query:

```graphql
query {
  fields {
    id
    name
    type
    location
    price_per_hour
    status
  }
}
```

## Expected Result

```text
GraphQL Gateway berhasil mengambil data field dari Field Service.
```

---

# Test 33 — GraphQL Gateway Booking Mutation

## Langkah

```text
1. Login member untuk mendapatkan token.
2. Masukkan token pada playground.
3. Jalankan mutation createBooking.
4. Gunakan booking_date setelah hari pengujian.
```

Contoh mutation:

```graphql
mutation {
  createBooking(
    field_id: 1
    booking_date: "2026-06-30"
    start_time: "09:00"
    end_time: "11:00"
    note: "Booking dari GraphQL Gateway"
  ) {
    success
    message
    booking {
      id
      member_name
      field_name
      booking_date
      start_time
      end_time
      status
      total_price
    }
    errors
  }
}
```

## Expected Result

```text
Booking berhasil dibuat melalui GraphQL Gateway jika data valid dan tidak melanggar aturan booking.
```

---

# Test 34 — GraphQL Endpoint POST

## Langkah

Buka langsung endpoint berikut di browser:

```text
http://localhost:8010/api/graphql
```

## Expected Result

```text
Jika dibuka langsung di browser, muncul informasi bahwa GET tidak didukung.
Hal ini normal karena /api/graphql hanya menerima POST.
Testing melalui browser dilakukan dari /playground.
```

---

# Test 35 — Hasura Setup Reporting

## Langkah

```text
1. Buka http://localhost:8080.
2. Masukkan admin secret retobluto_admin_secret.
3. Masuk Data -> SQL.
4. Jalankan isi file hasura/local/schema/reporting-schema.sql.
5. Track table dan view reporting.
```

## Expected Result

```text
Tabel report_fields, report_members, report_bookings, dan report_notification_logs berhasil dibuat.
View v_dashboard_summary, v_field_report, v_member_report, v_booking_report, dan v_notification_report berhasil dibuat dan dapat di-track.
```

---

# Test 36 — Hasura Dashboard Summary

## Langkah

Buka Hasura Console:

```text
http://localhost:8080
```

Masuk menu API, lalu jalankan:

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

## Expected Result

```text
Hasura menampilkan summary reporting dari database hasura_db.
```

---

# Test 37 — Hasura Field Report

## Langkah

Jalankan query:

```graphql
query {
  v_field_report(order_by: { id: asc }) {
    id
    source_field_id
    name
    type
    location
    price_per_hour
    status
  }
}
```

## Expected Result

```text
Hasura menampilkan data reporting lapangan.
```

---

# Test 38 — Hasura Booking Report

## Langkah

Jalankan query:

```graphql
query {
  v_booking_report(order_by: { booking_date: desc }) {
    id
    source_booking_id
    member_name
    field_name
    booking_date
    start_time
    end_time
    total_price
    status
  }
}
```

## Expected Result

```text
Hasura menampilkan data reporting booking.
```

---

# Test 39 — Script Docker Mode

## Langkah

Jalankan:

```powershell
.\scripts\use-docker.ps1
```

## Expected Result

```text
Script menjalankan Docker Mode dan menampilkan endpoint utama project.
```

---

# Test 40 — Script Migration Docker

## Langkah

Jalankan:

```powershell
.\scripts\migrate-docker.ps1
```

## Expected Result

```text
Script menjalankan migrate:fresh dan seeder pada service yang dibutuhkan.
Data demo kembali tersedia setelah script selesai.
```

---

# Kesimpulan Pengujian

Jika seluruh skenario pengujian di atas berhasil, maka sistem telah memenuhi kebutuhan utama:

```text
1. Microservices berjalan.
2. RESTful API berjalan.
3. Web Client berjalan.
4. Admin flow berjalan.
5. Member flow berjalan.
6. OTP email berjalan.
7. Template email HTML berjalan.
8. Booking flow berjalan.
9. Validasi booking berjalan.
10. Notification log berjalan.
11. Seeder demo saling terhubung.
12. Rekomendasi pribadi berjalan.
13. Lapangan terpopuler berjalan.
14. Redis/message broker berjalan.
15. GraphQL Gateway manual berjalan.
16. Hasura reporting berjalan.
17. Script Docker/Local tersedia.
```
