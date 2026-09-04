<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignmentSettlementsTable extends Migration
{
    public function up()
    {
        Schema::create('consignment_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->decimal('amount', 15, 2);
            $table->integer('total_qty');
            $table->timestamp('settled_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consignment_settlements');
    }
}
