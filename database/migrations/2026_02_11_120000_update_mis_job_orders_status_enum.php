<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update status enum for QB Requests
        DB::statement("ALTER TABLE mis_qb_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending'");

        // Update status enum for Undertime Requests
        DB::statement("ALTER TABLE mis_undertime_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending'");

        // Update status enum for Service Requests
        DB::statement("ALTER TABLE mis_service_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert is difficult without knowing the exact previous state, but we can assume 'pending' and 'completed' were likely always there.
        // We generally don't want to revert this kind of fix, but strictly speaking we could restrictive it back.
        // For now, we will leave down blank or just keep it as is since this is a fix.
    }
};
