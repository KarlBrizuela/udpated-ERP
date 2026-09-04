<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_voucher_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('journal_voucher_requests', 'manager_approved_by')) {
                $table->foreignId('manager_approved_by')->nullable()->after('approved_at')->constrained('users');
            }
            if (!Schema::hasColumn('journal_voucher_requests', 'manager_approved_at')) {
                $table->timestamp('manager_approved_at')->nullable()->after('manager_approved_by');
            }
        });
    }

    public function down()
    {
        Schema::table('journal_voucher_requests', function (Blueprint $table) {
            $table->dropForeign(['manager_approved_by']);
            $table->dropColumn(['approved_at', 'manager_approved_by', 'manager_approved_at']);
        });
    }
};
