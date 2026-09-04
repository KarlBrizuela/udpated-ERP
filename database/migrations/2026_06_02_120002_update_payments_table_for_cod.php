<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentsTableForCod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add COD tracking fields
            if (!Schema::hasColumn('payments', 'rider_collection_id')) {
                $table->foreignId('rider_collection_id')->nullable()->after('sales_order_id')->constrained('rider_collections')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('payments', 'collected_by')) {
                $table->foreignId('collected_by')->nullable()->after('rider_collection_id')->constrained('users');
            }
            
            if (!Schema::hasColumn('payments', 'handed_over_by')) {
                $table->foreignId('handed_over_by')->nullable()->after('collected_by')->constrained('users');
            }
            
            if (!Schema::hasColumn('payments', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('handed_over_by')->constrained('users');
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
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'rider_collection_id')) {
                $table->dropForeignKeyIfExists(['rider_collection_id']);
                $table->dropColumn('rider_collection_id');
            }
            if (Schema::hasColumn('payments', 'collected_by')) {
                $table->dropForeignKeyIfExists(['collected_by']);
                $table->dropColumn('collected_by');
            }
            if (Schema::hasColumn('payments', 'handed_over_by')) {
                $table->dropForeignKeyIfExists(['handed_over_by']);
                $table->dropColumn('handed_over_by');
            }
            if (Schema::hasColumn('payments', 'verified_by')) {
                $table->dropForeignKeyIfExists(['verified_by']);
                $table->dropColumn('verified_by');
            }
        });
    }
}
