<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change column to VARCHAR first to allow easy renaming/modification
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");

        // Map old statuses to new ones if necessary
        DB::table('mis_material_reqs')->where('status', 'completed')->update(['status' => 'forwarded to accounting']);
        DB::table('mis_material_reqs')->where('status', 'final approval')->update(['status' => 'Pending Final Approval']);

        // Apply new ENUM definition
        $enum = "'to submit', 'pending approval', 'Pending Final Approval', 'forwarded to accounting', 'received', 'rejected'";
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status ENUM($enum) DEFAULT 'to submit'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");

        DB::table('mis_material_reqs')->where('status', 'forwarded to accounting')->update(['status' => 'completed']);
        DB::table('mis_material_reqs')->where('status', 'Pending Final Approval')->update(['status' => 'final approval']);

        $enum = "'to submit', 'pending approval', 'final approval', 'completed', 'rejected', 'received'";
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status ENUM($enum) DEFAULT 'to submit'");
    }
};
