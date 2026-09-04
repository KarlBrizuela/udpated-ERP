<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_no')->unique();
            $table->date('date');
            $table->string('department');
            $table->string('supplier')->nullable();
            $table->string('po_number')->nullable();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('material_requisitions');
    }
}
