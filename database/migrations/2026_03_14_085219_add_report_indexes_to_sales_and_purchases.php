<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Composite indexes for report/list queries (date range + status).
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['sale_date', 'status'], 'sales_sale_date_status_index');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index(['order_date', 'status'], 'purchase_orders_order_date_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_sale_date_status_index');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('purchase_orders_order_date_status_index');
        });
    }
};
