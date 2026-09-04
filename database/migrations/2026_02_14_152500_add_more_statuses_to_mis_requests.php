<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update status enum for QB Requests
        DB::statement("ALTER TABLE mis_qb_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'on_hold', 'ongoing') NOT NULL DEFAULT 'pending'");

        // Update status enum for Undertime Requests
        DB::statement("ALTER TABLE mis_undertime_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'on_hold', 'ongoing') NOT NULL DEFAULT 'pending'");

        // Update status enum for Service Requests
        DB::statement("ALTER TABLE mis_service_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'on_hold', 'ongoing') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM (if possible, data loss risk if new statuses are used)
        // For development, we can restrict it back, but usually we don't need to revert enum extensions.
    }
};
