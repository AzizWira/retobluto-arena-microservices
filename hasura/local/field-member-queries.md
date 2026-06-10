# Field and Member Reporting Queries

File ini berisi contoh query Hasura untuk kebutuhan reporting data lapangan dan member.

## Query Semua Lapangan

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

## Query Lapangan Available

```graphql
query AvailableFields {
  v_field_report(
    where: { status: { _eq: "available" } }
    order_by: { id: asc }
  ) {
    id
    name
    type
    location
    price_per_hour
    status
  }
}
```

## Query Lapangan Maintenance

```graphql
query MaintenanceFields {
  v_field_report(
    where: { status: { _eq: "maintenance" } }
    order_by: { id: asc }
  ) {
    id
    name
    type
    location
    status
  }
}
```

## Query Filter Lapangan Berdasarkan Tipe

```graphql
query FutsalFields {
  v_field_report(
    where: { type: { _eq: "Futsal" } }
    order_by: { price_per_hour: asc }
  ) {
    id
    name
    type
    location
    price_per_hour
    status
  }
}
```

## Query Statistik Status Lapangan

```graphql
query FieldStatusStats {
  report_fields {
    id
    name
    status
  }

  v_dashboard_summary {
    fields_total
    fields_available
    fields_maintenance
    fields_inactive
  }
}
```

## Query Semua Member

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

## Query Member Aktif

```graphql
query ActiveMembers {
  v_member_report(where: { status: { _eq: "active" } }, order_by: { id: asc }) {
    id
    name
    email
    phone
    status
  }
}
```

## Query Member Inactive

```graphql
query InactiveMembers {
  v_member_report(
    where: { status: { _eq: "inactive" } }
    order_by: { id: asc }
  ) {
    id
    name
    email
    phone
    status
  }
}
```

## Query Member Blocked

```graphql
query BlockedMembers {
  v_member_report(
    where: { status: { _eq: "blocked" } }
    order_by: { id: asc }
  ) {
    id
    name
    email
    phone
    status
  }
}
```

## Query Search Member Berdasarkan Email

```graphql
query SearchMemberByEmail {
  v_member_report(
    where: { email: { _ilike: "%demo%" } }
    order_by: { id: asc }
  ) {
    id
    name
    email
    status
  }
}
```

## Query Statistik Member

```graphql
query MemberStats {
  v_dashboard_summary {
    members_total
    members_active
    members_inactive
    members_blocked
  }
}
```
