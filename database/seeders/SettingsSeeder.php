<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed settings from config/settings.php (missing keys only).
     */
    public function run(): void
    {
        Setting::seedFromConfig();
    }
}
