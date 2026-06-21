<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('notification_logs')->truncate();

        DB::table('notification_logs')->insert([
            [
                'id' => 1,
                'recipient_email' => 'wira123widodo@gmail.com',
                'type' => 'otp',
                'subject' => 'Kode OTP Registrasi Retobluto Arena',
                'message' => 'Halo Ahmad Aziz Wira Widodo, kode OTP Anda telah dikirim untuk verifikasi akun.',
                'status' => 'sent',
                'payload' => json_encode([
                    'name' => 'Ahmad Aziz Wira Widodo',
                    'email' => 'wira123widodo@gmail.com',
                    'otp' => '123456',
                    'expired_at' => $now->copy()->subDays(5)->addMinutes(10)->toDateTimeString(),
                ]),
                'sent_at' => $now->copy()->subDays(5),
                'error_message' => null,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => 2,
                'recipient_email' => 'auraiftitahh@gmail.com',
                'type' => 'member_registered',
                'subject' => 'Akun Member Retobluto Arena Berhasil Dibuat',
                'message' => 'Halo Aura Iftitah, akun member Anda berhasil dibuat dan siap digunakan.',
                'status' => 'sent',
                'payload' => json_encode([
                    'name' => 'Aura Iftitah',
                    'email' => 'auraiftitahh@gmail.com',
                    'status' => 'active',
                ]),
                'sent_at' => $now->copy()->subDays(4),
                'error_message' => null,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id' => 3,
                'recipient_email' => 'wira123widodo@gmail.com',
                'type' => 'booking_created',
                'subject' => 'Booking Lapangan Dibuat',
                'message' => 'Booking Anda untuk Lapangan Futsal A berhasil dibuat dan menunggu persetujuan admin.',
                'status' => 'sent',
                'payload' => json_encode([
                    'booking_id' => 1,
                    'member_name' => 'Ahmad Aziz Wira Widodo',
                    'field_name' => 'Lapangan Futsal A',
                    'booking_date' => now()->addDay()->toDateString(),
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                    'status' => 'pending',
                ]),
                'sent_at' => $now->copy()->subHours(5),
                'error_message' => null,
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'id' => 4,
                'recipient_email' => 'auraiftitahh@gmail.com',
                'type' => 'booking_created',
                'subject' => 'Booking Lapangan Dibuat',
                'message' => 'Booking Anda untuk Lapangan Badminton A berhasil dibuat dan menunggu persetujuan admin.',
                'status' => 'sent',
                'payload' => json_encode([
                    'booking_id' => 2,
                    'member_name' => 'Aura Iftitah',
                    'field_name' => 'Lapangan Badminton A',
                    'booking_date' => now()->addDays(2)->toDateString(),
                    'start_time' => '13:00',
                    'end_time' => '15:00',
                    'status' => 'pending',
                ]),
                'sent_at' => $now->copy()->subHours(4),
                'error_message' => null,
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(4),
            ],
            [
                'id' => 5,
                'recipient_email' => 'muhammadagilhidayahtullah295@gmail.com',
                'type' => 'booking_approved',
                'subject' => 'Booking Lapangan Disetujui',
                'message' => 'Booking Anda untuk Lapangan Futsal B telah disetujui oleh admin.',
                'status' => 'sent',
                'payload' => json_encode([
                    'booking_id' => 3,
                    'member_name' => 'Muhammad Agil Hidayahtullah',
                    'field_name' => 'Lapangan Futsal B',
                    'booking_date' => now()->subDay()->toDateString(),
                    'start_time' => '19:00',
                    'end_time' => '21:00',
                    'status' => 'approved',
                ]),
                'sent_at' => $now->copy()->subHours(20),
                'error_message' => null,
                'created_at' => $now->copy()->subHours(20),
                'updated_at' => $now->copy()->subHours(20),
            ],
            [
                'id' => 6,
                'recipient_email' => 'ryanalfin6@gmail.com',
                'type' => 'booking_rejected',
                'subject' => 'Booking Lapangan Ditolak',
                'message' => 'Booking Anda untuk Lapangan Basket A ditolak karena lapangan sedang maintenance.',
                'status' => 'sent',
                'payload' => json_encode([
                    'booking_id' => 4,
                    'member_name' => 'Ryan Alvin Saputra',
                    'field_name' => 'Lapangan Basket A',
                    'booking_date' => now()->subDays(2)->toDateString(),
                    'start_time' => '10:00',
                    'end_time' => '12:00',
                    'status' => 'rejected',
                    'rejection_reason' => 'Lapangan sedang maintenance pada jadwal tersebut.',
                ]),
                'sent_at' => $now->copy()->subDays(2)->addHours(2),
                'error_message' => null,
                'created_at' => $now->copy()->subDays(2)->addHours(2),
                'updated_at' => $now->copy()->subDays(2)->addHours(2),
            ],
            [
                'id' => 7,
                'recipient_email' => 'wira123widodo@gmail.com',
                'type' => 'booking_canceled',
                'subject' => 'Booking Lapangan Dibatalkan',
                'message' => 'Booking Anda untuk Lapangan Mini Soccer A telah dibatalkan.',
                'status' => 'sent',
                'payload' => json_encode([
                    'booking_id' => 5,
                    'member_name' => 'Ahmad Aziz Wira Widodo',
                    'field_name' => 'Lapangan Mini Soccer A',
                    'booking_date' => now()->subDays(3)->toDateString(),
                    'start_time' => '16:00',
                    'end_time' => '18:00',
                    'status' => 'canceled',
                ]),
                'sent_at' => $now->copy()->subDays(3)->addHour(),
                'error_message' => null,
                'created_at' => $now->copy()->subDays(3)->addHour(),
                'updated_at' => $now->copy()->subDays(3)->addHour(),
            ],
            [
                'id' => 8,
                'recipient_email' => 'dimas.member@example.com',
                'type' => 'email',
                'subject' => 'Informasi Status Akun Member',
                'message' => 'Halo Dimas Pratama Wijaya, akun Anda sedang dalam status blocked sehingga belum dapat melakukan booking.',
                'status' => 'failed',
                'payload' => json_encode([
                    'recipient_email' => 'dimas.member@example.com',
                    'subject' => 'Informasi Status Akun Member',
                    'message' => 'Akun Anda sedang dalam status blocked.',
                ]),
                'sent_at' => null,
                'error_message' => 'Simulasi kegagalan pengiriman email untuk kebutuhan testing log failed.',
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
        ]);
    }
}
