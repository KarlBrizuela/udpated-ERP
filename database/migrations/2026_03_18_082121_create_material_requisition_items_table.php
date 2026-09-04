<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialRequisitionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_requisition_id')->constrained('material_requisitions')->onDelete('cascade');
            $table->decimal('qty', 10, 2);
            $table->string('unit')->nullable();
            $table->string('description');
            $table->decimal('supplier1_price', 10, 2)->nullable();
            $table->decimal('supplier2_price', 10, 2)->nullable();
            $table->decimal('supplier3_price', 10, 2)->nullable();
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
        Schema::dropIfExists('material_requisition_items');
    }
}
