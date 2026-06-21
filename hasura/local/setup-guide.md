# Hasura Local Setup Guide

Dokumen ini menjelaskan langkah setup Hasura Local pada project Retobluto Arena Microservices.

Hasura digunakan sebagai GraphQL Engine otomatis untuk kebutuhan reporting/read-only query. Hasura membaca data dari PostgreSQL `hasura_db`, bukan langsung dari database MySQL service utama.

## 1. Prasyarat

Pastikan sudah tersedia:

```text
Docker Desktop
Docker Compose
Browser
PowerShell
```

Pastikan Docker Desktop sudah berjalan sebelum menjalankan Hasura.

## 2. Service Hasura pada Docker Compose

Hasura menggunakan dua service utama:

```text
hasura-db
hasura
```

Keterangan:

| Service     | Fungsi                                          |
| ----------- | ----------------------------------------------- |
| `hasura-db` | PostgreSQL database untuk data reporting Hasura |
| `hasura`    | Hasura GraphQL Engine                           |

Database Hasura:

```text
Database : hasura_db
User     : hasura_user
Password : hasura_password
Port     : 5433
```

Hasura Console:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

## 3. Menjalankan Hasura

Jalankan dari root project:

```powershell
docker compose up -d hasura-db hasura
```

Atau jika ingin menjalankan semua service project dengan Docker Mode:

```powershell
.\scripts\use-docker.ps1
```

Jika menggunakan Local/XAMPP Mode, Hasura tetap berjalan melalui Docker:

```powershell
.\scripts\use-local.ps1
```

## 4. Cek Container Hasura

Jalankan:

```powershell
docker compose ps
```

Pastikan container berikut berjalan:

```text
retobluto_hasura_db
retobluto_hasura
```

Jika belum berjalan, jalankan ulang:

```powershell
docker compose up -d hasura-db hasura
```

## 5. Membuka Hasura Console

Buka browser:

```text
http://localhost:8080
```

Masukkan admin secret:

```text
retobluto_admin_secret
```

Jika berhasil, halaman Hasura Console akan terbuka.

## 6. Menjalankan SQL Reporting

File SQL reporting berada pada:

```text
hasura/local/schema/reporting-schema.sql
```

Langkah menjalankan SQL:

```text
1. Buka Hasura Console.
2. Masuk menu Data.
3. Pilih SQL.
4. Copy seluruh isi file reporting-schema.sql.
5. Paste ke SQL editor Hasura.
6. Klik Run.
```

File SQL tersebut akan membuat:

```text
1. Table reporting.
2. View reporting.
3. Data demo reporting.
```

## 7. Table yang Dibuat

Table reporting:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

Fungsi table:

| Table                      | Fungsi                              |
| -------------------------- | ----------------------------------- |
| `report_fields`            | Menyimpan data reporting lapangan   |
| `report_members`           | Menyimpan data reporting member     |
| `report_bookings`          | Menyimpan data reporting booking    |
| `report_notification_logs` | Menyimpan data reporting notifikasi |

## 8. View yang Dibuat

View reporting:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

Fungsi view:

| View                    | Fungsi                                                            |
| ----------------------- | ----------------------------------------------------------------- |
| `v_dashboard_summary`   | Ringkasan total field, member, booking, revenue, dan notification |
| `v_field_report`        | Laporan data lapangan                                             |
| `v_member_report`       | Laporan data member                                               |
| `v_booking_report`      | Laporan data booking                                              |
| `v_notification_report` | Laporan data notification log                                     |

## 9. Track Table dan View

Setelah SQL berhasil dijalankan, table dan view perlu di-track agar dapat diakses melalui GraphQL.

Langkah:

```text
1. Masuk menu Data.
2. Pilih schema public.
3. Track table yang muncul.
4. Track view yang muncul.
```

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

Jika tombol `Track All` tersedia, tombol tersebut dapat digunakan untuk mempercepat proses.

## 10. Testing Query Dashboard Summary

Masuk ke menu:

```text
API
```

Jalankan query:

```graphql
query DashboardSummary {
  v_dashboard_summary {
    fields_total
    fields_available
    fields_maintenance
    fields_inactive

    members_total
    members_active
    members_inactive
    members_blocked

    bookings_total
    bookings_pending
    bookings_approved
    bookings_rejected
    bookings_canceled

    approved_revenue_total

    notifications_total
    notifications_sent
    notifications_failed
  }
}
```

Expected result:

```text
Hasura menampilkan ringkasan data reporting field, member, booking, revenue, dan notification.
```

## 11. Testing Query Field Report

```graphql
query FieldReport {
  v_field_report(order_by: { id: asc }) {
    id
    source_field_id
    name
    type
    location
    price_per_hour
    status
    open_time
    close_time
    synced_at
  }
}
```

Expected result:

```text
Hasura menampilkan data reporting lapangan.
```

## 12. Testing Query Member Report

```graphql
query MemberReport {
  v_member_report(order_by: { id: asc }) {
    id
    source_member_id
    source_user_id
    name
    email
    phone
    status
    synced_at
  }
}
```

Expected result:

```text
Hasura menampilkan data reporting member.
```

## 13. Testing Query Booking Report

```graphql
query BookingReport {
  v_booking_report(order_by: { booking_date: desc }) {
    id
    source_booking_id
    member_name
    member_email
    field_name
    field_type
    booking_date
    start_time
    end_time
    duration_hours
    total_price
    status
    approved_by
    rejected_by
    canceled_by
    synced_at
  }
}
```

Expected result:

```text
Hasura menampilkan data reporting booking.
```

## 14. Testing Query Notification Report

```graphql
query NotificationReport {
  v_notification_report(order_by: { id: desc }) {
    id
    source_log_id
    recipient_email
    type
    subject
    status
    sent_at
    synced_at
  }
}
```

Expected result:

```text
Hasura menampilkan data reporting notification log.
```

## 15. Testing Filter Data

Contoh filter field available:

```graphql
query AvailableFields {
  v_field_report(
    where: { status: { _eq: "available" } }
    order_by: { id: asc }
  ) {
    id
    name
    type
    location
    status
  }
}
```

Contoh filter member active:

```graphql
query ActiveMembers {
  v_member_report(where: { status: { _eq: "active" } }, order_by: { id: asc }) {
    id
    name
    email
    status
  }
}
```

Contoh filter booking approved:

```graphql
query ApprovedBookings {
  v_booking_report(
    where: { status: { _eq: "approved" } }
    order_by: { booking_date: desc }
  ) {
    id
    member_name
    field_name
    booking_date
    total_price
    status
  }
}
```

Contoh filter notification sent:

```graphql
query SentNotifications {
  v_notification_report(
    where: { status: { _eq: "sent" } }
    order_by: { id: desc }
  ) {
    id
    recipient_email
    type
    subject
    status
    sent_at
  }
}
```

## 16. File Query Tambahan

Contoh query Hasura juga tersedia pada folder:

```text
hasura/local/queries/
```

Daftar file:

```text
booking-queries.md
field-member-queries.md
notification-dashboard-queries.md
query-examples.md
```

Gunakan file tersebut sebagai referensi query saat demo atau testing.

## 17. Perbedaan Data Hasura dan Data Web Client

Hasura menggunakan database reporting:

```text
hasura_db
```

Web Client menggunakan data dari service utama:

```text
auth_db
member_db
field_db
booking_db
notification_db
```

Karena database Hasura terpisah, data pada Hasura tidak otomatis berubah saat data di Web Client berubah.

Jika ingin memperbarui data Hasura, jalankan ulang atau sesuaikan file:

```text
hasura/local/schema/reporting-schema.sql
```

Lalu jalankan ulang SQL di Hasura Console.

## 18. Kenapa Hasura Tidak Langsung Membaca Database Service?

Project ini menggunakan prinsip database per service. Setiap service memiliki database masing-masing dan tidak boleh langsung mengambil data dari database service lain.

Hasura menggunakan database reporting terpisah agar:

```text
1. Database utama service tetap terpisah.
2. Hasura tidak mengganggu transaksi utama.
3. Reporting dapat dibuat secara read-only.
4. Query reporting dapat disusun dari table dan view khusus.
5. Arsitektur microservices tetap konsisten.
```

## 19. Reset Data Hasura

Untuk reset data Hasura:

```text
1. Buka Hasura Console.
2. Masuk Data -> SQL.
3. Jalankan ulang isi reporting-schema.sql.
4. Track ulang table dan view jika diperlukan.
```

File `reporting-schema.sql` sudah berisi perintah drop table/view sehingga data reporting lama akan diganti dengan data baru dari SQL tersebut.

## 20. Troubleshooting

### Hasura Console Tidak Bisa Dibuka

Cek container:

```powershell
docker compose ps
```

Jika `hasura` dan `hasura-db` belum berjalan:

```powershell
docker compose up -d hasura-db hasura
```

### Admin Secret Salah

Gunakan:

```text
retobluto_admin_secret
```

### Table Tidak Muncul

Solusi:

```text
1. Pastikan SQL reporting sudah dijalankan.
2. Buka Data -> public.
3. Track table secara manual.
4. Refresh halaman Hasura Console.
```

### View Tidak Muncul

Solusi:

```text
1. Pastikan reporting-schema.sql berhasil dijalankan tanpa error.
2. Buka Data -> public.
3. Track view secara manual.
4. Jika belum muncul, jalankan ulang SQL.
```

### Query Error Karena Field Tidak Ada

Solusi:

```text
1. Pastikan query memakai nama field sesuai table/view.
2. Cek struktur table/view pada menu Data.
3. Gunakan contoh query dari folder hasura/local/queries.
```

### Data Tidak Sama dengan Web Client

Hal ini normal karena Hasura menggunakan database reporting terpisah.

Solusi:

```text
1. Update reporting-schema.sql.
2. Jalankan ulang SQL di Hasura.
3. Track ulang table/view jika diperlukan.
```

## 21. Kesimpulan Setup

Hasura Local berhasil disiapkan jika:

```text
1. Container hasura-db berjalan.
2. Container hasura berjalan.
3. Hasura Console dapat dibuka di http://localhost:8080.
4. Admin secret berhasil digunakan.
5. reporting-schema.sql berhasil dijalankan.
6. Table reporting berhasil dibuat.
7. View reporting berhasil dibuat.
8. Table dan view berhasil di-track.
9. Query pada menu API berhasil dijalankan.
```

Dengan setup ini, Hasura dapat digunakan sebagai GraphQL Engine otomatis untuk kebutuhan reporting pada project Retobluto Arena Microservices.
