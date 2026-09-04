<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0);
            }
            if (!Schema::hasColumn('products', 'reorder_point')) {
                $table->integer('reorder_point')->default(0);
            }
            if (!Schema::hasColumn('products', 'max_stock')) {
                $table->integer('max_stock')->default(0);
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('pcs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'reorder_point', 'max_stock', 'unit']);
        });
    }
};
