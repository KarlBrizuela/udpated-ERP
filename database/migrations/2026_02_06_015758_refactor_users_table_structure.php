<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefactorUsersTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add new columns
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'middle_initial')) {
                $table->string('middle_initial')->nullable()->after('last_name');
            }
            // Add position if not exists
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('last_name');
            }
        });

        // 2. Migrate Data
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $updates = [];

            // Split Name
            if (isset($user->name) && (!isset($user->first_name) || empty($user->first_name))) {
                $fullName = $user->name;
                $parts = explode(' ', $fullName);
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
                
                $updates['first_name'] = $firstName ?: $lastName;
                $updates['last_name'] = $firstName ? $lastName : '';
            }

            // Migrate Role to Position if Role exists
            if (isset($user->role) && (!isset($user->position) || empty($user->position))) {
                $updates['position'] = $user->role;
            }

            if (!empty($updates)) {
                DB::table('users')->where('id', $user->id)->update($updates);
            }
        }

        // 3. Drop old columns
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable();
            }
        });

        // Restore Data (Best Effort)
        $users = DB::table('users')->get();
        foreach ($users as $user) {
             $updates = [];
             $fullName = TRIM(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
             if (!empty($fullName)) {
                 $updates['name'] = $fullName;
             }
             if (isset($user->position)) {
                 $updates['role'] = $user->position;
             }
             
             if (!empty($updates)) {
                DB::table('users')->where('id', $user->id)->update($updates);
             }
        }
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'middle_initial', 'position']);
        });
    }
}
