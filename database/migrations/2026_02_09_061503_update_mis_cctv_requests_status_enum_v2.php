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
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");

        // Step 2: Update existing 'approved' status to 'Pending HR approval'
        DB::table('mis_cctv_requests')->where('status', 'approved')->update(['status' => 'Pending HR approval']);

        // Step 3: Apply the new ENUM definition
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status ENUM('to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'completed', 'rejected') DEFAULT 'to submit'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert 'Pending HR approval' to 'approved'
        DB::table('mis_cctv_requests')->where('status', 'Pending HR approval')->update(['status' => 'approved']);
        
        // Revert enum definition
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status ENUM('to submit', 'pending approval', 'approved', 'rejected', 'completed') DEFAULT 'to submit'");
    }
};
