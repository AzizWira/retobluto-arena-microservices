# Hasura GraphQL Query Examples

Contoh query berikut dijalankan melalui Hasura Console:

```text
http://localhost:8080
```

Masuk ke menu:

```text
API
```

## 1. Query Dashboard Summary

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

## 2. Query Field Report

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

## 3. Query Member Report

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

## 4. Query Booking Report

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
  }
}
```

## 5. Query Booking Pending

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

## 6. Query Approved Revenue

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

## 7. Query Notification Report

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

## 8. Query Failed Notification

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

## Catatan

Query Hasura ini digunakan untuk reporting dan analisis data. Proses transaksi utama tetap dilakukan melalui Laravel microservices dan GraphQL Gateway manual.
