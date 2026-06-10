# Setup Guide Hasura Local

Panduan ini menjelaskan cara menjalankan Hasura local pada project Retobluto Arena Microservices.

## 1. Jalankan Docker

Dari root project, jalankan:

```bash
docker compose up -d
```

Atau jika hanya ingin menjalankan Hasura dan database Hasura:

```bash
docker compose up -d hasura-db hasura
```

Cek container:

```bash
docker compose ps
```

Service yang perlu aktif:

```text
retobluto_hasura
retobluto_hasura_db
```

## 2. Buka Hasura Console

Buka browser:

```text
http://localhost:8080
```

Jika diminta admin secret, gunakan:

```text
retobluto_admin_secret
```

## 3. Jalankan SQL Reporting

Pada Hasura Console, masuk ke:

```text
Data -> SQL
```

Lalu copy seluruh isi file:

```text
hasura/local/reporting-schema.sql
```

Paste ke SQL editor Hasura, lalu klik:

```text
Run
```

## 4. Track Tables dan Views

Setelah SQL berhasil dijalankan, masuk ke:

```text
Data -> public
```

Track tabel berikut:

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

Jika tersedia tombol `Track All`, tombol tersebut dapat digunakan agar lebih cepat.

## 5. Test Query GraphQL

Masuk ke menu:

```text
API
```

Coba query:

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

Jika data angka berhasil tampil, maka Hasura local sudah berjalan dengan baik.

## Catatan

Hasura pada project ini digunakan sebagai reporting GraphQL Engine. Data reporting dapat diisi melalui SQL seed, import manual, atau proses sinkronisasi dari service apabila diperlukan pada pengembangan berikutnya.
