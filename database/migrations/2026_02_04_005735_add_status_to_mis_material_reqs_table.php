<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToMisMaterialReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            if (!Schema::hasColumn('mis_material_reqs', 'status')) {
                $table->enum('status', ['pending', 'received'])->default('pending')->after('request_details');
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
            $table->dropColumn('status');
        });
    }
}
