<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            $table->boolean('hardcopy')->default(false)->after('purpose');
            $table->boolean('viewing')->default(false)->after('hardcopy');
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
            $table->dropColumn(['hardcopy', 'viewing']);
        });
    }
}
