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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique(); // EXP-YYYYMMDD-XXX
            $table->date('expense_date');
            $table->string('category'); // e.g., Office Supplies, Utilities, Rent, Transportation, etc.
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('restrict'); // Link to Chart of Accounts expense account
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'card', 'mobile_banking', 'bank_transfer', 'cheque', 'other'])->default('cash');
            $table->string('vendor_name')->nullable(); // Supplier/Vendor name
            $table->string('vendor_contact')->nullable();
            $table->text('description');
            $table->string('reference_number')->nullable(); // Invoice/Receipt number
            $table->string('attachment')->nullable(); // File path for receipt/invoice
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null'); // If paid via bank
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->date('payment_date')->nullable(); // Date when expense was paid
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('expense_number');
            $table->index('expense_date');
            $table->index('category');
            $table->index('status');
            $table->index('account_id');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
