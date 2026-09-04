<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosFieldsToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('item_code')->nullable()->unique()->after('id');
            $table->decimal('price', 10, 2)->default(0)->after('stock');
            $table->string('category')->nullable()->after('price');
            $table->boolean('is_active')->default(true)->after('category');
            $table->text('sales_description')->nullable()->after('purchase_description');
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
            $table->dropColumn(['item_code', 'price', 'category', 'is_active', 'sales_description']);
        });
    }
}
