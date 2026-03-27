<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed only essential bootstrap data for fresh install.
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
