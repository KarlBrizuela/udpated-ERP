<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientNameToJvRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('jv_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            $table->dropColumn('client_name');
        });
    }
}
