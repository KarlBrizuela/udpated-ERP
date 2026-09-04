<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by_manager')->nullable()->after('request_details');
            $table->timestamp('manager_approved_at')->nullable()->after('approved_by_manager');
            
            $table->unsignedBigInteger('approved_by_director')->nullable()->after('manager_approved_at');
            $table->timestamp('director_approved_at')->nullable()->after('approved_by_director');
            
            // Rejections
            $table->unsignedBigInteger('rejected_by')->nullable()->after('director_approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->dropColumn([
                'approved_by_manager',
                'manager_approved_at',
                'approved_by_director',
                'director_approved_at',
                'rejected_by',
                'rejected_at',
                'rejection_reason'
            ]);
        });
    }
};
