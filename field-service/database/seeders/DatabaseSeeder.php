<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'name' => 'Lapangan Futsal A',
                'type' => 'Futsal',
                'description' => 'Lapangan futsal indoor dengan rumput sintetis.',
                'location' => 'Area Indoor 1',
                'price_per_hour' => 150000,
                'status' => 'available',
                'open_time' => '08:00',
                'close_time' => '22:00',
            ],
            [
                'name' => 'Lapangan Futsal B',
                'type' => 'Futsal',
                'description' => 'Lapangan futsal indoor standar turnamen.',
                'location' => 'Area Indoor 2',
                'price_per_hour' => 175000,
                'status' => 'available',
                'open_time' => '08:00',
                'close_time' => '22:00',
            ],
            [
                'name' => 'Lapangan Badminton A',
                'type' => 'Badminton',
                'description' => 'Lapangan badminton indoor dengan lantai vinyl.',
                'location' => 'Hall Badminton 1',
                'price_per_hour' => 75000,
                'status' => 'available',
                'open_time' => '07:00',
                'close_time' => '21:00',
            ],
            [
                'name' => 'Lapangan Basket A',
                'type' => 'Basket',
                'description' => 'Lapangan basket outdoor.',
                'location' => 'Area Outdoor',
                'price_per_hour' => 200000,
                'status' => 'maintenance',
                'open_time' => '08:00',
                'close_time' => '20:00',
            ],
        ];

        foreach ($fields as $field) {
            Field::updateOrCreate(
                ['name' => $field['name']],
                $field
            );
        }
    }
}
