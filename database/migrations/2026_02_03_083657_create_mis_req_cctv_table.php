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
    Schema::create('mis_cctv_requests', function (Blueprint $table) {
      $table->id('cctv_req_id');
      $table->enum('department', ['Admin', 'Marketing', 'Production'])->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
      $table->string('requested_by');
      $table->date('date_needed')->nullable();
      $table->date('date_of_incident')->nullable();
      $table->text('purpose')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('mis_cctv_requests');
  }
};
