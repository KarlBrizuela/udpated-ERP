<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastSettledAtToConsignmentOwners extends Migration
{
    public function up()
    {
        Schema::table('consignment_owners', function (Blueprint $table) {
            $table->timestamp('last_settled_at')->nullable()->after('account_number');
        });
    }

    public function down()
    {
        Schema::table('consignment_owners', function (Blueprint $table) {
            $table->dropColumn('last_settled_at');
        });
    }
}
