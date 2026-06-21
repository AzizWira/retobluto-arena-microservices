<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('fields')->truncate();

        DB::table('fields')->insert([
            [
                'id' => 1,
                'name' => 'Lapangan Futsal A',
                'type' => 'Futsal',
                'description' => 'Lapangan futsal indoor dengan rumput sintetis dan pencahayaan standar malam.',
                'location' => 'Area Indoor 1',
                'price_per_hour' => 150000,
                'status' => 'available',
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Lapangan Futsal B',
                'type' => 'Futsal',
                'description' => 'Lapangan futsal indoor standar turnamen dengan tribun kecil.',
                'location' => 'Area Indoor 2',
                'price_per_hour' => 175000,
                'status' => 'available',
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Lapangan Badminton A',
                'type' => 'Badminton',
                'description' => 'Lapangan badminton indoor dengan lantai vinyl.',
                'location' => 'Hall Badminton 1',
                'price_per_hour' => 75000,
                'status' => 'available',
                'open_time' => '07:00:00',
                'close_time' => '21:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Lapangan Basket A',
                'type' => 'Basket',
                'description' => 'Lapangan basket outdoor yang sedang dijadwalkan perawatan ring.',
                'location' => 'Area Outdoor 1',
                'price_per_hour' => 200000,
                'status' => 'maintenance',
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Lapangan Mini Soccer A',
                'type' => 'Mini Soccer',
                'description' => 'Lapangan mini soccer outdoor dengan rumput sintetis.',
                'location' => 'Area Outdoor 2',
                'price_per_hour' => 250000,
                'status' => 'available',
                'open_time' => '08:00:00',
                'close_time' => '23:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Lapangan Voli A',
                'type' => 'Voli',
                'description' => 'Lapangan voli indoor yang sedang tidak aktif untuk renovasi ringan.',
                'location' => 'Hall Voli 1',
                'price_per_hour' => 90000,
                'status' => 'inactive',
                'open_time' => '08:00:00',
                'close_time' => '21:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
