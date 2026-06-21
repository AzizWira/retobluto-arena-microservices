# Hasura Field and Member Queries

Dokumen ini berisi contoh query Hasura untuk data reporting lapangan dan member pada project Retobluto Arena Microservices.

Query pada file ini dijalankan melalui Hasura Console:

```text id="8hks64"
http://localhost:8080
```

Admin secret:

```text id="nqnjye"
retobluto_admin_secret
```

## Table dan View yang Digunakan

Query field menggunakan:

```text id="bieiuz"
report_fields
v_field_report
```

Query member menggunakan:

```text id="akxkot"
report_members
v_member_report
```

## Catatan

View `v_field_report` digunakan untuk laporan data lapangan secara ringkas.

View `v_member_report` digunakan untuk laporan data member secara ringkas.

Jika membutuhkan field tambahan seperti `description` pada lapangan atau `address` pada member, gunakan table asli `report_fields` atau `report_members`.

---

# 1. Query Semua Field

Query ini menampilkan seluruh data lapangan dari view reporting.

```graphql id="wyzp29"
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
    synced_at
  }
}
```

## Expected Result

Hasura menampilkan seluruh data lapangan reporting.

---

# 2. Query Field Available

Query ini menampilkan lapangan yang tersedia untuk digunakan.

```graphql id="sqe5v0"
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
    open_time
    close_time
  }
}
```

## Expected Result

Hasura menampilkan lapangan dengan status:

```text id="m5yq8z"
available
```

---

# 3. Query Field Maintenance

Query ini menampilkan lapangan yang sedang maintenance.

```graphql id="i6nqod"
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
    open_time
    close_time
  }
}
```

## Expected Result

Hasura menampilkan lapangan dengan status:

```text id="y1kp0j"
maintenance
```

---

# 4. Query Field Inactive

Query ini menampilkan lapangan yang tidak aktif.

```graphql id="x5u3m8"
query InactiveFields {
  v_field_report(
    where: { status: { _eq: "inactive" } }
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

Hasura menampilkan lapangan dengan status:

```text id="4cn7co"
inactive
```

---

# 5. Query Field Berdasarkan Tipe

Query ini menampilkan lapangan berdasarkan tipe tertentu.

```graphql id="2nvf73"
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

Hasura menampilkan lapangan dengan tipe:

```text id="07mfo2"
Futsal
```

---

# 6. Query Field Berdasarkan Keyword Nama

Query ini menampilkan lapangan berdasarkan keyword nama.

```graphql id="cdffhi"
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

Hasura menampilkan lapangan yang namanya mengandung keyword pencarian.

---

# 7. Query Field Berdasarkan Lokasi

Query ini menampilkan lapangan berdasarkan lokasi.

```graphql id="g33bve"
query FieldsByLocation {
  v_field_report(
    where: { location: { _ilike: "%Arena%" } }
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

Hasura menampilkan lapangan yang lokasinya sesuai keyword.

---

# 8. Query Field Berdasarkan Harga Termurah

Query ini menampilkan lapangan dari harga termurah.

```graphql id="6txtyz"
query FieldsCheapest {
  v_field_report(order_by: { price_per_hour: asc }) {
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

Hasura menampilkan lapangan dengan urutan harga per jam dari yang paling murah.

---

# 9. Query Field Berdasarkan Harga Termahal

Query ini menampilkan lapangan dari harga termahal.

```graphql id="r62teg"
query FieldsMostExpensive {
  v_field_report(order_by: { price_per_hour: desc }) {
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

Hasura menampilkan lapangan dengan urutan harga per jam dari yang paling mahal.

---

# 10. Query Field dengan Detail Lengkap

Query ini menggunakan table `report_fields`, bukan view, agar field `description`, `source_created_at`, dan `source_updated_at` dapat ditampilkan.

```graphql id="f984nx"
query FieldDetailReport {
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

Hasura menampilkan data lapangan lengkap dari table reporting.

---

# 11. Query Semua Member

Query ini menampilkan seluruh data member dari view reporting.

```graphql id="idsbbr"
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

Hasura menampilkan seluruh data member reporting.

---

# 12. Query Member Active

Query ini menampilkan member dengan status active.

```graphql id="9dcd4u"
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

Hasura menampilkan member dengan status:

```text id="a8elz1"
active
```

---

# 13. Query Member Inactive

Query ini menampilkan member dengan status inactive.

```graphql id="qxvhjg"
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

Hasura menampilkan member dengan status:

```text id="qg24yg"
inactive
```

---

# 14. Query Member Blocked

Query ini menampilkan member dengan status blocked.

```graphql id="1chby6"
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

Hasura menampilkan member dengan status:

```text id="ezwukf"
blocked
```

---

# 15. Query Member Berdasarkan Email

Query ini menampilkan member berdasarkan email tertentu.

```graphql id="2a5di7"
query MemberByEmail {
  v_member_report(where: { email: { _eq: "wira123widodo@gmail.com" } }) {
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

Hasura menampilkan member dengan email yang sesuai.

---

# 16. Query Member Berdasarkan Keyword Nama

Query ini menampilkan member berdasarkan keyword nama.

```graphql id="fh2vis"
query SearchMemberByName {
  v_member_report(
    where: { name: { _ilike: "%Ahmad%" } }
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

Hasura menampilkan member yang namanya mengandung keyword pencarian.

---

# 17. Query Member Berdasarkan Source User ID

Query ini digunakan untuk melihat data member berdasarkan `source_user_id` dari Auth Service.

```graphql id="b5i49r"
query MemberBySourceUserId {
  v_member_report(where: { source_user_id: { _eq: 2 } }) {
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

Hasura menampilkan member dengan `source_user_id` yang sesuai.

---

# 18. Query Member dengan Detail Lengkap

Query ini menggunakan table `report_members`, bukan view, agar field `address`, `source_created_at`, dan `source_updated_at` dapat ditampilkan.

```graphql id="xcdpcg"
query MemberDetailReport {
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

Hasura menampilkan data member lengkap dari table reporting.

---

# 19. Query Summary Jumlah Field Berdasarkan Status

Query ini menggunakan aggregation untuk menghitung jumlah data field.

```graphql id="s2nh90"
query FieldStatusSummary {
  total_fields: report_fields_aggregate {
    aggregate {
      count
    }
  }

  available_fields: report_fields_aggregate(
    where: { status: { _eq: "available" } }
  ) {
    aggregate {
      count
    }
  }

  maintenance_fields: report_fields_aggregate(
    where: { status: { _eq: "maintenance" } }
  ) {
    aggregate {
      count
    }
  }

  inactive_fields: report_fields_aggregate(
    where: { status: { _eq: "inactive" } }
  ) {
    aggregate {
      count
    }
  }
}
```

## Expected Result

Hasura menampilkan total lapangan dan jumlah lapangan berdasarkan status.

---

# 20. Query Summary Jumlah Member Berdasarkan Status

Query ini menggunakan aggregation untuk menghitung jumlah data member.

```graphql id="p1a927"
query MemberStatusSummary {
  total_members: report_members_aggregate {
    aggregate {
      count
    }
  }

  active_members: report_members_aggregate(
    where: { status: { _eq: "active" } }
  ) {
    aggregate {
      count
    }
  }

  inactive_members: report_members_aggregate(
    where: { status: { _eq: "inactive" } }
  ) {
    aggregate {
      count
    }
  }

  blocked_members: report_members_aggregate(
    where: { status: { _eq: "blocked" } }
  ) {
    aggregate {
      count
    }
  }
}
```

## Expected Result

Hasura menampilkan total member dan jumlah member berdasarkan status.

---

# 21. Query Field dan Member Summary dari View Dashboard

Jika ingin mengambil ringkasan field dan member secara lebih ringkas, gunakan view `v_dashboard_summary`.

```graphql id="t2heyo"
query FieldMemberDashboardSummary {
  v_dashboard_summary {
    fields_total
    fields_available
    fields_maintenance
    fields_inactive

    members_total
    members_active
    members_inactive
    members_blocked
  }
}
```

## Expected Result

Hasura menampilkan summary field dan member dari view dashboard.

---

# 22. Catatan Penggunaan

Gunakan `v_field_report` dan `v_member_report` untuk kebutuhan laporan ringkas.

Gunakan `report_fields` dan `report_members` jika membutuhkan data lengkap yang tidak tersedia pada view.

Field status lapangan yang digunakan:

```text id="3ulv9i"
available
maintenance
inactive
```

Status member yang digunakan:

```text id="zspzyl"
active
inactive
blocked
```

## Kesimpulan

Query pada file ini digunakan untuk membuktikan bahwa Hasura dapat membaca dan memfilter data reporting lapangan serta member.

Dengan query ini, Hasura dapat digunakan untuk:

```text id="rlwosv"
1. Melihat daftar lapangan.
2. Memfilter lapangan berdasarkan status, tipe, lokasi, dan harga.
3. Melihat daftar member.
4. Memfilter member berdasarkan status, email, nama, dan source user id.
5. Mengambil summary field dan member.
```
