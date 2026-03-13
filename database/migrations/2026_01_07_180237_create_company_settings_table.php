<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('ERP System');
            $table->string('service_center_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->default('Dhaka');
            $table->string('country')->default('Bangladesh');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });
        
        // Insert default company information
        DB::table('company_settings')->insert([
            'company_name' => 'ERP System',
            'service_center_name' => 'Service Center',
            'address' => '',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'phone' => '+880 1XXX XXXXXX',
            'email' => 'info@erpsystem.com',
            'website' => '',
            'terms_and_conditions' => "1. Items are repaired at customer's risk. We are not responsible for data loss during service.\n2. Service charges must be paid before delivery unless prior arrangements have been made.\n3. Unclaimed items after 30 days will be disposed of or sold to recover costs.\n4. Warranty is provided for parts replaced during service for 90 days.\n5. Customer is responsible for collecting the item within the specified delivery date.",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
