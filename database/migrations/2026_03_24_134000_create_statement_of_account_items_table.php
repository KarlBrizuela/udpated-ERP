<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementOfAccountItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statement_of_account_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statement_of_account_id')->constrained('statement_of_accounts')->onDelete('cascade');
            $table->string('service');
            $table->string('description')->nullable();
            $table->string('qty')->nullable();
            $table->decimal('price', 15, 2);
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
        Schema::dropIfExists('statement_of_account_items');
    }
}
