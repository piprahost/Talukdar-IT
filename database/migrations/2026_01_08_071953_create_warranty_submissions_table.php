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
        Schema::create('warranty_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('memo_number')->unique();
            
            // Link to warranty
            $table->foreignId('warranty_id')->constrained('warranties')->onDelete('restrict');
            $table->foreignId('sale_id')->constrained('sales')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // Barcode
            $table->string('barcode')->nullable();
            
            // Submission details
            $table->date('submission_date');
            $table->text('problem_description');
            $table->text('customer_complaint');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('good');
            $table->text('physical_condition_notes')->nullable();
            
            // Customer information (snapshot)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            
            // Service/Repair details
            $table->enum('status', ['pending', 'received', 'in_progress', 'completed', 'returned', 'cancelled'])->default('pending');
            $table->text('internal_notes')->nullable();
            $table->text('service_notes')->nullable();
            $table->decimal('service_charge', 10, 2)->nullable();
            
            // Dates
            $table->date('expected_completion_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('return_date')->nullable();
            
            // Additional
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('memo_number');
            $table->index('warranty_id');
            $table->index('status');
            $table->index('submission_date');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_submissions');
    }
};
