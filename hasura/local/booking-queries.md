# Booking Reporting Queries

File ini berisi contoh query Hasura untuk kebutuhan reporting data booking.

## Query Semua Booking

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

## Query Booking Pending

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

## Query Booking Approved

```graphql
query ApprovedBookings {
  v_booking_report(
    where: { status: { _eq: "approved" } }
    order_by: { booking_date: desc }
  ) {
    id
    member_name
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

## Query Booking Rejected

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
    status
    rejection_reason
    rejected_by
    rejected_at
  }
}
```

## Query Booking Canceled

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
    status
    canceled_by
    canceled_at
  }
}
```

## Query Booking Berdasarkan Lapangan

```graphql
query BookingByField {
  v_booking_report(
    where: { field_name: { _ilike: "%Futsal%" } }
    order_by: { booking_date: desc }
  ) {
    id
    member_name
    field_name
    field_type
    booking_date
    start_time
    end_time
    status
  }
}
```

## Query Booking Berdasarkan Member

```graphql
query BookingByMember {
  v_booking_report(
    where: { member_email: { _eq: "member.demo@example.com" } }
    order_by: { booking_date: desc }
  ) {
    id
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

## Query Revenue dari Booking Approved

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

## Query Statistik Booking

```graphql
query BookingStats {
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
