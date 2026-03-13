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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            
            // Payment type: 'customer' or 'supplier'
            $table->enum('payment_type', ['customer', 'supplier']);
            
            // Reference to sale or purchase
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            
            // Customer or Supplier
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            
            // Payment details
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'card', 'mobile_banking', 'bank_transfer', 'cheque', 'other'])->default('cash');
            $table->string('reference_number')->nullable(); // Transaction reference, cheque number, etc.
            $table->text('notes')->nullable();
            
            // Additional info
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('payment_number');
            $table->index('payment_type');
            $table->index('payment_date');
            $table->index('sale_id');
            $table->index('purchase_id');
            $table->index('customer_id');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
