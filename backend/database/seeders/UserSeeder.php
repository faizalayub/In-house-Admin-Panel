<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'John Smith',       'email' => 'john.smith@example.com'],
            ['name' => 'Emma Johnson',     'email' => 'emma.johnson@example.com'],
            ['name' => 'Michael Brown',    'email' => 'michael.brown@example.com'],
            ['name' => 'Sarah Davis',      'email' => 'sarah.davis@example.com'],
            ['name' => 'James Wilson',     'email' => 'james.wilson@example.com'],
            ['name' => 'Olivia Martinez',  'email' => 'olivia.martinez@example.com'],
            ['name' => 'William Anderson', 'email' => 'william.anderson@example.com'],
            ['name' => 'Sophia Taylor',    'email' => 'sophia.taylor@example.com'],
            ['name' => 'Benjamin Thomas',  'email' => 'benjamin.thomas@example.com'],
            ['name' => 'Isabella Jackson', 'email' => 'isabella.jackson@example.com'],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => bcrypt('password')]
            );
        }
    }
}
