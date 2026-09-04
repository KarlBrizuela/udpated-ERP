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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->unique()->after('id');
            $table->string('division')->nullable()->after('email');
            $table->string('department')->nullable()->after('division');
            $table->string('role')->nullable()->after('department');
            $table->boolean('status')->default(true)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_number', 'division', 'department', 'role', 'status']);
        });
    }
};
