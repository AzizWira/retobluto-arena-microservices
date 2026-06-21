# Hasura Query Examples

Dokumen ini berisi kumpulan query utama Hasura untuk kebutuhan testing, demo, dan validasi reporting pada project Retobluto Arena Microservices.

Query dijalankan melalui Hasura Console:

```text id="cbdu30"
http://localhost:8080
```

Admin secret:

```text id="xv7e64"
retobluto_admin_secret
```

## Tujuan Query Examples

File ini digunakan sebagai referensi cepat untuk:

```text id="mg2ibg"
1. Mengecek dashboard summary.
2. Mengecek data lapangan.
3. Mengecek data member.
4. Mengecek data booking.
5. Mengecek data notification log.
6. Mengecek filter data reporting.
7. Menunjukkan kemampuan Hasura sebagai GraphQL Engine otomatis.
```

## Table Reporting

```text id="ik7x10"
report_fields
report_members
report_bookings
report_notification_logs
```

## View Reporting

```text id="iu9b53"
v_dashboard_summary
v_field_report
v_member_report
v_booking_report
v_notification_report
```

---

# 1. Dashboard Summary Lengkap

Query ini digunakan untuk mengambil seluruh ringkasan utama sistem.

```graphql id="rcrn94"
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

Hasura menampilkan ringkasan jumlah lapangan, member, booking, revenue, dan notification.

---

# 2. Dashboard Summary Ringkas

Query ini cocok digunakan saat demo karena hanya menampilkan angka utama.

```graphql id="j44c94"
query DashboardShortSummary {
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

Hasura menampilkan ringkasan total data utama project.

---

# 3. Semua Data Field

```graphql id="zuvrts"
query AllFields {
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
  }
}
```

## Expected Result

Hasura menampilkan semua data lapangan dari view reporting.

---

# 4. Field Available

```graphql id="g6yrvm"
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

Hasura menampilkan lapangan dengan status `available`.

---

# 5. Field Maintenance

```graphql id="9t93ce"
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

Hasura menampilkan lapangan dengan status `maintenance`.

---

# 6. Field Berdasarkan Tipe

```graphql id="j4avws"
query FieldsByType {
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

Hasura menampilkan lapangan berdasarkan tipe tertentu.

---

# 7. Field Berdasarkan Keyword Nama

```graphql id="cfzblc"
query SearchFieldByName {
  v_field_report(
    where: { name: { _ilike: "%Futsal%" } }
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

Hasura menampilkan lapangan yang namanya sesuai keyword pencarian.

---

# 8. Detail Field Lengkap

Query ini menggunakan table `report_fields` karena data lengkap tersedia pada table.

```graphql id="v0gtzv"
query FieldDetail {
  report_fields(order_by: { id: asc }) {
    id
    source_field_id
    name
    type
    description
    location
    price_per_hour
    status
    open_time
    close_time
    source_created_at
    source_updated_at
    synced_at
  }
}
```

## Expected Result

Hasura menampilkan data lapangan lengkap.

---

# 9. Semua Data Member

```graphql id="qa5zcb"
query AllMembers {
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

Hasura menampilkan semua data member dari view reporting.

---

# 10. Member Active

```graphql id="rh5ao6"
query ActiveMembers {
  v_member_report(where: { status: { _eq: "active" } }, order_by: { id: asc }) {
    id
    source_member_id
    source_user_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan member dengan status `active`.

---

# 11. Member Inactive

```graphql id="jh1qaw"
query InactiveMembers {
  v_member_report(
    where: { status: { _eq: "inactive" } }
    order_by: { id: asc }
  ) {
    id
    source_member_id
    source_user_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan member dengan status `inactive`.

---

# 12. Member Blocked

```graphql id="c1u05r"
query BlockedMembers {
  v_member_report(
    where: { status: { _eq: "blocked" } }
    order_by: { id: asc }
  ) {
    id
    source_member_id
    source_user_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan member dengan status `blocked`.

---

# 13. Member Berdasarkan Email

```graphql id="oa3bdt"
query MemberByEmail {
  v_member_report(where: { email: { _eq: "wira123widodo@gmail.com" } }) {
    id
    source_member_id
    source_user_id
    name
    email
    phone
    status
  }
}
```

## Expected Result

Hasura menampilkan data member berdasarkan email tertentu.

---

# 14. Detail Member Lengkap

Query ini menggunakan table `report_members` karena field `address` tersedia pada table.

```graphql id="pltx0n"
query MemberDetail {
  report_members(order_by: { id: asc }) {
    id
    source_member_id
    source_user_id
    name
    email
    phone
    address
    status
    source_created_at
    source_updated_at
    synced_at
  }
}
```

## Expected Result

Hasura menampilkan data member lengkap.

---

# 15. Semua Data Booking

```graphql id="e3l4tg"
query AllBookings {
  v_booking_report(order_by: { booking_date: desc }) {
    id
    source_booking_id
    source_member_id
    member_name
    member_email
    source_field_id
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
  }
}
```

## Expected Result

Hasura menampilkan seluruh data booking dari view reporting.

---

# 16. Booking Pending

Booking request pada sistem adalah booking dengan status `pending`.

```graphql id="jtt2bc"
query PendingBookings {
  v_booking_report(
    where: { status: { _eq: "pending" } }
    order_by: { booking_date: asc }
  ) {
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

## Expected Result

Hasura menampilkan booking request yang menunggu approve/reject admin.

---

# 17. Booking Approved

```graphql id="4idepp"
query ApprovedBookings {
  v_booking_report(
    where: { status: { _eq: "approved" } }
    order_by: { booking_date: desc }
  ) {
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
    approved_by
  }
}
```

## Expected Result

Hasura menampilkan booking yang sudah disetujui admin.

---

# 18. Booking Rejected

Query ini menggunakan table `report_bookings` agar alasan reject dapat ditampilkan.

```graphql id="x6c15c"
query RejectedBookings {
  report_bookings(
    where: { status: { _eq: "rejected" } }
    order_by: { booking_date: desc }
  ) {
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
    rejection_reason
    rejected_by
    rejected_at
  }
}
```

## Expected Result

Hasura menampilkan booking rejected beserta alasan penolakan.

---

# 19. Booking Canceled

Query ini menggunakan table `report_bookings` agar waktu cancel dapat ditampilkan.

```graphql id="p9y7kq"
query CanceledBookings {
  report_bookings(
    where: { status: { _eq: "canceled" } }
    order_by: { booking_date: desc }
  ) {
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
    canceled_by
    canceled_at
  }
}
```

## Expected Result

Hasura menampilkan booking yang dibatalkan.

---

# 20. Booking Berdasarkan Member

```graphql id="gu2y6g"
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

# 21. Booking Berdasarkan Lapangan

```graphql id="xz5rvb"
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

Hasura menampilkan booking berdasarkan keyword nama lapangan.

---

# 22. Booking Berdasarkan Tanggal

```graphql id="mojz3o"
query BookingByDate {
  v_booking_report(
    where: { booking_date: { _eq: "2026-06-30" } }
    order_by: { start_time: asc }
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

Hasura menampilkan booking berdasarkan tanggal tertentu.

---

# 23. Booking Berdasarkan Rentang Tanggal

```graphql id="qy2odh"
query BookingByDateRange {
  v_booking_report(
    where: { booking_date: { _gte: "2026-06-01", _lte: "2026-06-30" } }
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

Hasura menampilkan booking dalam rentang tanggal tertentu.

---

# 24. Total Revenue Booking Approved

Revenue dihitung dari booking dengan status `approved`.

```graphql id="xm7u0q"
query ApprovedRevenue {
  report_bookings_aggregate(where: { status: { _eq: "approved" } }) {
    aggregate {
      count
      sum {
        total_price
      }
    }
  }
}
```

## Expected Result

Hasura menampilkan jumlah booking approved dan total revenue.

---

# 25. Summary Booking Berdasarkan Status

```graphql id="u4pbhb"
query BookingStatusSummary {
  total_bookings: report_bookings_aggregate {
    aggregate {
      count
    }
  }

  pending_bookings: report_bookings_aggregate(
    where: { status: { _eq: "pending" } }
  ) {
    aggregate {
      count
    }
  }

  approved_bookings: report_bookings_aggregate(
    where: { status: { _eq: "approved" } }
  ) {
    aggregate {
      count
    }
  }

  rejected_bookings: report_bookings_aggregate(
    where: { status: { _eq: "rejected" } }
  ) {
    aggregate {
      count
    }
  }

  canceled_bookings: report_bookings_aggregate(
    where: { status: { _eq: "canceled" } }
  ) {
    aggregate {
      count
    }
  }
}
```

## Expected Result

Hasura menampilkan jumlah booking berdasarkan status.

---

# 26. Semua Notification Log

```graphql id="wx5pwx"
query AllNotificationLogs {
  v_notification_report(order_by: { id: desc }) {
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

Hasura menampilkan seluruh log notifikasi dari view reporting.

---

# 27. Notification Sent

```graphql id="gu96g5"
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

Hasura menampilkan notifikasi dengan status `sent`.

---

# 28. Notification Failed

Query ini menggunakan table `report_notification_logs` agar `error_message` dapat ditampilkan.

```graphql id="d3d3ht"
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

Hasura menampilkan notifikasi gagal beserta pesan error.

---

# 29. Notification OTP

```graphql id="hhe78q"
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

# 30. Notification Booking

```graphql id="hmz8cv"
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

Hasura menampilkan log notifikasi status booking.

---

# 31. Notification Email Manual

```graphql id="e1ztw6"
query ManualEmailNotifications {
  v_notification_report(
    where: { type: { _eq: "email" } }
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

Hasura menampilkan log email manual admin.

---

# 32. Detail Notification Log

```graphql id="a8uydm"
query NotificationDetail {
  report_notification_logs(where: { source_log_id: { _eq: 1 } }) {
    id
    source_log_id
    recipient_email
    type
    subject
    message
    status
    error_message
    sent_at
    source_created_at
    source_updated_at
    synced_at
  }
}
```

## Expected Result

Hasura menampilkan detail notification log berdasarkan source log id.

---

# 33. Summary Notification Berdasarkan Status

```graphql id="dn8pvm"
query NotificationStatusSummary {
  total_notifications: report_notification_logs_aggregate {
    aggregate {
      count
    }
  }

  sent_notifications: report_notification_logs_aggregate(
    where: { status: { _eq: "sent" } }
  ) {
    aggregate {
      count
    }
  }

  failed_notifications: report_notification_logs_aggregate(
    where: { status: { _eq: "failed" } }
  ) {
    aggregate {
      count
    }
  }
}
```

## Expected Result

Hasura menampilkan jumlah notification berdasarkan status.

---

# 34. Full Reporting Check

Query ini digunakan untuk mengecek seluruh view utama dalam satu request.

```graphql id="t4upkb"
query FullReportingCheck {
  v_dashboard_summary {
    fields_total
    members_total
    bookings_total
    notifications_total
    approved_revenue_total
  }

  v_field_report(order_by: { id: asc }, limit: 5) {
    id
    name
    type
    status
  }

  v_member_report(order_by: { id: asc }, limit: 5) {
    id
    name
    email
    status
  }

  v_booking_report(order_by: { booking_date: desc }, limit: 5) {
    id
    member_name
    field_name
    booking_date
    total_price
    status
  }

  v_notification_report(order_by: { id: desc }, limit: 5) {
    id
    recipient_email
    type
    subject
    status
  }
}
```

## Expected Result

Hasura menampilkan dashboard summary dan data dari seluruh view utama.

---

# 35. Query untuk Demo Presentasi

Query ini cocok digunakan ketika dosen meminta bukti Hasura berjalan.

```graphql id="zoih2e"
query DemoHasuraReporting {
  v_dashboard_summary {
    fields_total
    members_total
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

  v_booking_report(order_by: { booking_date: desc }, limit: 5) {
    source_booking_id
    member_name
    field_name
    booking_date
    start_time
    end_time
    total_price
    status
  }

  v_notification_report(order_by: { id: desc }, limit: 5) {
    source_log_id
    recipient_email
    type
    subject
    status
  }
}
```

## Expected Result

Hasura menampilkan ringkasan dashboard, booking terbaru, dan notification terbaru.

---

# 36. Catatan Penggunaan

Gunakan query pada file ini untuk kebutuhan testing cepat.

Untuk query yang lebih spesifik, gunakan file:

```text id="l6bvhk"
field-member-queries.md
booking-queries.md
notification-dashboard-queries.md
```

## Kesimpulan

Query examples ini membuktikan bahwa Hasura dapat digunakan sebagai GraphQL Engine otomatis untuk reporting.

Hasura dapat menampilkan:

```text id="rfnkch"
1. Dashboard summary.
2. Laporan lapangan.
3. Laporan member.
4. Laporan booking.
5. Laporan notification.
6. Filter data berdasarkan status, tanggal, tipe, member, field, dan email.
7. Aggregate data seperti count, sum, avg, max, dan min.
```
