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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'mobile_banking', 'bank_transfer', 'cheque', 'other'])->default('cash')->after('due_amount');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null')->after('payment_method');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropIndex(['payment_method']);
            $table->dropColumn(['payment_method', 'bank_account_id']);
        });
    }
};
