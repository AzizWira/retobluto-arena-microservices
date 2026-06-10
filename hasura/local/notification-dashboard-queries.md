# Notification and Dashboard Reporting Queries

File ini berisi contoh query Hasura untuk reporting log notifikasi dan dashboard summary.

## Query Semua Log Notifikasi

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

## Query Notifikasi Sent

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

## Query Notifikasi Failed

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

## Query Notifikasi OTP

```graphql
query OtpNotifications {
  v_notification_report(
    where: { type: { _eq: "otp" } }
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

## Query Notifikasi Booking

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
    recipient_email
    type
    subject
    status
    sent_at
  }
}
```

## Query Dashboard Summary Lengkap

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

## Query Dashboard Ringkas

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
