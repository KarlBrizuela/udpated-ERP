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
        Schema::table('employee_cash_advances', function (Blueprint $table) {
            // 1. Drop Columns
            $table->dropColumn(['request_type', 'repayment_method']);
        });

        // 2. Modify Status Enum (Using raw SQL for enum modification as it's cleaner for existing data)
        // We will map existing 'pending approval' to 'pending_supervisor_approval'
        DB::statement("ALTER TABLE employee_cash_advances MODIFY COLUMN status ENUM('pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval', 'approved', 'rejected') DEFAULT 'pending_supervisor_approval'");
        
        // Update existing records to match new status
        DB::table('employee_cash_advances')
            ->where('status', 'pending approval')
            ->update(['status' => 'pending_supervisor_approval']);

         DB::table('employee_cash_advances')
            ->where('status', 'Pending Final Approval')
            ->update(['status' => 'pending_admin_approval']); // Rough mapping, assuming next stage
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_cash_advances', function (Blueprint $table) {
            $table->string('request_type')->default('Official Business');
            $table->string('repayment_method')->default('Cash');
        });

        // Revert status (approximated)
        DB::statement("ALTER TABLE employee_cash_advances MODIFY COLUMN status ENUM('pending approval', 'Pending Final Approval', 'forwarded to accounting', 'received', 'rejected') DEFAULT 'pending approval'");
    }
};
