<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsignmentFieldsToBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('consignment_owner_id')->nullable()->after('sub_category_id');
            $table->decimal('source_price', 10, 2)->default(0)->after('cost');
            $table->decimal('markup_amount', 10, 2)->default(0)->after('source_price');

            $table->foreign('consignment_owner_id')->references('id')->on('consignment_owners')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['consignment_owner_id']);
            $table->dropColumn(['consignment_owner_id', 'source_price', 'markup_amount']);
        });
    }
}
