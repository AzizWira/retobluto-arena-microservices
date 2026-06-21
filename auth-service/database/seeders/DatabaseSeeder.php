<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('otp_codes')->truncate();
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Retobluto Admin',
                'email' => 'admin@retobluto.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'admin',
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Ahmad Aziz Wira Widodo',
                'email' => 'wira123widodo@gmail.com',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'member',
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Aura Iftitah',
                'email' => 'auraiftitahh@gmail.com',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'member',
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Muhammad Agil Hidayahtullah',
                'email' => 'muhammadagilhidayahtullah295@gmail.com',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'member',
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Ryan Alvin Saputra',
                'email' => 'ryanalfin6@gmail.com',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'member',
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Nabila Putri Ramadhani',
                'email' => 'nabila.member@example.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'member',
                'is_verified' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Dimas Pratama Wijaya',
                'email' => 'dimas.member@example.com',
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'role' => 'member',
                'is_verified' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
