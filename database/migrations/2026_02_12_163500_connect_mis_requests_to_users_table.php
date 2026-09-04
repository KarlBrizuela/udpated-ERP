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
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->foreign('approved_by_manager')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_director')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            $table->foreign('approved_by_manager')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_hr')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_director')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mis_material_reqs', function (Blueprint $table) {
            $table->dropForeign(['approved_by_manager']);
            $table->dropForeign(['approved_by_director']);
            $table->dropForeign(['rejected_by']);
        });

        Schema::table('mis_cctv_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by_manager']);
            $table->dropForeign(['approved_by_hr']);
            $table->dropForeign(['approved_by_director']);
            $table->dropForeign(['rejected_by']);
        });
    }
};
