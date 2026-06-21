-- Retobluto Arena Hasura Local Reporting Schema
-- Hasura digunakan sebagai GraphQL Engine untuk reporting/read-only query.
-- Data pada file ini diselaraskan dengan seeder demo service utama.

DROP VIEW IF EXISTS v_dashboard_summary;
DROP VIEW IF EXISTS v_booking_report;
DROP VIEW IF EXISTS v_field_report;
DROP VIEW IF EXISTS v_member_report;
DROP VIEW IF EXISTS v_notification_report;

DROP TABLE IF EXISTS report_notification_logs;
DROP TABLE IF EXISTS report_bookings;
DROP TABLE IF EXISTS report_members;
DROP TABLE IF EXISTS report_fields;

CREATE TABLE report_fields (
id SERIAL PRIMARY KEY,
source_field_id INTEGER,
name VARCHAR(150) NOT NULL,
type VARCHAR(50) NOT NULL,
description TEXT,
location VARCHAR(150),
price_per_hour NUMERIC(12, 2) NOT NULL DEFAULT 0,
status VARCHAR(30) NOT NULL DEFAULT 'available',
open_time TIME,
close_time TIME,
source_created_at TIMESTAMP NULL,
source_updated_at TIMESTAMP NULL,
synced_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE report_members (
id SERIAL PRIMARY KEY,
source_member_id INTEGER,
source_user_id INTEGER,
name VARCHAR(100) NOT NULL,
email VARCHAR(150) NOT NULL,
phone VARCHAR(30),
address TEXT,
status VARCHAR(30) NOT NULL DEFAULT 'inactive',
source_created_at TIMESTAMP NULL,
source_updated_at TIMESTAMP NULL,
synced_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE report_bookings (
id SERIAL PRIMARY KEY,
source_booking_id INTEGER,
source_member_id INTEGER,
source_member_user_id INTEGER,
member_name VARCHAR(100),
member_email VARCHAR(150),
source_field_id INTEGER,
field_name VARCHAR(150),
field_type VARCHAR(50),
booking_date DATE NOT NULL,
start_time TIME NOT NULL,
end_time TIME NOT NULL,
duration_hours INTEGER DEFAULT 0,
price_per_hour NUMERIC(12, 2) DEFAULT 0,
total_price NUMERIC(12, 2) DEFAULT 0,
status VARCHAR(30) NOT NULL DEFAULT 'pending',
note TEXT,
rejection_reason TEXT,
approved_by VARCHAR(100),
rejected_by VARCHAR(100),
canceled_by VARCHAR(100),
approved_at TIMESTAMP NULL,
rejected_at TIMESTAMP NULL,
canceled_at TIMESTAMP NULL,
source_created_at TIMESTAMP NULL,
source_updated_at TIMESTAMP NULL,
synced_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE report_notification_logs (
id SERIAL PRIMARY KEY,
source_log_id INTEGER,
recipient_email VARCHAR(150),
type VARCHAR(50),
subject VARCHAR(255),
message TEXT,
status VARCHAR(30) NOT NULL DEFAULT 'sent',
error_message TEXT,
sent_at TIMESTAMP NULL,
source_created_at TIMESTAMP NULL,
source_updated_at TIMESTAMP NULL,
synced_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE VIEW v_field_report AS
SELECT
id,
source_field_id,
name,
type,
location,
price_per_hour,
status,
open_time,
close_time,
synced_at
FROM report_fields;

CREATE VIEW v_member_report AS
SELECT
id,
source_member_id,
source_user_id,
name,
email,
phone,
status,
synced_at
FROM report_members;

CREATE VIEW v_booking_report AS
SELECT
id,
source_booking_id,
source_member_id,
member_name,
member_email,
source_field_id,
field_name,
field_type,
booking_date,
start_time,
end_time,
duration_hours,
total_price,
status,
approved_by,
rejected_by,
canceled_by,
synced_at
FROM report_bookings;

CREATE VIEW v_notification_report AS
SELECT
id,
source_log_id,
recipient_email,
type,
subject,
status,
sent_at,
synced_at
FROM report_notification_logs;

CREATE VIEW v_dashboard_summary AS
SELECT
(SELECT COUNT(*) FROM report_fields) AS fields_total,
(SELECT COUNT(*) FROM report_fields WHERE status = 'available') AS fields_available,
(SELECT COUNT(*) FROM report_fields WHERE status = 'maintenance') AS fields_maintenance,
(SELECT COUNT(*) FROM report_fields WHERE status = 'inactive') AS fields_inactive,

```
(SELECT COUNT(*) FROM report_members) AS members_total,
(SELECT COUNT(*) FROM report_members WHERE status = 'active') AS members_active,
(SELECT COUNT(*) FROM report_members WHERE status = 'inactive') AS members_inactive,
(SELECT COUNT(*) FROM report_members WHERE status = 'blocked') AS members_blocked,

(SELECT COUNT(*) FROM report_bookings) AS bookings_total,
(SELECT COUNT(*) FROM report_bookings WHERE status = 'pending') AS bookings_pending,
(SELECT COUNT(*) FROM report_bookings WHERE status = 'approved') AS bookings_approved,
(SELECT COUNT(*) FROM report_bookings WHERE status = 'rejected') AS bookings_rejected,
(SELECT COUNT(*) FROM report_bookings WHERE status = 'canceled') AS bookings_canceled,

(SELECT COALESCE(SUM(total_price), 0) FROM report_bookings WHERE status = 'approved') AS approved_revenue_total,

(SELECT COUNT(*) FROM report_notification_logs) AS notifications_total,
(SELECT COUNT(*) FROM report_notification_logs WHERE status = 'sent') AS notifications_sent,
(SELECT COUNT(*) FROM report_notification_logs WHERE status = 'failed') AS notifications_failed;
```

INSERT INTO report_fields (
source_field_id,
name,
type,
description,
location,
price_per_hour,
status,
open_time,
close_time,
source_created_at,
source_updated_at
) VALUES
(
1,
'Lapangan Futsal A',
'Futsal',
'Lapangan futsal indoor dengan rumput sintetis dan pencahayaan standar malam.',
'Area Indoor 1',
150000,
'available',
'08:00:00',
'22:00:00',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
2,
'Lapangan Futsal B',
'Futsal',
'Lapangan futsal indoor standar turnamen dengan tribun kecil.',
'Area Indoor 2',
175000,
'available',
'08:00:00',
'22:00:00',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
3,
'Lapangan Badminton A',
'Badminton',
'Lapangan badminton indoor dengan lantai vinyl.',
'Hall Badminton 1',
75000,
'available',
'07:00:00',
'21:00:00',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
4,
'Lapangan Basket A',
'Basket',
'Lapangan basket outdoor yang sedang dijadwalkan perawatan ring.',
'Area Outdoor 1',
200000,
'maintenance',
'08:00:00',
'20:00:00',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
5,
'Lapangan Mini Soccer A',
'Mini Soccer',
'Lapangan mini soccer outdoor dengan rumput sintetis.',
'Area Outdoor 2',
250000,
'available',
'08:00:00',
'23:00:00',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
6,
'Lapangan Voli A',
'Voli',
'Lapangan voli indoor yang sedang tidak aktif untuk renovasi ringan.',
'Hall Voli 1',
90000,
'inactive',
'08:00:00',
'21:00:00',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
);

INSERT INTO report_members (
source_member_id,
source_user_id,
name,
email,
phone,
address,
status,
source_created_at,
source_updated_at
) VALUES
(
1,
2,
'Ahmad Aziz Wira Widodo',
'[wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)',
'081234567890',
'Surabaya, Jawa Timur',
'active',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
2,
3,
'Aura Iftitah',
'[auraiftitahh@gmail.com](mailto:auraiftitahh@gmail.com)',
'081234567891',
'Sidoarjo, Jawa Timur',
'active',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
3,
4,
'Muhammad Agil Hidayahtullah',
'[muhammadagilhidayahtullah295@gmail.com](mailto:muhammadagilhidayahtullah295@gmail.com)',
'081234567892',
'Gresik, Jawa Timur',
'active',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
4,
5,
'Ryan Alvin Saputra',
'[ryanalfin6@gmail.com](mailto:ryanalfin6@gmail.com)',
'081234567893',
'Mojokerto, Jawa Timur',
'active',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
5,
6,
'Nabila Putri Ramadhani',
'[nabila.member@example.com](mailto:nabila.member@example.com)',
'081234567894',
'Surabaya, Jawa Timur',
'inactive',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
),
(
6,
7,
'Dimas Pratama Wijaya',
'[dimas.member@example.com](mailto:dimas.member@example.com)',
'081234567895',
'Malang, Jawa Timur',
'blocked',
CURRENT_TIMESTAMP,
CURRENT_TIMESTAMP
);

INSERT INTO report_bookings (
source_booking_id,
source_member_id,
source_member_user_id,
member_name,
member_email,
source_field_id,
field_name,
field_type,
booking_date,
start_time,
end_time,
duration_hours,
price_per_hour,
total_price,
status,
note,
rejection_reason,
approved_by,
rejected_by,
canceled_by,
approved_at,
rejected_at,
canceled_at,
source_created_at,
source_updated_at
) VALUES
(
1,
1,
2,
'Ahmad Aziz Wira Widodo',
'[wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)',
1,
'Lapangan Futsal A',
'Futsal',
CURRENT_DATE + INTERVAL '1 day',
'09:00:00',
'11:00:00',
2,
150000,
300000,
'pending',
'Booking request untuk latihan futsal tim.',
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '5 hours',
CURRENT_TIMESTAMP - INTERVAL '5 hours'
),
(
2,
2,
3,
'Aura Iftitah',
'[auraiftitahh@gmail.com](mailto:auraiftitahh@gmail.com)',
3,
'Lapangan Badminton A',
'Badminton',
CURRENT_DATE + INTERVAL '2 days',
'13:00:00',
'15:00:00',
2,
75000,
150000,
'pending',
'Booking request untuk latihan badminton.',
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '4 hours',
CURRENT_TIMESTAMP - INTERVAL '4 hours'
),
(
3,
3,
4,
'Muhammad Agil Hidayahtullah',
'[muhammadagilhidayahtullah295@gmail.com](mailto:muhammadagilhidayahtullah295@gmail.com)',
2,
'Lapangan Futsal B',
'Futsal',
CURRENT_DATE - INTERVAL '1 day',
'19:00:00',
'21:00:00',
2,
175000,
350000,
'approved',
'Booking yang sudah disetujui untuk simulasi riwayat.',
NULL,
'Retobluto Admin',
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '20 hours',
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '2 days',
CURRENT_TIMESTAMP - INTERVAL '20 hours'
),
(
4,
4,
5,
'Ryan Alvin Saputra',
'[ryanalfin6@gmail.com](mailto:ryanalfin6@gmail.com)',
4,
'Lapangan Basket A',
'Basket',
CURRENT_DATE - INTERVAL '2 days',
'10:00:00',
'12:00:00',
2,
200000,
400000,
'rejected',
'Booking pada lapangan yang sedang maintenance.',
'Lapangan sedang maintenance pada jadwal tersebut.',
NULL,
'Retobluto Admin',
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '2 days' + INTERVAL '2 hours',
NULL,
CURRENT_TIMESTAMP - INTERVAL '3 days',
CURRENT_TIMESTAMP - INTERVAL '2 days' + INTERVAL '2 hours'
),
(
5,
1,
2,
'Ahmad Aziz Wira Widodo',
'[wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)',
5,
'Lapangan Mini Soccer A',
'Mini Soccer',
CURRENT_DATE - INTERVAL '3 days',
'16:00:00',
'18:00:00',
2,
250000,
500000,
'canceled',
'Booking yang dibatalkan oleh member.',
NULL,
NULL,
NULL,
'Ahmad Aziz Wira Widodo',
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '3 days' + INTERVAL '1 hour',
CURRENT_TIMESTAMP - INTERVAL '4 days',
CURRENT_TIMESTAMP - INTERVAL '3 days' + INTERVAL '1 hour'
),
(
6,
2,
3,
'Aura Iftitah',
'[auraiftitahh@gmail.com](mailto:auraiftitahh@gmail.com)',
1,
'Lapangan Futsal A',
'Futsal',
CURRENT_DATE + INTERVAL '4 days',
'18:00:00',
'20:00:00',
2,
150000,
300000,
'approved',
'Booking approved untuk jadwal mendatang.',
NULL,
'Retobluto Admin',
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '2 hours',
NULL,
NULL,
CURRENT_TIMESTAMP - INTERVAL '3 hours',
CURRENT_TIMESTAMP - INTERVAL '2 hours'
);

INSERT INTO report_notification_logs (
source_log_id,
recipient_email,
type,
subject,
message,
status,
error_message,
sent_at,
source_created_at,
source_updated_at
) VALUES
(
1,
'[wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)',
'otp',
'Kode OTP Registrasi Retobluto Arena',
'Halo Ahmad Aziz Wira Widodo, kode OTP Anda telah dikirim untuk verifikasi akun.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '5 days',
CURRENT_TIMESTAMP - INTERVAL '5 days',
CURRENT_TIMESTAMP - INTERVAL '5 days'
),
(
2,
'[auraiftitahh@gmail.com](mailto:auraiftitahh@gmail.com)',
'member_registered',
'Akun Member Retobluto Arena Berhasil Dibuat',
'Halo Aura Iftitah, akun member Anda berhasil dibuat dan siap digunakan.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '4 days',
CURRENT_TIMESTAMP - INTERVAL '4 days',
CURRENT_TIMESTAMP - INTERVAL '4 days'
),
(
3,
'[wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)',
'booking_created',
'Booking Lapangan Dibuat',
'Booking Anda untuk Lapangan Futsal A berhasil dibuat dan menunggu persetujuan admin.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '5 hours',
CURRENT_TIMESTAMP - INTERVAL '5 hours',
CURRENT_TIMESTAMP - INTERVAL '5 hours'
),
(
4,
'[auraiftitahh@gmail.com](mailto:auraiftitahh@gmail.com)',
'booking_created',
'Booking Lapangan Dibuat',
'Booking Anda untuk Lapangan Badminton A berhasil dibuat dan menunggu persetujuan admin.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '4 hours',
CURRENT_TIMESTAMP - INTERVAL '4 hours',
CURRENT_TIMESTAMP - INTERVAL '4 hours'
),
(
5,
'[muhammadagilhidayahtullah295@gmail.com](mailto:muhammadagilhidayahtullah295@gmail.com)',
'booking_approved',
'Booking Lapangan Disetujui',
'Booking Anda untuk Lapangan Futsal B telah disetujui oleh admin.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '20 hours',
CURRENT_TIMESTAMP - INTERVAL '20 hours',
CURRENT_TIMESTAMP - INTERVAL '20 hours'
),
(
6,
'[ryanalfin6@gmail.com](mailto:ryanalfin6@gmail.com)',
'booking_rejected',
'Booking Lapangan Ditolak',
'Booking Anda untuk Lapangan Basket A ditolak karena lapangan sedang maintenance.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '2 days' + INTERVAL '2 hours',
CURRENT_TIMESTAMP - INTERVAL '2 days' + INTERVAL '2 hours',
CURRENT_TIMESTAMP - INTERVAL '2 days' + INTERVAL '2 hours'
),
(
7,
'[wira123widodo@gmail.com](mailto:wira123widodo@gmail.com)',
'booking_canceled',
'Booking Lapangan Dibatalkan',
'Booking Anda untuk Lapangan Mini Soccer A telah dibatalkan.',
'sent',
NULL,
CURRENT_TIMESTAMP - INTERVAL '3 days' + INTERVAL '1 hour',
CURRENT_TIMESTAMP - INTERVAL '3 days' + INTERVAL '1 hour',
CURRENT_TIMESTAMP - INTERVAL '3 days' + INTERVAL '1 hour'
),
(
8,
'[dimas.member@example.com](mailto:dimas.member@example.com)',
'email',
'Informasi Status Akun Member',
'Halo Dimas Pratama Wijaya, akun Anda sedang dalam status blocked sehingga belum dapat melakukan booking.',
'failed',
'Simulasi kegagalan pengiriman email untuk kebutuhan testing log failed.',
NULL,
CURRENT_TIMESTAMP - INTERVAL '1 day',
CURRENT_TIMESTAMP - INTERVAL '1 day'
);
