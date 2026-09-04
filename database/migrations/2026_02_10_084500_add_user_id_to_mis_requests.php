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
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('mis_cctv_requests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('cctv_req_id')->constrained('users')->onDelete('cascade');
            }
        });

        Schema::table('mis_material_reqs', function (Blueprint $table) {
            if (!Schema::hasColumn('mis_material_reqs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('material_req_id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
