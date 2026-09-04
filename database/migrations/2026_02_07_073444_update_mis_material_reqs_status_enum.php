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
        // Step 1: Temporarily change column to VARCHAR to allow string updates without enum constraints
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");

        // Step 2: Update existing 'pending' status to 'pending approval'
        DB::table('mis_material_reqs')->where('status', 'pending')->update(['status' => 'pending approval']);

        // Step 3: Apply the new ENUM definition
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status ENUM('to submit', 'pending approval', 'approved', 'rejected', 'received') DEFAULT 'to submit'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert 'pending approval' to 'pending'
        DB::table('mis_material_reqs')->where('status', 'pending approval')->update(['status' => 'pending']);

        // Revert enum definition (assuming previous was pending, received)
        DB::statement("ALTER TABLE mis_material_reqs MODIFY COLUMN status ENUM('pending', 'received') DEFAULT 'pending'");
    }
};
