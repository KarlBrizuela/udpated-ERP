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
            if (!Schema::hasColumn('mis_material_reqs', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
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
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            if (Schema::hasColumn('mis_material_reqs', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
