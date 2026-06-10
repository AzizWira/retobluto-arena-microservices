-- Retobluto Arena Hasura Local Reporting Schema
-- Hasura digunakan sebagai GraphQL Engine untuk reporting/read-only query.

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

INSERT INTO report_fields (
    source_field_id,
    name,
    type,
    description,
    location,
    price_per_hour,
    status,
    open_time,
    close_time
) VALUES
(1, 'Lapangan Futsal A', 'Futsal', 'Lapangan utama untuk futsal', 'Arena Utama', 120000, 'available', '08:00', '22:00'),
(2, 'Lapangan Badminton A', 'Badminton', 'Lapangan indoor badminton', 'Gedung Indoor', 75000, 'available', '08:00', '21:00'),
(3, 'Lapangan Basket A', 'Basket', 'Lapangan basket outdoor', 'Area Outdoor', 100000, 'maintenance', '09:00', '22:00');

INSERT INTO report_members (
    source_member_id,
    source_user_id,
    name,
    email,
    phone,
    address,
    status
) VALUES
(1, 11, 'Member Demo Aktif', 'member.demo@example.com', '081234567890', 'Surabaya', 'active'),
(2, 12, 'Member Demo Inactive', 'inactive.demo@example.com', '081234567891', 'Surabaya', 'inactive'),
(3, 13, 'Member Demo Blocked', 'blocked.demo@example.com', '081234567892', 'Surabaya', 'blocked');

INSERT INTO report_bookings (
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
    price_per_hour,
    total_price,
    status,
    note,
    approved_by,
    rejected_by,
    canceled_by,
    approved_at,
    rejected_at,
    canceled_at
) VALUES
(1, 1, 'Member Demo Aktif', 'member.demo@example.com', 1, 'Lapangan Futsal A', 'Futsal', CURRENT_DATE + INTERVAL '1 day', '09:00', '11:00', 2, 120000, 240000, 'pending', 'Booking pending demo', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'Member Demo Aktif', 'member.demo@example.com', 2, 'Lapangan Badminton A', 'Badminton', CURRENT_DATE - INTERVAL '1 day', '10:00', '12:00', 2, 75000, 150000, 'approved', 'Booking approved demo', 'Admin Demo', NULL, NULL, CURRENT_TIMESTAMP, NULL, NULL),
(3, 2, 'Member Demo Inactive', 'inactive.demo@example.com', 1, 'Lapangan Futsal A', 'Futsal', CURRENT_DATE - INTERVAL '2 day', '13:00', '15:00', 2, 120000, 240000, 'rejected', 'Booking rejected demo', NULL, 'Admin Demo', NULL, NULL, CURRENT_TIMESTAMP, NULL),
(4, 1, 'Member Demo Aktif', 'member.demo@example.com', 3, 'Lapangan Basket A', 'Basket', CURRENT_DATE - INTERVAL '3 day', '15:00', '16:00', 1, 100000, 100000, 'canceled', 'Booking canceled demo', NULL, NULL, 'Member Demo Aktif', NULL, NULL, CURRENT_TIMESTAMP);

INSERT INTO report_notification_logs (
    source_log_id,
    recipient_email,
    type,
    subject,
    message,
    status,
    error_message,
    sent_at
) VALUES
(1, 'member.demo@example.com', 'otp', 'Kode OTP Registrasi Retobluto Arena', 'Kode OTP berhasil dikirim.', 'sent', NULL, CURRENT_TIMESTAMP),
(2, 'member.demo@example.com', 'booking_approved', 'Booking Disetujui', 'Booking lapangan telah disetujui.', 'sent', NULL, CURRENT_TIMESTAMP),
(3, 'inactive.demo@example.com', 'booking_rejected', 'Booking Ditolak', 'Booking lapangan ditolak.', 'sent', NULL, CURRENT_TIMESTAMP),
(4, 'failed.demo@example.com', 'email', 'Email Gagal', 'Simulasi email gagal.', 'failed', 'SMTP connection failed', NULL);