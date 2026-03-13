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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            
            // Individual Item Tracking - Each item has its own barcode
            $table->string('barcode')->unique();
            $table->string('serial_number')->nullable();
            
            // Pricing (per item)
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2)->nullable();
            
            // Quantity (usually 1 per item for individual tracking)
            $table->integer('quantity')->default(1);
            
            // Status
            $table->enum('status', ['pending', 'received', 'damaged', 'returned'])->default('pending');
            $table->date('received_date')->nullable();
            
            // Additional Info
            $table->text('condition_notes')->nullable(); // For used items
            $table->text('warranty_info')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('purchase_order_id');
            $table->index('product_id');
            $table->index('barcode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
