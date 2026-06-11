# Final Testing Scenario

Dokumen ini berisi skenario testing final Retobluto Arena Microservices.

## Persiapan Testing

Pastikan semua container berjalan:

```bash
docker compose ps
```

Pastikan service berikut aktif:

```text
retobluto_auth_service
retobluto_member_service
retobluto_field_service
retobluto_booking_service
retobluto_notification_service
retobluto_notification_worker
retobluto_web_client
retobluto_graphql_gateway
retobluto_hasura
retobluto_redis
```

Akses web:

```text
http://localhost:8090
```

Akun admin:

```text
Email    : admin@retobluto.test
Password : password
```

## Test 1 — Admin Login

Langkah:

```text
1. Buka http://localhost:8090/login/master
2. Masukkan email admin@retobluto.test
3. Masukkan password password
4. Klik login
```

Expected result:

```text
Admin berhasil masuk ke dashboard admin.
```

## Test 2 — Admin Tambah Lapangan

Langkah:

```text
1. Login sebagai admin
2. Masuk menu Fields/Lapangan
3. Klik tambah lapangan
4. Isi nama, tipe, lokasi, harga, status, open_time, close_time
5. Simpan
```

Expected result:

```text
Lapangan berhasil ditambahkan dan muncul pada list lapangan.
```

## Test 3 — Admin Update Status Lapangan

Langkah:

```text
1. Masuk ke list lapangan
2. Pilih salah satu lapangan
3. Ubah status menjadi available, maintenance, atau inactive
4. Simpan
```

Expected result:

```text
Status lapangan berhasil berubah.
```

## Test 4 — Admin Tambah Member

Langkah:

```text
1. Login sebagai admin
2. Masuk menu Members
3. Klik tambah member
4. Isi nama, email, password, phone, dan address
5. Simpan
```

Expected result:

```text
Member berhasil dibuat di Auth Service dan Member Service.
```

Catatan:

```text
Member yang dibuat admin masih perlu verifikasi OTP apabila login sebagai member sebelum aktif.
```

## Test 5 — Member Register Request OTP

Langkah:

```text
1. Buka http://localhost:8090/register
2. Isi nama, email, dan password
3. Submit register
```

Expected result:

```text
OTP dikirim ke email member melalui Notification Service.
Member diarahkan ke halaman verifikasi OTP.
```

## Test 6 — Member Resend OTP

Langkah:

```text
1. Pada halaman verifikasi OTP, klik resend OTP
2. Tunggu response sistem
```

Expected result:

```text
OTP baru berhasil dikirim.
Log OTP tercatat di Notification Service.
```

## Test 7 — Member Verifikasi OTP

Langkah:

```text
1. Masukkan email member
2. Masukkan kode OTP yang diterima
3. Submit verifikasi
```

Expected result:

```text
Member berhasil diverifikasi.
Member dapat login atau langsung masuk ke halaman member.
```

## Test 8 — Member Login

Langkah:

```text
1. Buka http://localhost:8090/login
2. Masukkan email dan password member
3. Klik login
```

Expected result:

```text
Member berhasil masuk ke dashboard member.
```

## Test 9 — Member Melihat Lapangan

Langkah:

```text
1. Login sebagai member
2. Masuk menu Fields/Lapangan
3. Pilih salah satu lapangan
```

Expected result:

```text
Member dapat melihat list dan detail lapangan.
```

## Test 10 — Member Membuat Booking

Langkah:

```text
1. Login sebagai member aktif
2. Masuk menu Bookings
3. Klik buat booking
4. Pilih lapangan
5. Pilih tanggal
6. Isi jam mulai dan jam selesai
7. Submit booking
```

Expected result:

```text
Booking berhasil dibuat dengan status pending.
```

## Test 11 — Validasi Konflik Jadwal Booking

Langkah:

```text
1. Buat booking pertama pada lapangan dan jam tertentu
2. Buat booking kedua pada lapangan dan jam yang sama
```

Expected result:

```text
Booking kedua ditolak karena terjadi konflik jadwal.
```

## Test 12 — Validasi Member Inactive Tidak Bisa Booking

Langkah:

```text
1. Admin ubah status member menjadi inactive
2. Login sebagai member tersebut
3. Coba buat booking
```

Expected result:

```text
Sistem menolak booking karena member tidak aktif.
```

## Test 13 — Validasi Member Blocked Tidak Bisa Booking

Langkah:

```text
1. Admin ubah status member menjadi blocked
2. Login sebagai member tersebut
3. Coba buat booking
```

Expected result:

```text
Sistem menolak booking karena member diblokir.
```

## Test 14 — Admin Melihat Booking Request

Langkah:

```text
1. Login sebagai admin
2. Masuk menu Booking Requests
```

Expected result:

```text
Admin dapat melihat daftar booking dengan status pending.
```

## Test 15 — Admin Approve Booking

Langkah:

```text
1. Login sebagai admin
2. Masuk detail booking pending
3. Klik approve
```

Expected result:

```text
Status booking berubah menjadi approved.
Nama admin tercatat pada approved_by.
Member dapat melihat booking approved.
```

## Test 16 — Admin Reject Booking

Langkah:

```text
1. Login sebagai admin
2. Masuk detail booking pending
3. Isi alasan reject
4. Klik reject
```

Expected result:

```text
Status booking berubah menjadi rejected.
Alasan reject tersimpan.
Nama admin tercatat pada rejected_by.
```

## Test 17 — Member Cancel Booking

Langkah:

```text
1. Login sebagai member
2. Masuk menu Bookings
3. Pilih booking yang masih bisa dibatalkan
4. Klik cancel
```

Expected result:

```text
Status booking berubah menjadi canceled.
Nama member tercatat pada canceled_by.
```

## Test 18 — Notification Log

Langkah:

```text
1. Login sebagai admin
2. Masuk menu Notifications
3. Buka list log notifikasi
```

Expected result:

```text
Log OTP dan notifikasi booking tampil.
Detail log dapat dibuka.
```

## Test 19 — GraphQL Gateway Health

Endpoint:

```text
http://localhost:8010/playground
```

Query:

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

Expected result:

```text
Semua service mengembalikan ok true.
```

## Test 20 — Hasura Reporting

Endpoint:

```text
http://localhost:8080
```

Query:

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

Expected result:

```text
Hasura menampilkan data summary reporting.
```

## Kesimpulan Testing

Jika seluruh skenario berhasil dijalankan, maka sistem sudah memenuhi kebutuhan utama:

```text
- Microservices berjalan
- REST API berjalan
- Web client berjalan
- OTP berjalan
- Redis/message broker berjalan
- Booking flow berjalan
- Notification log berjalan
- GraphQL Gateway manual berjalan
- Hasura reporting berjalan
- Docker deployment berjalan
```
