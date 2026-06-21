# GraphQL Gateway Testing Guide

Dokumen ini berisi panduan testing GraphQL Gateway manual pada project Retobluto Arena Microservices.

GraphQL Gateway dibuat menggunakan Laravel dan berfungsi sebagai gateway manual yang meneruskan query/mutation GraphQL ke REST API service terkait.

## Endpoint GraphQL Gateway

Docker Mode:

```text
http://localhost:8010
```

Local/XAMPP Mode:

```text
http://127.0.0.1:8010
```

## Daftar Endpoint

| Method | Endpoint              | Keterangan                                               |
| ------ | --------------------- | -------------------------------------------------------- |
| GET    | `/playground`         | Halaman playground untuk testing GraphQL melalui browser |
| GET    | `/api/health`         | Health check GraphQL Gateway                             |
| POST   | `/api/graphql`        | Endpoint utama GraphQL                                   |
| GET    | `/api/graphql/schema` | Melihat schema GraphQL                                   |

## Catatan Penting Endpoint GraphQL

Endpoint berikut hanya menerima method `POST`:

```text
/api/graphql
```

Jika endpoint ini dibuka langsung melalui browser:

```text
http://localhost:8010/api/graphql
```

maka browser akan mengirim request `GET`, sehingga Laravel menampilkan pesan:

```text
The GET method is not supported for route api/graphql. Supported methods: POST.
```

Hal tersebut normal. Untuk testing melalui browser, gunakan:

```text
http://localhost:8010/playground
```

## Cara Menggunakan Playground

Buka:

```text
http://localhost:8010/playground
```

Pada halaman playground:

```text
1. Masukkan query atau mutation GraphQL.
2. Jika endpoint membutuhkan token, masukkan token pada field token.
3. Klik Run Query.
4. Response akan tampil pada panel output.
```

Format token:

```text
Bearer <access_token>
```

Jika token dimasukkan tanpa teks `Bearer`, playground akan menambahkan prefix tersebut secara otomatis.

## Cara Mendapatkan Token Admin

Token admin dapat diperoleh dari Auth Service.

Contoh menggunakan PowerShell:

```powershell
$loginBody = @{
  email = "admin@retobluto.test"
  password = "password"
} | ConvertTo-Json

$login = Invoke-RestMethod `
  -Uri "http://localhost:8001/api/admin/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body $loginBody

$adminToken = $login.access_token
$adminToken
```

Token tersebut dapat dipakai untuk query/mutation yang membutuhkan akses admin.

## Cara Mendapatkan Token Member

Token member dapat diperoleh dari Auth Service.

Contoh menggunakan akun member active:

```powershell
$loginBody = @{
  email = "wira123widodo@gmail.com"
  password = "password"
} | ConvertTo-Json

$login = Invoke-RestMethod `
  -Uri "http://localhost:8001/api/member/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body $loginBody

$memberToken = $login.access_token
$memberToken
```

Token tersebut dapat dipakai untuk query/mutation yang membutuhkan akses member.

## Cara Test GraphQL Endpoint Menggunakan PowerShell

Selain melalui playground, endpoint GraphQL dapat dites menggunakan PowerShell.

```powershell
$body = @{
  query = "query { health { auth { ok status message } member { ok status message } field { ok status message } booking { ok status message } notification { ok status message } } }"
} | ConvertTo-Json

Invoke-RestMethod `
  -Uri "http://localhost:8010/api/graphql" `
  -Method POST `
  -ContentType "application/json" `
  -Body $body
```

Jika query membutuhkan token:

```powershell
$body = @{
  query = "query { me { success message user { id name email role } errors } }"
} | ConvertTo-Json

Invoke-RestMethod `
  -Uri "http://localhost:8010/api/graphql" `
  -Method POST `
  -Headers @{ Authorization = "Bearer $adminToken" } `
  -ContentType "application/json" `
  -Body $body
```

---

# 1. Query Health

Query ini digunakan untuk mengecek status service yang terhubung ke GraphQL Gateway.

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
GraphQL Gateway menampilkan status health dari Auth Service, Member Service, Field Service, Booking Service, dan Notification Service.
```

---

# 2. Query Me

Query ini membutuhkan token.

```graphql
query {
  me {
    success
    message
    user {
      id
      name
      email
      role
      is_verified
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan data user berdasarkan token yang dikirim.
```

---

# 3. Query Validate Token

Query ini membutuhkan token.

```graphql
query {
  validateToken {
    success
    message
    valid
    user {
      id
      name
      email
      role
      is_verified
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway melakukan validasi token melalui Auth Service.
Jika token valid, field valid bernilai true.
```

---

# 4. Query Fields

Query ini digunakan untuk mengambil daftar lapangan dari Field Service.

```graphql
query {
  fields {
    id
    name
    type
    description
    location
    price_per_hour
    status
    open_time
    close_time
    created_at
    updated_at
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar lapangan dari Field Service.
```

## Query Fields dengan Filter

```graphql
query {
  fields(status: "available", type: "Futsal") {
    id
    name
    type
    location
    price_per_hour
    status
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar lapangan sesuai filter status dan type.
```

## Query Fields dengan Search

```graphql
query {
  fields(search: "Futsal") {
    id
    name
    type
    location
    status
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar lapangan berdasarkan keyword pencarian.
```

---

# 5. Query Available Fields

Query ini mengambil daftar lapangan yang tersedia untuk booking.

```graphql
query {
  availableFields {
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

Expected result:

```text
GraphQL Gateway menampilkan lapangan dengan status available.
```

---

# 6. Query Field Detail

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
    created_at
    updated_at
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan detail lapangan berdasarkan id.
```

---

# 7. Query Field Schedule

Query ini digunakan untuk melihat jadwal booking pada lapangan tertentu melalui Field Service.

```graphql
query {
  fieldSchedule(id: 1, date: "2026-06-30")
}
```

Expected result:

```text
GraphQL Gateway menampilkan jadwal booking lapangan berdasarkan field id dan tanggal.
```

Catatan:

```text
Gunakan tanggal setelah hari pengujian agar sesuai dengan skenario testing.
```

---

# 8. Mutation Create Field

Mutation ini membutuhkan token admin.

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
      location
      price_per_hour
      status
      open_time
      close_time
    }
    errors
  }
}
```

Expected result:

```text
Lapangan berhasil dibuat melalui GraphQL Gateway jika token admin valid dan data valid.
```

Validasi yang dilakukan pada GraphQL Gateway:

```text
name wajib diisi
type wajib diisi dan harus salah satu dari tipe yang diizinkan
price_per_hour wajib berupa angka minimal 0
status harus available, maintenance, atau inactive
open_time dan close_time harus berformat HH:mm
close_time harus lebih besar dari open_time
```

---

# 9. Mutation Update Field

Mutation ini membutuhkan token admin.

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
      type
      location
      price_per_hour
      status
    }
    errors
  }
}
```

Expected result:

```text
Data lapangan berhasil diperbarui melalui GraphQL Gateway.
```

---

# 10. Mutation Update Field Status

Mutation ini membutuhkan token admin.

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

Expected result:

```text
Status lapangan berhasil diperbarui.
```

Status yang valid:

```text
available
maintenance
inactive
```

---

# 11. Mutation Delete Field

Mutation ini membutuhkan token admin.

```graphql
mutation {
  deleteField(id: 1) {
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

Expected result:

```text
Lapangan berhasil dihapus jika tidak melanggar aturan pada Field Service.
```

---

# 12. Query Members

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
      address
      status
      created_at
      updated_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar member dari Member Service.
```

## Query Members dengan Filter Status

```graphql
query {
  members(status: "active") {
    success
    message
    members {
      id
      name
      email
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan member berdasarkan status.
```

Status yang valid pada member:

```text
active
inactive
blocked
```

## Query Members dengan Search

```graphql
query {
  members(search: "wira") {
    success
    message
    members {
      id
      name
      email
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan member sesuai keyword pencarian.
```

---

# 13. Query Member Detail

Query ini membutuhkan token admin.

```graphql
query {
  member(id: 1) {
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
      created_at
      updated_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan detail member berdasarkan id.
```

---

# 14. Query Member by User ID

Query ini membutuhkan token.

```graphql
query {
  memberByUserId(user_id: 2) {
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

Expected result:

```text
GraphQL Gateway menampilkan data member berdasarkan user_id.
```

---

# 15. Query My Profile

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
      created_at
      updated_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan profil member yang sedang login.
```

---

# 16. Mutation Admin Create Member

Mutation ini membutuhkan token admin.

```graphql
mutation {
  adminCreateMember(
    name: "Member GraphQL Test"
    email: "member.graphql.test@example.com"
    password: "password"
    phone: "081299990001"
    address: "Surabaya"
  ) {
    success
    message
    user {
      id
      name
      email
      role
      is_verified
    }
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

Expected result:

```text
Akun member berhasil dibuat melalui Auth Service dan data profile member tersinkron ke Member Service.
```

---

# 17. Mutation Update Profile

Mutation ini membutuhkan token member.

```graphql
mutation {
  updateProfile(
    name: "Ahmad Aziz Wira Widodo Updated"
    phone: "081234567890"
    address: "Surabaya"
  ) {
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

Expected result:

```text
Member berhasil memperbarui profil miliknya.
```

---

# 18. Mutation Update Member

Mutation ini membutuhkan token admin.

```graphql
mutation {
  updateMember(
    id: 1
    user_id: 2
    name: "Ahmad Aziz Wira Widodo"
    email: "wira123widodo@gmail.com"
    phone: "081234567890"
    address: "Surabaya"
    status: "active"
  ) {
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

Expected result:

```text
Admin berhasil memperbarui data member.
```

---

# 19. Mutation Update Member Status

Mutation ini membutuhkan token admin.

```graphql
mutation {
  updateMemberStatus(id: 1, status: "inactive") {
    success
    message
    member {
      id
      name
      email
      status
    }
    errors
  }
}
```

Expected result:

```text
Status member berhasil diperbarui.
```

Status yang valid:

```text
active
inactive
blocked
```

---

# 20. Mutation Delete Member

Mutation ini membutuhkan token admin.

```graphql
mutation {
  deleteMember(id: 1) {
    success
    message
    member {
      id
      name
      email
      status
    }
    errors
  }
}
```

Expected result:

```text
Data member berhasil dihapus jika service mengizinkan proses penghapusan.
```

---

# 21. Mutation Delete Member Auth Account

Mutation ini membutuhkan token admin.

```graphql
mutation {
  deleteMemberAuthAccount(email: "member.graphql.test@example.com") {
    success
    message
    data
    errors
  }
}
```

Alternatif menggunakan `user_id`:

```graphql
mutation {
  deleteMemberAuthAccount(user_id: 7) {
    success
    message
    data
    errors
  }
}
```

Expected result:

```text
Akun auth member berhasil dihapus berdasarkan email atau user_id.
```

---

# 22. Query Bookings

Query ini membutuhkan token admin.

```graphql
query {
  bookings {
    success
    message
    bookings {
      id
      member_id
      member_user_id
      member_name
      member_email
      field_id
      field_name
      field_type
      booking_date
      start_time
      end_time
      duration_hours
      price_per_hour
      total_price
      status
      note
      approved_by
      rejected_by
      canceled_by
      created_at
      updated_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar booking dari Booking Service.
```

## Query Bookings dengan Filter Status

```graphql
query {
  bookings(status: "approved") {
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

Expected result:

```text
GraphQL Gateway menampilkan booking berdasarkan status.
```

Status booking yang valid:

```text
pending
approved
rejected
canceled
```

## Query Bookings dengan Filter Tanggal

```graphql
query {
  bookings(booking_date: "2026-06-30") {
    success
    message
    bookings {
      id
      member_name
      field_name
      booking_date
      start_time
      end_time
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan booking berdasarkan tanggal.
```

---

# 23. Query Booking Detail

Query ini membutuhkan token.

```graphql
query {
  booking(id: 1) {
    success
    message
    booking {
      id
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
      note
      rejection_reason
      approved_by
      rejected_by
      canceled_by
      approved_at
      rejected_at
      canceled_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan detail booking berdasarkan id.
```

---

# 24. Query My Bookings

Query ini membutuhkan token member.

```graphql
query {
  myBookings {
    success
    message
    bookings {
      id
      field_name
      field_type
      booking_date
      start_time
      end_time
      duration_hours
      total_price
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan booking aktif milik member login.
```

---

# 25. Query My Booking History

Query ini membutuhkan token member.

```graphql
query {
  myBookingHistory {
    success
    message
    bookings {
      id
      field_name
      field_type
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

Expected result:

```text
GraphQL Gateway menampilkan riwayat booking milik member login.
```

---

# 26. Query Booking Requests

Query ini membutuhkan token admin.

```graphql
query {
  bookingRequests {
    success
    message
    bookings {
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
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar booking request dengan status pending.
```

---

# 27. Query Field Booking Schedule

```graphql
query {
  fieldBookingSchedule(field_id: 1, date: "2026-06-30", status: "approved") {
    success
    message
    bookings {
      id
      member_name
      field_name
      booking_date
      start_time
      end_time
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan jadwal booking berdasarkan field_id, tanggal, dan status.
```

---

# 28. Query Bookings by Member

Query ini membutuhkan token admin.

```graphql
query {
  bookingsByMember(member_id: 1) {
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

Expected result:

```text
GraphQL Gateway menampilkan daftar booking berdasarkan member_id.
```

---

# 29. Mutation Create Booking

Mutation ini membutuhkan token member active.

```graphql
mutation {
  createBooking(
    field_id: 1
    booking_date: "2026-06-30"
    start_time: "09:00"
    end_time: "11:00"
    note: "Booking dari GraphQL Gateway"
  ) {
    success
    message
    booking {
      id
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
    }
    errors
  }
}
```

Expected result:

```text
Booking berhasil dibuat jika token member valid, member active, lapangan available, tanggal valid, jam valid, dan tidak ada konflik jadwal.
```

Catatan:

```text
Gunakan booking_date dengan tanggal setelah hari pengujian.
Tanggal tidak boleh sebelum hari ini.
Jam selesai harus lebih besar dari jam mulai.
```

---

# 30. Mutation Approve Booking

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

Expected result:

```text
Booking berhasil di-approve jika token admin valid dan booking masih bisa disetujui.
```

---

# 31. Mutation Reject Booking

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

Expected result:

```text
Booking berhasil ditolak dan alasan reject tersimpan.
```

Catatan:

```text
rejection_reason maksimal 500 karakter.
```

---

# 32. Mutation Cancel Booking

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

Expected result:

```text
Booking berhasil dibatalkan jika token member valid dan booking masih dapat dibatalkan.
```

---

# 33. Query Notification Logs

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
      message
      status
      sent_at
      error_message
      created_at
      updated_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan daftar log notifikasi dari Notification Service.
```

## Query Notification Logs dengan Filter

```graphql
query {
  notificationLogs(status: "sent", type: "otp") {
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

Expected result:

```text
GraphQL Gateway menampilkan log notifikasi sesuai filter status dan type.
```

## Query Notification Logs dengan Search

```graphql
query {
  notificationLogs(search: "booking") {
    success
    message
    logs {
      id
      recipient_email
      type
      subject
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan log notifikasi berdasarkan keyword pencarian.
```

---

# 34. Query Notification Log Detail

Query ini membutuhkan token admin.

```graphql
query {
  notificationLog(id: 1) {
    success
    message
    log {
      id
      recipient_email
      type
      subject
      message
      status
      payload
      sent_at
      error_message
      created_at
      updated_at
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan detail log notifikasi berdasarkan id.
```

---

# 35. Query Dashboard Summary

Query ini membutuhkan token admin untuk menghitung data member, booking, dan notification. Jika token tidak diberikan, data field tetap dapat dihitung, tetapi summary lain akan terbatas.

```graphql
query {
  dashboardSummary {
    success
    message
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
    notifications_total
    notifications_sent
    notifications_failed
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway menampilkan summary dashboard berdasarkan data dari Field Service, Member Service, Booking Service, dan Notification Service.
```

---

# 36. Testing Validasi GraphQL Gateway

GraphQL Gateway memiliki validasi input sebelum meneruskan request ke service.

## Contoh Validasi Tipe Lapangan

```graphql
mutation {
  createField(
    name: "Lapangan Salah"
    type: "Golf"
    description: "Tipe tidak valid"
    location: "Test"
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
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway mengembalikan error karena type hanya boleh Futsal, Badminton, Basket, Tenis, Mini Soccer, atau Voli.
```

## Contoh Validasi Booking Date Lampau

```graphql
mutation {
  createBooking(
    field_id: 1
    booking_date: "2020-01-01"
    start_time: "09:00"
    end_time: "11:00"
    note: "Tanggal lampau"
  ) {
    success
    message
    booking {
      id
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway mengembalikan error karena tanggal booking tidak boleh sebelum hari ini.
```

## Contoh Validasi Jam Booking

```graphql
mutation {
  createBooking(
    field_id: 1
    booking_date: "2026-06-30"
    start_time: "11:00"
    end_time: "09:00"
    note: "Jam tidak valid"
  ) {
    success
    message
    booking {
      id
      status
    }
    errors
  }
}
```

Expected result:

```text
GraphQL Gateway mengembalikan error karena jam selesai harus lebih besar dari jam mulai.
```

---

# 37. Batasan GraphQL Gateway

GraphQL Gateway pada project ini adalah implementasi manual berbasis Laravel.

Catatan batasan:

```text
1. Endpoint /api/graphql hanya menerima POST.
2. Playground digunakan untuk testing melalui browser.
3. Query dan mutation ditulis secara langsung pada field query.
4. Beberapa query/mutation membutuhkan token admin atau member.
5. GraphQL Gateway tidak menyimpan data utama.
6. GraphQL Gateway meneruskan request ke REST API microservices.
7. Hasura tetap digunakan terpisah untuk reporting/read-only query.
```

## Query yang Tersedia

```text
health
me
validateToken
fields
availableFields
field
fieldSchedule
bookings
booking
myBookings
myBookingHistory
bookingRequests
fieldBookingSchedule
bookingsByMember
members
member
memberByUserId
myProfile
notificationLogs
notificationLog
dashboardSummary
```

## Mutation yang Tersedia

```text
createField
updateField
updateFieldStatus
deleteField
createBooking
approveBooking
rejectBooking
cancelBooking
adminCreateMember
deleteMemberAuthAccount
updateProfile
updateMember
updateMemberStatus
deleteMember
```

---

# 38. Kesimpulan Testing GraphQL

Jika seluruh query dan mutation yang dibutuhkan berhasil dijalankan, maka GraphQL Gateway sudah memenuhi fungsi utama sebagai gateway manual yang menghubungkan GraphQL request ke REST API microservices.

GraphQL Gateway digunakan untuk:

```text
1. Health check service.
2. Query data field.
3. Query dan mutasi data booking.
4. Query dan mutasi data member.
5. Query data notification log.
6. Query dashboard summary.
7. Membuktikan implementasi GraphQL manual berbasis Laravel.
```
