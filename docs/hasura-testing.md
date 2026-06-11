# Hasura Testing Guide

Dokumen ini berisi panduan testing Hasura local sebagai GraphQL Engine untuk reporting.

## Endpoint

Hasura Console:

```text
http://localhost:8080
```

Admin secret:

```text
retobluto_admin_secret
```

## Fungsi Hasura

Hasura digunakan sebagai reporting/read-only GraphQL Engine. Hasura tidak digunakan untuk transaksi utama seperti login, OTP, booking, approve, reject, atau cancel.

## Database Hasura

Hasura menggunakan PostgreSQL:

```text
hasura_db
```

Container:

```text
retobluto_hasura_db
```

Service Docker:

```text
hasura-db
```

## Setup Schema Reporting

Buka Hasura Console:

```text
http://localhost:8080
```

Masuk ke:

```text
Data -> SQL
```

Jalankan isi file:

```text
hasura/local/schema/reporting-schema.sql
```

## Track Tables

Track tabel berikut:

```text
report_fields
report_members
report_bookings
report_notification_logs
```

## Track Views

Track view berikut:

```text
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

## Query Dashboard Summary

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
Hasura menampilkan jumlah field, member, booking, revenue, dan notification.
```

## Query Field Report

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

## Query Member Report

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

## Query Booking Report

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

## Query Pending Booking

```graphql
query PendingBookings {
  v_booking_report(
    where: { status: { _eq: "pending" } }
    order_by: { booking_date: asc }
  ) {
    id
    member_name
    field_name
    booking_date
    start_time
    end_time
    status
  }
}
```

## Query Approved Revenue

```graphql
query ApprovedRevenue {
  report_bookings(
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

  v_dashboard_summary {
    approved_revenue_total
  }
}
```

## Query Notification Report

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

## Query Failed Notification

```graphql
query FailedNotifications {
  report_notification_logs(
    where: { status: { _eq: "failed" } }
    order_by: { id: desc }
  ) {
    id
    recipient_email
    type
    subject
    status
    error_message
  }
}
```

## Catatan Sinkronisasi

Pada project ini, database Hasura berbeda dari database utama web/service.

Struktur database:

```text
auth-service          -> auth_db
member-service        -> member_db
field-service         -> field_db
booking-service       -> booking_db
notification-service  -> notification_db
hasura                -> hasura_db
```

Hasura menggunakan database reporting agar prinsip database per service tetap terjaga. Data reporting dapat diisi melalui SQL seed, import manual, atau proses sinkronisasi dari service pada pengembangan berikutnya.

## Alasan Tidak Menggunakan Hasura Cloud

Project ini menggunakan Hasura local agar seluruh sistem dapat dijalankan dalam satu environment Docker. Dengan pendekatan ini, proses demo dan pengujian tidak bergantung pada database public atau koneksi cloud.
