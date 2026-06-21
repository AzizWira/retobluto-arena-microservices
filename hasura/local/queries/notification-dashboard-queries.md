# Hasura Notification and Dashboard Queries

Dokumen ini berisi contoh query Hasura untuk data reporting notification log dan dashboard summary pada project Retobluto Arena Microservices.

Query pada file ini dijalankan melalui Hasura Console:

```text id="9m2xvv"
http://localhost:8080
```

Admin secret:

```text id="gpe4pw"
retobluto_admin_secret
```

## Table dan View yang Digunakan

Query notification menggunakan:

```text id="1uq5a9"
report_notification_logs
v_notification_report
```

Query dashboard menggunakan:

```text id="nn046q"
v_dashboard_summary
```

## Catatan

View `v_notification_report` digunakan untuk laporan notifikasi secara ringkas.

Table `report_notification_logs` digunakan jika membutuhkan data lengkap seperti:

```text id="d3a42b"
message
error_message
source_created_at
source_updated_at
```

Status notifikasi yang digunakan:

```text id="wow0sr"
sent
failed
```

Tipe notifikasi yang digunakan:

```text id="gijtd5"
otp
booking_approved
booking_rejected
booking_canceled
email
```

---

# 1. Query Semua Notification Log

Query ini menampilkan seluruh data notification log dari view reporting.

```graphql id="f2kz1w"
query AllNotificationLogs {
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

Hasura menampilkan seluruh data notification log reporting dengan urutan terbaru.

---

# 2. Query Notification Sent

Query ini menampilkan notification log dengan status sent.

```graphql id="ehnw8l"
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

Hasura menampilkan log notifikasi yang berhasil dikirim.

---

# 3. Query Notification Failed

Query ini menggunakan table `report_notification_logs` agar `error_message` dapat ditampilkan.

```graphql id="96p0pz"
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
    synced_at
  }
}
```

## Expected Result

Hasura menampilkan log notifikasi yang gagal dikirim beserta pesan error.

---

# 4. Query Notification OTP

Query ini menampilkan log notifikasi dengan tipe OTP.

```graphql id="273874"
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

Hasura menampilkan log notifikasi OTP registrasi member.

---

# 5. Query Notification Booking Approved

Query ini menampilkan log notifikasi booking approved.

```graphql id="bkjbaf"
query BookingApprovedNotifications {
  v_notification_report(
    where: { type: { _eq: "booking_approved" } }
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

Hasura menampilkan log email notifikasi booking yang disetujui admin.

---

# 6. Query Notification Booking Rejected

Query ini menampilkan log notifikasi booking rejected.

```graphql id="wop7ma"
query BookingRejectedNotifications {
  v_notification_report(
    where: { type: { _eq: "booking_rejected" } }
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

Hasura menampilkan log email notifikasi booking yang ditolak admin.

---

# 7. Query Notification Booking Canceled

Query ini menampilkan log notifikasi booking canceled.

```graphql id="csii6t"
query BookingCanceledNotifications {
  v_notification_report(
    where: { type: { _eq: "booking_canceled" } }
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

Hasura menampilkan log email notifikasi booking yang dibatalkan.

---

# 8. Query Notification Email Manual

Query ini menampilkan log email manual admin.

```graphql id="4ba4ep"
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

Hasura menampilkan log email manual yang dikirim oleh admin.

---

# 9. Query Notification Berdasarkan Email Tujuan

Query ini menampilkan log notifikasi berdasarkan email penerima.

```graphql id="k9gn6z"
query NotificationsByRecipient {
  v_notification_report(
    where: { recipient_email: { _eq: "wira123widodo@gmail.com" } }
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

Hasura menampilkan log notifikasi untuk email penerima tertentu.

---

# 10. Query Notification Berdasarkan Keyword Email

Query ini menampilkan log notifikasi berdasarkan keyword email.

```graphql id="6bcyy7"
query SearchNotificationByEmail {
  v_notification_report(
    where: { recipient_email: { _ilike: "%gmail.com%" } }
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

Hasura menampilkan log notifikasi dengan email penerima yang mengandung keyword tertentu.

---

# 11. Query Notification Berdasarkan Subject

Query ini menampilkan log notifikasi berdasarkan keyword subject.

```graphql id="uhun1h"
query NotificationsBySubject {
  v_notification_report(
    where: { subject: { _ilike: "%Booking%" } }
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

Hasura menampilkan log notifikasi dengan subject yang sesuai keyword pencarian.

---

# 12. Query Detail Notification Log

Query ini menggunakan table `report_notification_logs` agar message dan error_message dapat ditampilkan.

```graphql id="tnig01"
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

Hasura menampilkan detail notification log berdasarkan `source_log_id`.

---

# 13. Query Notification Berdasarkan Rentang Waktu Sent At

Query ini menampilkan notifikasi berdasarkan rentang waktu pengiriman.

```graphql id="f2x0br"
query NotificationsBySentDateRange {
  v_notification_report(
    where: {
      sent_at: { _gte: "2026-06-01T00:00:00", _lte: "2026-06-30T23:59:59" }
    }
    order_by: { sent_at: desc }
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

Hasura menampilkan log notifikasi berdasarkan rentang waktu `sent_at`.

---

# 14. Query Notification Sent Berdasarkan Tipe

Query ini menampilkan notifikasi sukses berdasarkan tipe tertentu.

```graphql id="e4ma1x"
query SentOtpNotifications {
  v_notification_report(
    where: { status: { _eq: "sent" }, type: { _eq: "otp" } }
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

Hasura menampilkan log OTP yang berhasil dikirim.

---

# 15. Query Failed Notification dengan Error Message

Query ini menampilkan notifikasi gagal yang memiliki pesan error.

```graphql id="73e9xi"
query FailedNotificationsWithErrorMessage {
  report_notification_logs(
    where: { status: { _eq: "failed" }, error_message: { _is_null: false } }
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

Hasura menampilkan log notifikasi gagal beserta detail error.

---

# 16. Query Summary Notification Berdasarkan Status

Query ini menggunakan aggregate untuk menghitung jumlah notification berdasarkan status.

```graphql id="cekz31"
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

Hasura menampilkan jumlah seluruh notifikasi, jumlah notifikasi terkirim, dan jumlah notifikasi gagal.

---

# 17. Query Summary Notification Berdasarkan Tipe

Query ini menggunakan aggregate untuk menghitung notifikasi berdasarkan tipe tertentu.

```graphql id="4dlivz"
query NotificationTypeSummary {
  otp_notifications: report_notification_logs_aggregate(
    where: { type: { _eq: "otp" } }
  ) {
    aggregate {
      count
    }
  }

  booking_approved_notifications: report_notification_logs_aggregate(
    where: { type: { _eq: "booking_approved" } }
  ) {
    aggregate {
      count
    }
  }

  booking_rejected_notifications: report_notification_logs_aggregate(
    where: { type: { _eq: "booking_rejected" } }
  ) {
    aggregate {
      count
    }
  }

  booking_canceled_notifications: report_notification_logs_aggregate(
    where: { type: { _eq: "booking_canceled" } }
  ) {
    aggregate {
      count
    }
  }

  manual_email_notifications: report_notification_logs_aggregate(
    where: { type: { _eq: "email" } }
  ) {
    aggregate {
      count
    }
  }
}
```

## Expected Result

Hasura menampilkan jumlah notifikasi berdasarkan tipe.

---

# 18. Query Dashboard Summary Lengkap

Query ini mengambil seluruh summary utama dari view `v_dashboard_summary`.

```graphql id="7wiolp"
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

Hasura menampilkan summary field, member, booking, revenue, dan notification.

---

# 19. Query Dashboard Summary Ringkas

Query ini mengambil beberapa angka penting untuk kebutuhan dashboard singkat.

```graphql id="o09wiv"
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

Hasura menampilkan total utama sistem dan revenue booking approved.

---

# 20. Query Dashboard Field Summary

Query ini mengambil summary lapangan dari view dashboard.

```graphql id="s4nzh2"
query DashboardFieldSummary {
  v_dashboard_summary {
    fields_total
    fields_available
    fields_maintenance
    fields_inactive
  }
}
```

## Expected Result

Hasura menampilkan ringkasan jumlah lapangan berdasarkan status.

---

# 21. Query Dashboard Member Summary

Query ini mengambil summary member dari view dashboard.

```graphql id="0ttkqm"
query DashboardMemberSummary {
  v_dashboard_summary {
    members_total
    members_active
    members_inactive
    members_blocked
  }
}
```

## Expected Result

Hasura menampilkan ringkasan jumlah member berdasarkan status.

---

# 22. Query Dashboard Booking Summary

Query ini mengambil summary booking dari view dashboard.

```graphql id="kfb49j"
query DashboardBookingSummary {
  v_dashboard_summary {
    bookings_total
    bookings_pending
    bookings_approved
    bookings_rejected
    bookings_canceled
    approved_revenue_total
  }
}
```

## Expected Result

Hasura menampilkan ringkasan jumlah booking berdasarkan status dan total revenue approved.

---

# 23. Query Dashboard Notification Summary

Query ini mengambil summary notification dari view dashboard.

```graphql id="jbp7qm"
query DashboardNotificationSummary {
  v_dashboard_summary {
    notifications_total
    notifications_sent
    notifications_failed
  }
}
```

## Expected Result

Hasura menampilkan ringkasan jumlah notification berdasarkan status.

---

# 24. Query Dashboard untuk Presentasi Demo

Query ini dapat digunakan ketika demo karena menampilkan seluruh angka utama dalam satu request.

```graphql id="wqoayq"
query DemoDashboard {
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
}
```

## Expected Result

Hasura menampilkan data ringkasan yang mudah dijelaskan saat demo final.

---

# 25. Query Gabungan Dashboard dan Report Terbaru

Query ini menampilkan dashboard summary dan beberapa data notification terbaru.

```graphql id="lw3x1l"
query DashboardWithLatestNotifications {
  v_dashboard_summary {
    fields_total
    members_total
    bookings_total
    approved_revenue_total
    notifications_total
    notifications_sent
    notifications_failed
  }

  v_notification_report(order_by: { id: desc }, limit: 5) {
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

Hasura menampilkan ringkasan dashboard dan 5 log notifikasi terbaru.

---

# 26. Query Gabungan Dashboard dan Booking Terbaru

Query ini menampilkan dashboard summary dan beberapa booking terbaru.

```graphql id="sn0ys2"
query DashboardWithLatestBookings {
  v_dashboard_summary {
    fields_total
    members_total
    bookings_total
    bookings_pending
    bookings_approved
    approved_revenue_total
  }

  v_booking_report(order_by: { booking_date: desc }, limit: 5) {
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

Hasura menampilkan ringkasan dashboard dan 5 data booking terbaru.

---

# 27. Query Gabungan Dashboard, Field, Member, Booking, dan Notification

Query ini dapat digunakan sebagai query lengkap untuk mengecek semua view utama.

```graphql id="bkc6dy"
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

Hasura menampilkan data dari seluruh view utama reporting.

---

# 28. Catatan Penggunaan Query Notification dan Dashboard

Gunakan `v_notification_report` untuk query notification ringkas.

Gunakan `report_notification_logs` jika membutuhkan data lengkap seperti:

```text id="fo8aft"
message
error_message
source_created_at
source_updated_at
```

Gunakan `v_dashboard_summary` untuk mengambil ringkasan:

```text id="5692j6"
field
member
booking
revenue
notification
```

Dashboard summary dihitung dari table reporting:

```text id="r3xg2a"
report_fields
report_members
report_bookings
report_notification_logs
```

Revenue pada dashboard dihitung dari booking dengan status:

```text id="t7q1yf"
approved
```

## Kesimpulan

Query pada file ini digunakan untuk membuktikan bahwa Hasura dapat membaca data notification log dan dashboard summary.

Dengan query ini, Hasura dapat digunakan untuk:

```text id="yqxcv9"
1. Melihat seluruh log notifikasi.
2. Memfilter notifikasi berdasarkan status, tipe, subject, dan email penerima.
3. Melihat error pada notifikasi gagal.
4. Menghitung jumlah notifikasi berdasarkan status dan tipe.
5. Menampilkan dashboard summary.
6. Menggabungkan dashboard summary dengan data report terbaru.
```
