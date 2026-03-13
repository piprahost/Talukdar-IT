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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique();
            
            // Product Information
            $table->string('product_name');
            $table->string('serial_number')->nullable();
            $table->text('problem_notes')->nullable();
            $table->text('service_notes')->nullable();
            $table->decimal('service_cost', 10, 2)->default(0);
            
            // Dates
            $table->date('receive_date');
            $table->date('delivery_date')->nullable();
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            
            // Payment Information
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            
            // Status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delivered', 'cancelled'])->default('pending');
            
            // Additional
            $table->text('internal_notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
