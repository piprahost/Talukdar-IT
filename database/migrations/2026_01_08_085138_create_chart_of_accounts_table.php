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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Account code like 1000, 2000, etc.
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']); // Account type
            $table->enum('category', [
                // Assets
                'current_asset', 'fixed_asset', 'intangible_asset',
                // Liabilities
                'current_liability', 'long_term_liability',
                // Equity
                'capital', 'retained_earnings', 'drawing',
                // Revenue
                'sales_revenue', 'other_revenue',
                // Expenses
                'cost_of_goods_sold', 'operating_expense', 'financial_expense', 'other_expense'
            ]);
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete(); // For sub-accounts
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('balance_type', ['debit', 'credit']); // Normal balance type
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // System accounts cannot be deleted
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('type');
            $table->index('category');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
