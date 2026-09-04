<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdsToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category');
            }
            if (!Schema::hasColumn('books', 'sub_category_id')) {
                $table->unsignedBigInteger('sub_category_id')->nullable()->after('sub_category');
            }

            try {
                $table->foreign('category_id')->references('id')->on('book_categories')->onDelete('set null');
                $table->foreign('sub_category_id')->references('id')->on('book_categories')->onDelete('set null');
            } catch (\Exception $e) {
                // Ignore if foreign keys already exist
            }
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
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['category_id', 'sub_category_id']);
        });
    }
}
