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
        Schema::table('books', function (Blueprint $row) {
            $row->string('shelf_number')->nullable()->after('max_stock');
            $row->string('rack_number')->nullable()->after('shelf_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $row) {
            $row->dropColumn(['shelf_number', 'rack_number']);
        });
    }
};
