# GraphQL Gateway Testing Guide

Dokumen ini berisi panduan testing GraphQL Gateway manual berbasis Laravel.

## Endpoint

Playground:

```text
http://localhost:8010/playground
```

GraphQL endpoint:

```text
http://localhost:8010/api/graphql
```

Schema:

```text
http://localhost:8010/api/graphql/schema
```

Health check:

```text
http://localhost:8010/api/health
```

## Cara Menggunakan Token

Beberapa query dan mutation membutuhkan token.

Token dapat diperoleh melalui login Auth Service atau login dari web. Pada playground, token dapat dimasukkan pada field token.

Format token:

```text
Bearer <access_token>
```

## Query Health

```graphql
query {
  health {
    auth {
      ok
      status
      message
    }
    member {
      ok
      status
      message
    }
    field {
      ok
      status
      message
    }
    booking {
      ok
      status
      message
    }
    notification {
      ok
      status
      message
    }
  }
}
```

Expected result:

```text
Semua service menampilkan ok true.
```

## Query Fields

```graphql
query {
  fields {
    id
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

## Query Available Fields

```graphql
query {
  availableFields {
    id
    name
    type
    location
    price_per_hour
    status
  }
}
```

## Query Field Detail

```graphql
query {
  field(id: 1) {
    id
    name
    type
    description
    location
    price_per_hour
    status
    open_time
    close_time
  }
}
```

## Mutation Create Field

Mutation ini digunakan oleh admin.

```graphql
mutation {
  createField(
    name: "Lapangan Test GraphQL"
    type: "Futsal"
    description: "Lapangan dibuat dari GraphQL Gateway"
    location: "Gedung Test"
    price_per_hour: 100000
    status: "available"
    open_time: "08:00"
    close_time: "22:00"
  ) {
    success
    message
    field {
      id
      name
      type
      status
    }
    errors
  }
}
```

## Mutation Update Field

```graphql
mutation {
  updateField(
    id: 1
    name: "Lapangan Futsal Updated"
    type: "Futsal"
    description: "Update melalui GraphQL Gateway"
    location: "Arena Utama"
    price_per_hour: 120000
    status: "available"
    open_time: "08:00"
    close_time: "22:00"
  ) {
    success
    message
    field {
      id
      name
      status
    }
    errors
  }
}
```

## Mutation Update Field Status

```graphql
mutation {
  updateFieldStatus(id: 1, status: "maintenance") {
    success
    message
    field {
      id
      name
      status
    }
    errors
  }
}
```

## Query Members

Query ini membutuhkan token admin.

```graphql
query {
  members {
    success
    message
    members {
      id
      user_id
      name
      email
      phone
      status
    }
    errors
  }
}
```

## Query My Profile

Query ini membutuhkan token member.

```graphql
query {
  myProfile {
    success
    message
    member {
      id
      user_id
      name
      email
      phone
      address
      status
    }
    errors
  }
}
```

## Query Bookings

Query ini membutuhkan token admin.

```graphql
query {
  bookings {
    success
    message
    bookings {
      id
      member_name
      field_name
      booking_date
      start_time
      end_time
      total_price
      status
    }
    errors
  }
}
```

## Query My Bookings

Query ini membutuhkan token member.

```graphql
query {
  myBookings {
    success
    message
    bookings {
      id
      field_name
      booking_date
      start_time
      end_time
      status
      total_price
    }
    errors
  }
}
```

## Mutation Create Booking

Mutation ini membutuhkan token member aktif.

```graphql
mutation {
  createBooking(
    field_id: 1
    booking_date: "2026-06-15"
    start_time: "09:00"
    end_time: "11:00"
    note: "Booking dari GraphQL Gateway"
  ) {
    success
    message
    booking {
      id
      member_name
      field_name
      booking_date
      start_time
      end_time
      status
      total_price
    }
    errors
  }
}
```

## Mutation Approve Booking

Mutation ini membutuhkan token admin.

```graphql
mutation {
  approveBooking(id: 1) {
    success
    message
    booking {
      id
      status
      approved_by
      approved_at
    }
    errors
  }
}
```

## Mutation Reject Booking

Mutation ini membutuhkan token admin.

```graphql
mutation {
  rejectBooking(id: 1, rejection_reason: "Jadwal tidak tersedia") {
    success
    message
    booking {
      id
      status
      rejection_reason
      rejected_by
      rejected_at
    }
    errors
  }
}
```

## Mutation Cancel Booking

Mutation ini membutuhkan token member.

```graphql
mutation {
  cancelBooking(id: 1) {
    success
    message
    booking {
      id
      status
      canceled_by
      canceled_at
    }
    errors
  }
}
```

## Query Notification Logs

Query ini membutuhkan token admin.

```graphql
query {
  notificationLogs {
    success
    message
    logs {
      id
      recipient_email
      type
      subject
      status
      sent_at
    }
    errors
  }
}
```

## Query Dashboard Summary

```graphql
query {
  dashboardSummary {
    success
    message
    fields_total
    fields_available
    members_total
    members_active
    bookings_total
    bookings_pending
    bookings_approved
    notifications_total
    notifications_sent
    notifications_failed
    errors
  }
}
```

## Catatan

GraphQL Gateway manual pada project ini tidak menggantikan service utama. Gateway ini meneruskan request GraphQL ke REST API microservices.
