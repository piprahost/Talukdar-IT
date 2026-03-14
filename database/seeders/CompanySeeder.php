<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    /**
     * Seed company settings with Bangladesh-friendly demo data.
     */
    public function run(): void
    {
        $tagline = 'Computer Sales & Repair • You Can Buy And Replace Here';
        $data = [
            'company_name' => 'Talukdar IT',
            'service_center_name' => 'Talukdar IT Service Center',
            'address' => 'House 12, Road 5, Block C, Banani',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'phone' => '+880 1712-345678',
            'email' => 'info@talukdarit.com',
            'website' => 'https://www.talukdarit.com',
            'terms_and_conditions' => "1. Items are repaired at customer's risk. We are not responsible for data loss during service.\n2. Service charges must be paid before delivery unless prior arrangements have been made.\n3. Unclaimed items after 30 days will be disposed of or sold to recover costs.\n4. Warranty is provided for parts replaced during service for 90 days.\n5. Customer is responsible for collecting the item within the specified delivery date.",
        ];

        $exists = DB::table('company_settings')->first();
        if ($exists) {
            $update = array_merge($data, ['updated_at' => now()]);
            if (Schema::hasColumn('company_settings', 'tagline')) {
                $update['tagline'] = $tagline;
            }
            DB::table('company_settings')->where('id', $exists->id)->update($update);
        } else {
            $insert = array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (Schema::hasColumn('company_settings', 'tagline')) {
                $insert['tagline'] = $tagline;
            }
            DB::table('company_settings')->insert($insert);
        }
        $this->command?->info('Company settings (Bangladesh demo) seeded.');
    }
}
