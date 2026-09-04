<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_transfers')) {
            Schema::table('stock_transfers', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_transfers', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('notes');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_transfers')) {
            Schema::table('stock_transfers', function (Blueprint $table) {
                if (Schema::hasColumn('stock_transfers', 'rejection_reason')) {
                    $table->dropColumn('rejection_reason');
                }
            });
        }
    }
};
