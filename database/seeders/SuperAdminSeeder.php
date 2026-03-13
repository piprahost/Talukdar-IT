<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $superAdmin = User::where('email', 'admin@erp.com')->first();
        
        if (!$superAdmin) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@erp.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
            
            // Assign Super Admin role
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                $user->assignRole($superAdminRole);
            }
            
            $this->command->info('Super Admin created successfully!');
            $this->command->info('Email: admin@erp.com');
            $this->command->info('Password: admin123');
        } else {
            // Ensure the role is assigned
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            if ($superAdminRole && !$superAdmin->hasRole('Super Admin')) {
                $superAdmin->assignRole($superAdminRole);
            }
            $this->command->info('Super Admin already exists!');
        }
    }
}
