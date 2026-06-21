# Hasura Local Reporting Integration

Folder ini berisi dokumentasi, SQL schema, dan contoh query untuk integrasi Hasura Local pada project Retobluto Arena Microservices.

Hasura digunakan sebagai GraphQL Engine tambahan untuk kebutuhan reporting atau read-only query. Proses utama seperti login, register, OTP, booking, approve booking, reject booking, cancel booking, dan pengiriman notifikasi tetap diproses melalui Laravel microservices.

## Fungsi Hasura dalam Project

Hasura pada project ini digunakan untuk:

```text id="yru1bu"
1. Menyediakan GraphQL otomatis untuk kebutuhan reporting.
2. Menampilkan data ringkasan dashboard.
3. Menampilkan laporan data lapangan.
4. Menampilkan laporan data member.
5. Menampilkan laporan data booking.
6. Menampilkan laporan log notifikasi.
7. Menjadi pembanding dengan GraphQL Gateway manual berbasis Laravel.
```

Hasura tidak digunakan untuk menggantikan REST API utama dan tidak digunakan untuk proses transaksi utama sistem.

## Endpoint Hasura Local

Hasura Console:

```text id="zwedqu"
http://localhost:8080
```

Admin secret:

```text id="tjqkwj"
retobluto_admin_secret
```

## Database Hasura

Hasura Local menggunakan PostgreSQL service:

```text id="bjyi7k"
hasura-db
```

Database:

```text id="h7sd4e"
hasura_db
```

Konfigurasi database pada `docker-compose.yml`:

```text id="87p4lc"
POSTGRES_DB=hasura_db
POSTGRES_USER=hasura_user
POSTGRES_PASSWORD=hasura_password
```

## Posisi Hasura dalam Arsitektur

Project ini menggunakan prinsip database per service.

Database utama:

```text id="bz7w70"
auth-service          -> auth_db
member-service        -> member_db
field-service         -> field_db
booking-service       -> booking_db
notification-service  -> notification_db
hasura                -> hasura_db
```

Hasura menggunakan database terpisah bernama `hasura_db`. Database ini digunakan khusus untuk reporting, bukan untuk transaksi utama.

Dengan pemisahan ini, Hasura tidak langsung mengakses database MySQL milik service utama. Data reporting Hasura dapat diisi melalui SQL seed, import manual, atau proses sinkronisasi apabila dikembangkan lebih lanjut.

## Perbedaan Hasura dan GraphQL Gateway Manual

| Komponen               | Fungsi                                                                             |
| ---------------------- | ---------------------------------------------------------------------------------- |
| GraphQL Gateway Manual | Dibuat menggunakan Laravel dan meneruskan query/mutation ke REST API microservices |
| Hasura                 | GraphQL otomatis dari table dan view PostgreSQL untuk kebutuhan reporting          |

GraphQL Gateway manual digunakan untuk komunikasi dengan service utama, sedangkan Hasura digunakan untuk reporting/read-only query.

## Struktur Folder

```text id="qz2ch5"
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

## File Penting

| File                                        | Keterangan                                            |
| ------------------------------------------- | ----------------------------------------------------- |
| `README.md`                                 | Penjelasan umum Hasura Local pada project             |
| `setup-guide.md`                            | Panduan setup Hasura Local                            |
| `schema/reporting-schema.sql`               | SQL schema untuk table, view, dan data demo reporting |
| `queries/field-member-queries.md`           | Contoh query field dan member                         |
| `queries/booking-queries.md`                | Contoh query booking                                  |
| `queries/notification-dashboard-queries.md` | Contoh query notification dan dashboard               |
| `queries/query-examples.md`                 | Kumpulan query utama Hasura                           |

## Table Reporting

Table reporting yang digunakan:

```text id="830gw4"
report_fields
report_members
report_bookings
report_notification_logs
```

## View Reporting

View reporting yang digunakan:

```text id="qnyq0z"
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

## Data Reporting

Data pada Hasura berasal dari file:

```text id="onh5w7"
hasura/local/schema/reporting-schema.sql
```

File tersebut membuat table, view, dan data demo reporting yang selaras dengan kebutuhan demo project.

Data reporting mencakup:

```text id="1we29m"
1. Data lapangan.
2. Data member.
3. Data booking.
4. Data log notifikasi.
5. Data summary dashboard.
```

## Cara Singkat Menjalankan Hasura

Jalankan Hasura dan database Hasura:

```powershell id="4zksfa"
docker compose up -d hasura-db hasura
```

Buka Hasura Console:

```text id="7jeq9q"
http://localhost:8080
```

Masukkan admin secret:

```text id="vsyb3z"
retobluto_admin_secret
```

Jalankan SQL dari file:

```text id="scc8j0"
hasura/local/schema/reporting-schema.sql
```

Lalu track table dan view pada menu:

```text id="f4v9u8"
Data -> public
```

## Query Utama

Contoh query dashboard summary:

```graphql id="4vj1wl"
query DashboardSummary {
  v_dashboard_summary {
    fields_total
    members_total
    bookings_total
    notifications_total
    approved_revenue_total
  }
}
```

Contoh query field report:

```graphql id="bhpdmm"
query FieldReport {
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

Contoh query booking report:

```graphql id="34q1gi"
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
    total_price
    status
  }
}
```

## Catatan Penting

```text id="7vzmaq"
1. Hasura digunakan untuk reporting/read-only query.
2. Hasura tidak digunakan untuk login, register, OTP, booking, approve, reject, atau cancel.
3. Proses transaksi utama tetap dilakukan melalui Laravel microservices.
4. GraphQL Gateway manual tetap digunakan sebagai implementasi GraphQL berbasis Laravel.
5. Hasura menggunakan PostgreSQL hasura_db, bukan database MySQL service utama.
6. Data Hasura tidak otomatis sama dengan data Web Client jika tidak dilakukan sinkronisasi.
```

## Alasan Menggunakan Hasura Local

Hasura dijalankan secara lokal menggunakan Docker Compose agar seluruh service, database, dan GraphQL engine berada dalam satu environment yang sama.

Pendekatan ini dipilih agar sistem mudah direplikasi, diuji, dan didemokan tanpa bergantung pada database public atau koneksi cloud.

## Kesimpulan

Hasura Local pada project Retobluto Arena Microservices berfungsi sebagai reporting GraphQL Engine. Hasura melengkapi GraphQL Gateway manual dengan menyediakan query otomatis berbasis table dan view PostgreSQL.

Dengan adanya Hasura, project memiliki dua pendekatan GraphQL:

```text id="l3vx87"
1. GraphQL manual berbasis Laravel melalui graphql-gateway.
2. GraphQL otomatis berbasis Hasura untuk reporting.
```
