<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->truncate();

        $roles = [
            [
                'id' => Str::uuid(),
                'role' => 'Admin Users',
//                'type' => 'dashboard',
                'access' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid(),
                'role' => 'Editors',
//                'type' => 'dashboard',
                'access' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid(),
                'role' => 'Operations',
//                'type' => 'dashboard',
                'access' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid(),
                'role' => 'Viewers',
//                'type' => 'mobile',
                'access' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid(),
                'role' => 'Checkin Users',
//                'type' => 'mobile',
                'access' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Role::insert($roles);
    }
}
