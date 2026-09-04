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
            $table->string('category')->nullable();
            $table->string('barcode')->nullable();
            $table->string('mfr_part_no')->nullable();
            $table->text('purchase_description')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('cogs_account')->nullable();
            $table->text('sales_description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('asset_account')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->integer('max_stock')->default(0);
            $table->string('unit')->default('pcs');
            
            // Book Metadata
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('size')->nullable();
            $table->integer('pages')->nullable();
            $table->string('image')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
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
