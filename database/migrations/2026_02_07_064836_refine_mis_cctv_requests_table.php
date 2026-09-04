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
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            $table->time('time_of_incident')->nullable()->after('date_of_incident');
            $table->dropColumn('date_needed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            $table->date('date_needed')->nullable()->after('requested_by');
            $table->dropColumn('time_of_incident');
        });
    }
};
