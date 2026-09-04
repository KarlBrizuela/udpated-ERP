<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetadataFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'copyright')) {
                $table->string('copyright')->nullable();
            }
            if (!Schema::hasColumn('products', 'book_type')) {
                $table->string('book_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'weight')) {
                $table->string('weight')->nullable();
            }
            if (!Schema::hasColumn('products', 'cover_type')) {
                $table->string('cover_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'royalty')) {
                $table->string('royalty')->nullable();
            }
            if (!Schema::hasColumn('products', 'article')) {
                $table->string('article')->nullable();
            }
            if (!Schema::hasColumn('products', 'sub_category')) {
                $table->string('sub_category')->nullable();
            }
            if (!Schema::hasColumn('products', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('products', 'contact_number')) {
                $table->string('contact_number')->nullable();
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'copyright',
                'book_type',
                'weight',
                'cover_type',
                'royalty',
                'article',
                'sub_category',
                'email',
                'contact_number'
            ]);
        });
    }
}
