<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('category')->nullable(); // Crucifixes, Rosaries, Candles, etc.
            $table->text('description')->nullable();
            
            // Stock management
            $table->integer('stock')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->integer('max_stock')->default(0);
            $table->string('unit')->default('pcs');
            
            // Costing
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('cogs_account')->nullable();
            $table->text('purchase_description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
