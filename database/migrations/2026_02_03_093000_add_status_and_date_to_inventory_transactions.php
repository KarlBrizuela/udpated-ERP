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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_transactions', 'status')) {
                $table->string('status')->default('completed')->after('total_cost'); // completed, pending, cancelled
            }
            if (!Schema::hasColumn('inventory_transactions', 'transaction_date')) {
                $table->date('transaction_date')->nullable()->after('supplier');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'transaction_date']);
        });
    }
};
