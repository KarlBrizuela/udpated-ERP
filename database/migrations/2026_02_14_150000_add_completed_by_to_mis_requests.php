<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mis_qb_requests', function (Blueprint $table) {
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
        });

        Schema::table('mis_undertime_requests', function (Blueprint $table) {
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
        });

        Schema::table('mis_service_requests', function (Blueprint $table) {
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mis_qb_requests', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['completed_by', 'completed_at']);
        });

        Schema::table('mis_undertime_requests', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['completed_by', 'completed_at']);
        });

        Schema::table('mis_service_requests', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['completed_by', 'completed_at']);
        });
    }
};
