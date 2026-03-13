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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('product_model_id')->nullable()->constrained('product_models')->onDelete('set null');
            
            // Product Details
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->string('unit')->default('pcs');
            
            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            
            // Stock Management
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->integer('min_stock')->default(5);
            $table->integer('max_stock')->nullable();
            
            // Status & Visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('available'); // available, out_of_stock, discontinued
            
            // Images
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            
            // Additional Info
            $table->string('warranty_period')->nullable();
            $table->text('notes')->nullable();
            
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('sku');
            $table->index('barcode');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
