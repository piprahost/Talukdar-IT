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
            $table->string('supplier_name')->nullable()->after('supplier_id');
            $table->string('supplier_phone')->nullable()->after('supplier_name');
            $table->text('supplier_address')->nullable()->after('supplier_phone');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable(false)->change();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['supplier_name', 'supplier_phone', 'supplier_address']);
        });
    }
};
