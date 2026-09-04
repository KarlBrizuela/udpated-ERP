<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->foreignId('rider_id')->constrained('users'); // driver/rider assigned
            $table->decimal('amount_to_collect', 15, 2);
            $table->decimal('amount_collected', 15, 2)->nullable();
            $table->enum('status', ['pending', 'collected', 'handed_over', 'verified'])->default('pending');
            $table->dateTime('collected_at')->nullable();
            $table->dateTime('handed_over_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->text('collection_notes')->nullable();
            $table->string('customer_signature_photo')->nullable(); // path to photo proof
            $table->string('reference_photo')->nullable(); // receipt/proof photo
            $table->foreignId('verified_by')->nullable()->constrained('users'); // cashier who verified
            $table->decimal('amount_discrepancy', 15, 2)->nullable(); // difference if any
            $table->text('discrepancy_notes')->nullable();
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
        Schema::dropIfExists('rider_collections');
    }
}
