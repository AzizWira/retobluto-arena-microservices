# Hasura Testing Guide

Dokumen ini berisi panduan testing Hasura Local pada project Retobluto Arena Microservices.

Hasura digunakan sebagai GraphQL Engine otomatis untuk kebutuhan reporting/read-only query. Hasura tidak menggantikan proses transaksi utama seperti login, register, OTP, booking, approve, reject, atau cancel. Semua proses transaksi utama tetap dilakukan oleh Laravel microservices.

## Endpoint Hasura

Hasura Console:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

## Fungsi Hasura dalam Project

Hasura digunakan untuk:

```text
1. Menyediakan GraphQL otomatis untuk data reporting.
2. Membaca data dari PostgreSQL hasura_db.
3. Menampilkan summary data field, member, booking, dan notification.
4. Menyediakan query reporting melalui table dan view.
5. Menjadi pembanding dengan GraphQL Gateway manual berbasis Laravel.
```

## Perbedaan Hasura dan GraphQL Gateway Manual

| Komponen               | Fungsi                                                                                     |
| ---------------------- | ------------------------------------------------------------------------------------------ |
| GraphQL Gateway Manual | GraphQL dibuat manual menggunakan Laravel dan meneruskan request ke REST API microservices |
| Hasura                 | GraphQL otomatis dari table/view PostgreSQL untuk reporting                                |

GraphQL Gateway manual digunakan untuk kebutuhan query dan mutation yang berhubungan dengan REST API service utama.

Hasura digunakan untuk kebutuhan reporting/read-only query.

## Database Hasura

Hasura menggunakan database PostgreSQL:

```text
hasura_db
```

Service Docker:

```text
hasura-db
```

Container:

```text
retobluto_hasura_db
```

Hasura tidak langsung membaca database MySQL service utama seperti `auth_db`, `member_db`, `field_db`, `booking_db`, atau `notification_db`.

## Alasan Hasura Menggunakan Database Terpisah

Project ini menggunakan prinsip database per service. Setiap service memiliki database sendiri sesuai domain masing-masing.

Struktur database utama:

```text
auth-service          -> auth_db
member-service        -> member_db
field-service         -> field_db
booking-service       -> booking_db
notification-service  -> notification_db
hasura                -> hasura_db
```

Hasura menggunakan database reporting terpisah agar tidak mengambil alih database transaksi utama. Dengan pendekatan ini, proses utama tetap berjalan melalui microservices, sedangkan Hasura digunakan untuk kebutuhan reporting.

## File Hasura Local

Struktur folder Hasura:

```text
hasura/
└── local/
    ├── README.md
    ├── setup-guide.md
    ├── schema/
    │   └── reporting-schema.sql
    └── queries/
        ├── booking-queries.md
        ├── field-member-queries.md
        ├── notification-dashboard-queries.md
        └── query-examples.md
```

## File SQL Reporting

Schema reporting Hasura berada pada:

```text
hasura/local/schema/reporting-schema.sql
```

File ini berisi:

```text
1. Drop table/view lama jika ada.
2. Pembuatan table reporting.
3. Pembuatan view reporting.
4. Insert data demo reporting.
```

## Table Reporting

Table reporting yang dibuat:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

## View Reporting

View reporting yang dibuat:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

---

# 1. Menjalankan Hasura

## Docker Mode

Jalankan:

```powershell
.\scripts\use-docker.ps1
```

Atau secara manual:

```powershell
docker compose up -d hasura-db hasura
```

## Local/XAMPP Mode

Pada Local/XAMPP Mode, Hasura tetap dijalankan melalui Docker.

Jalankan:

```powershell
.\scripts\use-local.ps1
```

Atau secara manual:

```powershell
docker compose up -d hasura-db hasura
```

## Cek Container Hasura

```powershell
docker compose ps
```

Pastikan container berikut berjalan:

```text
retobluto_hasura_db
retobluto_hasura
```

---

# 2. Membuka Hasura Console

Buka browser:

```text
http://localhost:8080
```

Jika diminta admin secret, isi:

```text
retobluto_admin_secret
```

Expected result:

```text
Hasura Console berhasil terbuka.
```

---

# 3. Menjalankan SQL Reporting

## Langkah

Pada Hasura Console, masuk ke:

```text
Data -> SQL
```

Copy seluruh isi file:

```text
hasura/local/schema/reporting-schema.sql
```

Paste ke SQL editor Hasura, lalu klik:

```text
Run
```

## Expected Result

Table dan view reporting berhasil dibuat pada database `hasura_db`.

Table yang muncul:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

View yang muncul:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

---

# 4. Track Table dan View

Setelah SQL berhasil dijalankan, masuk ke:

```text
Data -> public
```

Track table berikut:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

Track view berikut:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

Jika tersedia tombol `Track All`, tombol tersebut dapat digunakan.

## Expected Result

Table dan view berhasil di-track oleh Hasura dan dapat digunakan pada menu API.

---

# 5. Query Dashboard Summary

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

## Expected Result

Hasura menampilkan ringkasan data reporting, meliputi:

```text
1. Total lapangan.
2. Total member.
3. Total booking.
4. Total revenue dari booking approved.
5. Total notification log.
```

---

# 6. Query Field Report

Jalankan query:

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

## Expected Result

Hasura menampilkan data reporting lapangan.

Data yang ditampilkan meliputi:

```text
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
```

---

# 7. Query Field Available

Jalankan query:

```graphql
query AvailableFields {
  v_field_report(
    where: { status: { _eq: "available" } }
    order_by: { id: asc }
  ) {
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

Hasura menampilkan field dengan status:

```text
available
```

---

# 8. Query Field Maintenance

Jalankan query:

```graphql
query MaintenanceFields {
  v_field_report(
    where: { status: { _eq: "maintenance" } }
    order_by: { id: asc }
  ) {
    id
    source_field_id
    name
    type
    location
    status
  }
}
```

## Expected Result

Hasura menampilkan field dengan status:

```text
maintenance
```

---

# 9. Query Field Berdasarkan Tipe

Jalankan query:

```graphql
query FutsalFields {
  v_field_report(
    where: { type: { _eq: "Futsal" } }
    order_by: { price_per_hour: asc }
  ) {
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

Hasura menampilkan field berdasarkan tipe lapangan.

---

# 10. Query Member Report

Jalankan query:

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

## Expected Result

Hasura menampilkan data reporting member.

Data yang ditampilkan meliputi:

```text
id
source_member_id
source_user_id
name
email
phone
status
synced_at
```

---

# 11. Query Member Active

Jalankan query:

```graphql
query ActiveMembers {
  v_member_report(where: { status: { _eq: "active" } }, order_by: { id: asc }) {
    id
    source_member_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan member dengan status:

```text
active
```

---

# 12. Query Member Inactive

Jalankan query:

```graphql
query InactiveMembers {
  v_member_report(
    where: { status: { _eq: "inactive" } }
    order_by: { id: asc }
  ) {
    id
    source_member_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan member dengan status:

```text
inactive
```

---

# 13. Query Member Blocked

Jalankan query:

```graphql
query BlockedMembers {
  v_member_report(
    where: { status: { _eq: "blocked" } }
    order_by: { id: asc }
  ) {
    id
    source_member_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan member dengan status:

```text
blocked
```

---

# 14. Query Booking Report

Jalankan query:

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

## Expected Result

Hasura menampilkan data reporting booking.

Data yang ditampilkan meliputi:

```text
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
```

---

# 15. Query Booking Pending

Jalankan query:

```graphql
query PendingBookings {
  v_booking_report(
    where: { status: { _eq: "pending" } }
    order_by: { booking_date: asc }
  ) {
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

Hasura menampilkan booking dengan status:

```text
pending
```

Booking dengan status pending juga dapat disebut sebagai booking request.

---

# 16. Query Booking Approved

Jalankan query:

```graphql
query ApprovedBookings {
  v_booking_report(
    where: { status: { _eq: "approved" } }
    order_by: { booking_date: desc }
  ) {
    id
    source_booking_id
    member_name
    field_name
    booking_date
    start_time
    end_time
    total_price
    status
    approved_by
  }
}
```

## Expected Result

Hasura menampilkan booking dengan status:

```text
approved
```

---

# 17. Query Booking Rejected

Jalankan query:

```graphql
query RejectedBookings {
  report_bookings(
    where: { status: { _eq: "rejected" } }
    order_by: { booking_date: desc }
  ) {
    id
    source_booking_id
    member_name
    field_name
    booking_date
    start_time
    end_time
    total_price
    status
    rejection_reason
    rejected_by
    rejected_at
  }
}
```

## Expected Result

Hasura menampilkan booking dengan status:

```text
rejected
```

Data reject menampilkan alasan reject dan nama admin yang melakukan reject.

---

# 18. Query Booking Canceled

Jalankan query:

```graphql
query CanceledBookings {
  report_bookings(
    where: { status: { _eq: "canceled" } }
    order_by: { booking_date: desc }
  ) {
    id
    source_booking_id
    member_name
    field_name
    booking_date
    start_time
    end_time
    total_price
    status
    canceled_by
    canceled_at
  }
}
```

## Expected Result

Hasura menampilkan booking dengan status:

```text
canceled
```

---

# 19. Query Booking Berdasarkan Member

Jalankan query:

```graphql
query BookingByMember {
  v_booking_report(
    where: { member_email: { _eq: "wira123widodo@gmail.com" } }
    order_by: { booking_date: desc }
  ) {
    id
    source_booking_id
    member_name
    member_email
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

Hasura menampilkan booking berdasarkan email member tertentu.

---

# 20. Query Booking Berdasarkan Lapangan

Jalankan query:

```graphql
query BookingByField {
  v_booking_report(
    where: { field_name: { _ilike: "%Futsal%" } }
    order_by: { booking_date: desc }
  ) {
    id
    source_booking_id
    member_name
    field_name
    field_type
    booking_date
    start_time
    end_time
    total_price
    status
  }
}
```

## Expected Result

Hasura menampilkan booking berdasarkan nama lapangan atau keyword lapangan tertentu.

---

# 21. Query Approved Revenue

Jalankan query:

```graphql
query ApprovedRevenue {
  report_bookings(
    where: { status: { _eq: "approved" } }
    order_by: { booking_date: desc }
  ) {
    id
    source_booking_id
    member_name
    field_name
    booking_date
    total_price
    status
  }

  v_dashboard_summary {
    approved_revenue_total
  }
}
```

## Expected Result

Hasura menampilkan daftar booking approved dan total revenue dari booking approved.

---

# 22. Query Notification Report

Jalankan query:

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

## Expected Result

Hasura menampilkan data reporting notifikasi.

Data yang ditampilkan meliputi:

```text
source_log_id
recipient_email
type
subject
status
sent_at
synced_at
```

---

# 23. Query Notification Sent

Jalankan query:

```graphql
query SentNotifications {
  v_notification_report(
    where: { status: { _eq: "sent" } }
    order_by: { id: desc }
  ) {
    id
    source_log_id
    recipient_email
    type
    subject
    status
    sent_at
  }
}
```

## Expected Result

Hasura menampilkan notifikasi dengan status:

```text
sent
```

---

# 24. Query Notification Failed

Jalankan query:

```graphql
query FailedNotifications {
  report_notification_logs(
    where: { status: { _eq: "failed" } }
    order_by: { id: desc }
  ) {
    id
    source_log_id
    recipient_email
    type
    subject
    status
    error_message
    sent_at
  }
}
```

## Expected Result

Hasura menampilkan notifikasi dengan status:

```text
failed
```

Jika terdapat error, field `error_message` akan menampilkan informasi error.

---

# 25. Query Notification OTP

Jalankan query:

```graphql
query OtpNotifications {
  v_notification_report(
    where: { type: { _eq: "otp" } }
    order_by: { id: desc }
  ) {
    id
    source_log_id
    recipient_email
    type
    subject
    status
    sent_at
  }
}
```

## Expected Result

Hasura menampilkan log notifikasi OTP.

---

# 26. Query Notification Booking

Jalankan query:

```graphql
query BookingNotifications {
  v_notification_report(
    where: {
      type: {
        _in: ["booking_approved", "booking_rejected", "booking_canceled"]
      }
    }
    order_by: { id: desc }
  ) {
    id
    source_log_id
    recipient_email
    type
    subject
    status
    sent_at
  }
}
```

## Expected Result

Hasura menampilkan log notifikasi yang berhubungan dengan status booking.

---

# 27. Query Statistik Dashboard Ringkas

Jalankan query:

```graphql
query DashboardShort {
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

Hasura menampilkan summary ringkas untuk kebutuhan dashboard/reporting.

---

# 28. Perbedaan Data Hasura dan Data Web

Hasura menggunakan database reporting `hasura_db`.

Data pada Hasura berasal dari:

```text
hasura/local/schema/reporting-schema.sql
```

Data pada Web Client berasal dari service utama:

```text
auth_db
member_db
field_db
booking_db
notification_db
```

Karena itu, data Hasura tidak otomatis sama dengan data yang ada di Web Client jika tidak dilakukan proses sinkronisasi.

## Catatan

Dalam project ini, Hasura diposisikan sebagai reporting layer. Data reporting dapat diisi melalui SQL seed, import manual, atau proses sinkronisasi jika dikembangkan lebih lanjut.

---

# 29. Alasan Data Reporting Menggunakan SQL Terpisah

Data reporting dibuat melalui SQL khusus agar:

```text
1. Hasura tetap dapat berjalan secara lokal.
2. Hasura tidak perlu mengakses database MySQL service utama.
3. Prinsip database per service tetap terjaga.
4. Query reporting dapat dipisahkan dari proses transaksi utama.
5. Demo Hasura dapat dilakukan tanpa mengubah logic service utama.
```

---

# 30. Troubleshooting Hasura

## Hasura Console Tidak Bisa Dibuka

Pastikan container Hasura berjalan:

```powershell
docker compose ps
```

Pastikan container berikut aktif:

```text
retobluto_hasura
retobluto_hasura_db
```

Jika belum aktif, jalankan:

```powershell
docker compose up -d hasura-db hasura
```

## Admin Secret Salah

Gunakan admin secret:

```text
retobluto_admin_secret
```

## Table atau View Tidak Muncul

Solusi:

```text
1. Buka Data -> SQL.
2. Jalankan ulang hasura/local/schema/reporting-schema.sql.
3. Buka Data -> public.
4. Track table dan view secara manual.
```

## Query Tidak Bisa Dijalankan

Pastikan:

```text
1. Table sudah dibuat.
2. View sudah dibuat.
3. Table dan view sudah di-track.
4. Query menggunakan nama field yang sesuai.
```

## Data Hasura Tidak Sama dengan Data Web

Hal ini normal karena Hasura menggunakan database reporting terpisah.

Solusi:

```text
1. Update isi reporting-schema.sql.
2. Jalankan ulang SQL pada Hasura.
3. Track ulang table/view jika diperlukan.
4. Jika dikembangkan lebih lanjut, buat proses sinkronisasi reporting.
```

---

# 31. Kesimpulan Testing Hasura

Jika seluruh query berhasil dijalankan, maka Hasura sudah berjalan sebagai GraphQL Engine otomatis untuk reporting.

Hasura pada project ini membuktikan bahwa sistem memiliki:

```text
1. GraphQL otomatis menggunakan Hasura.
2. PostgreSQL reporting database.
3. Table reporting.
4. View reporting.
5. Query summary dashboard.
6. Query field report.
7. Query member report.
8. Query booking report.
9. Query notification report.
10. Dokumentasi setup dan query Hasura.
```
