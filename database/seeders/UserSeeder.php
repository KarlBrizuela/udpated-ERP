<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@clarentian.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'middle_initial' => '',
                'employee_number' => 'ADM-001',
                'password' => Hash::make('admin123'),
                'plain_password' => 'admin123',
                'division' => 'Super Admin',
                'department' => 'Administration',
                'position' => 'Super Admin',
                'role' => 'Super Admin',
                'status' => true,
            ]
        );
    }
}
