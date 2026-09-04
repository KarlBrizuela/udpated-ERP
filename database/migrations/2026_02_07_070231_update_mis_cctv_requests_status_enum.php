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
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status VARCHAR(255) DEFAULT 'to submit'");

        DB::table('mis_cctv_requests')->where('status', 'pending')->update(['status' => 'pending approval']);

        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status ENUM('to submit', 'pending approval', 'approved', 'rejected', 'completed') DEFAULT 'to submit'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert 'pending approval' to 'pending'
        DB::table('mis_cctv_requests')->where('status', 'pending approval')->update(['status' => 'pending']);
        
        // Revert enum definition
        DB::statement("ALTER TABLE mis_cctv_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};
