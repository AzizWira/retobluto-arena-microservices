# Hasura Local Reporting Integration

Folder ini berisi dokumentasi dan SQL schema untuk integrasi Hasura lokal pada project Retobluto Arena Microservices.

Hasura digunakan sebagai GraphQL Engine tambahan untuk kebutuhan reporting atau read-only query. Proses utama seperti login, OTP, booking, approve, reject, dan cancel tetap diproses melalui Laravel microservices dan GraphQL Gateway manual.

## Endpoint Hasura Local

```text
http://localhost:8080
```

## Admin Secret

```text
retobluto_admin_secret
```

## Database Hasura

Hasura local menggunakan PostgreSQL service:

```text
hasura-db
```

Konfigurasi database pada `docker-compose.yml`:

```text
POSTGRES_DB=hasura_db
POSTGRES_USER=hasura_user
POSTGRES_PASSWORD=hasura_password
```

## Fungsi Hasura dalam Project

Hasura digunakan untuk:

- Reporting data lapangan
- Reporting data member
- Reporting data booking
- Reporting log notifikasi
- Dashboard summary

Hasura tidak digunakan untuk menggantikan REST API utama dan tidak digunakan untuk proses transaksi utama sistem.

## File Penting

```text
reporting-schema.sql                 -> SQL schema untuk tabel dan view reporting
setup-guide.md                       -> panduan setup Hasura local
field-member-queries.md              -> contoh query field dan member
booking-queries.md                   -> contoh query booking
notification-dashboard-queries.md    -> contoh query notification dan dashboard
query-examples.md                    -> kumpulan query utama Hasura
```

## Alasan Menggunakan Hasura Local

Hasura dijalankan secara lokal menggunakan Docker Compose agar seluruh service, database, dan GraphQL engine berada dalam satu environment yang sama. Pendekatan ini dipilih supaya sistem lebih mudah direplikasi, diuji, dan didemokan tanpa bergantung pada database public atau koneksi cloud.
