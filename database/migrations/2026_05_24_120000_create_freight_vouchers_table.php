<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreightVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('fv_number')->unique();
            $table->date('date');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('received_by')->nullable();
            $table->enum('status', ['open', 'paid', 'liquidated'])->default('open');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freight_vouchers');
    }
}
