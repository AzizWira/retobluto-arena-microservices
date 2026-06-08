<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'user_id' => null,
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567890',
                'address' => 'Surabaya',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'phone' => '082345678901',
                'address' => 'Sidoarjo',
                'status' => 'active',
            ],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(
                ['email' => $member['email']],
                $member
            );
        }
    }
}
