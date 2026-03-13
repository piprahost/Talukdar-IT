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
        // First, update existing warranty_period values that might be text to days
        // Convert common formats to days (this is a best-effort conversion)
        DB::table('products')->whereNotNull('warranty_period')->get()->each(function ($product) {
            $period = $product->warranty_period;
            $days = $this->convertToDays($period);
            
            if ($days !== null) {
                DB::table('products')->where('id', $product->id)->update([
                    'warranty_period' => $days
                ]);
            } else {
                // Default to 365 days (1 year) if we can't parse
                DB::table('products')->where('id', $product->id)->update([
                    'warranty_period' => 365
                ]);
            }
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->integer('warranty_period')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('warranty_period')->nullable()->change();
        });
    }
    
    /**
     * Convert warranty period text to days
     */
    private function convertToDays($period): ?int
    {
        if (is_numeric($period)) {
            return (int) $period;
        }
        
        $period = strtolower(trim($period));
        
        // Extract numbers and units
        if (preg_match('/(\d+)\s*(year|years|yr|y)/i', $period, $matches)) {
            return (int) $matches[1] * 365;
        }
        if (preg_match('/(\d+)\s*(month|months|mo)/i', $period, $matches)) {
            return (int) $matches[1] * 30;
        }
        if (preg_match('/(\d+)\s*(week|weeks|wk|w)/i', $period, $matches)) {
            return (int) $matches[1] * 7;
        }
        if (preg_match('/(\d+)\s*(day|days|d)/i', $period, $matches)) {
            return (int) $matches[1];
        }
        
        return null;
    }
};
