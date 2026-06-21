<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('members')->truncate();

        DB::table('members')->insert([
            [
                'id' => 1,
                'user_id' => 2,
                'name' => 'Ahmad Aziz Wira Widodo',
                'email' => 'wira123widodo@gmail.com',
                'phone' => '081234567890',
                'address' => 'Surabaya, Jawa Timur',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'name' => 'Aura Iftitah',
                'email' => 'auraiftitahh@gmail.com',
                'phone' => '081234567891',
                'address' => 'Sidoarjo, Jawa Timur',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'name' => 'Muhammad Agil Hidayahtullah',
                'email' => 'muhammadagilhidayahtullah295@gmail.com',
                'phone' => '081234567892',
                'address' => 'Gresik, Jawa Timur',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'user_id' => 5,
                'name' => 'Ryan Alvin Saputra',
                'email' => 'ryanalfin6@gmail.com',
                'phone' => '081234567893',
                'address' => 'Mojokerto, Jawa Timur',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'user_id' => 6,
                'name' => 'Nabila Putri Ramadhani',
                'email' => 'nabila.member@example.com',
                'phone' => '081234567894',
                'address' => 'Surabaya, Jawa Timur',
                'status' => 'inactive',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'user_id' => 7,
                'name' => 'Dimas Pratama Wijaya',
                'email' => 'dimas.member@example.com',
                'phone' => '081234567895',
                'address' => 'Malang, Jawa Timur',
                'status' => 'blocked',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
