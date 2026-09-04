<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Super Admin
        Role::updateOrCreate(
            ['name' => 'Super Admin'],
            [
                'division' => 'All Divisions',
                'permission_level' => 'OVERRIDE',
                'access_scope' => 'ALL_DIVISIONS',
                'description' => 'Full access to all system features and settings.',
                'is_active' => true,
            ]
        );

        // 2. Director
        Role::updateOrCreate(
            ['name' => 'Director'],
            [
                'division' => 'All Divisions',
                'permission_level' => 'APPROVE',
                'access_scope' => 'ALL_DIVISIONS',
                'description' => 'Director level access with approval capabilities.',
                'is_active' => true,
            ]
        );
    }
}
