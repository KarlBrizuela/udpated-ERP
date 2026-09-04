<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->foreignId('approved_by_admin')->nullable()->after('manager_approved_at')->constrained('users');
            $table->timestamp('admin_approved_at')->nullable()->after('approved_by_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_cash_advances', function (Blueprint $table) {
            $table->dropForeign(['approved_by_admin']);
            $table->dropColumn(['approved_by_admin', 'admin_approved_at']);
        });
    }
};
