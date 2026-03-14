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
        // Seed core configuration and Bangladesh-friendly demo data
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            DemoUserSeeder::class,
            ChartOfAccountsSeeder::class,
            SettingsSeeder::class,
            CompanySeeder::class,
            ProductSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
