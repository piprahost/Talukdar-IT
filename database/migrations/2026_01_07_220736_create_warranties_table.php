<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('warranties')) {
            return; // Table already exists
        }
        
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_item_id')->constrained('sale_items')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // Barcode tracking
            $table->string('barcode')->nullable()->index();
            
            // Warranty details
            $table->integer('warranty_period_days'); // Warranty period in days
            $table->date('start_date'); // When warranty starts (sale completion date)
            $table->date('end_date'); // When warranty expires
            
            // Status
            $table->enum('status', ['active', 'expired', 'voided'])->default('active');
            
            // Additional info
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for faster lookups
            $table->index('status', 'warranties_status_index');
            $table->index('end_date', 'warranties_end_date_index');
            $table->index(['barcode', 'status'], 'warranties_barcode_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
