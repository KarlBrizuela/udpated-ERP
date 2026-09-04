<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('division_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('division');
            $table->timestamps();
        });

        // Backfill existing data
        $users = DB::table('users')->whereNotNull('division')->get();
        foreach ($users as $user) {
            if ($user->division && $user->division !== 'All Divisions') {
                DB::table('division_user')->insert([
                    'user_id' => $user->id,
                    'division' => $user->division,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('division_user');
    }
}
