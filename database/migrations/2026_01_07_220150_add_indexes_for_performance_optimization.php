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
        try {
            // Purchase Items indexes
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->index('status', 'purchase_items_status_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            // Sale Items indexes
            Schema::table('sale_items', function (Blueprint $table) {
                $table->index('barcode', 'sale_items_barcode_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            // Sales indexes
            Schema::table('sales', function (Blueprint $table) {
                $table->index('payment_status', 'sales_payment_status_index');
                $table->index('total_amount', 'sales_total_amount_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            // Purchase Orders indexes
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->index('payment_status', 'purchase_orders_payment_status_index');
                $table->index('total_amount', 'purchase_orders_total_amount_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            // Products indexes
            Schema::table('products', function (Blueprint $table) {
                $table->index('status', 'products_status_index');
                $table->index('stock_quantity', 'products_stock_quantity_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['total_amount']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['total_amount']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_stock_quantity_index');
        });
    }
};
