<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoUserSeeder extends Seeder
{
    /**
     * Create an additional full-access demo user account.
     */
    public function run(): void
    {
        // Ensure Super Admin role exists (created by RolePermissionSeeder)
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        $user = User::firstOrCreate(
            ['email' => 'demo@talukdarit.com'],
            [
                'name' => 'Demo Super Admin',
                'password' => Hash::make('demo123'),
                'role' => 'super_admin',
            ]
        );

        if ($superAdminRole && !$user->hasRole('Super Admin')) {
            $user->assignRole($superAdminRole);
        }

        $this->command?->info('Demo user created/updated (Bangladesh demo).');
        $this->command?->info('Email: demo@talukdarit.com');
        $this->command?->info('Password: demo123');
    }
}

