<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMisMaterialReqsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('mis_material_reqs', function (Blueprint $table) {
      $table->id('material_req_id');
      $table->string('requested_by');
      $table->date('request_date');
      $table->text('request_details');
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
    Schema::dropIfExists('mis_material_reqs');
  }
}
