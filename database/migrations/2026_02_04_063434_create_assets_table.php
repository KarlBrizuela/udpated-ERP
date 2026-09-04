<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('assets', function (Blueprint $table) {
      $table->id('asset_id');
      $table->string('property_code', 20)->unique();
      $table->string('category');
      $table->text('description')->nullable();
      $table->date('acquisition_date');
      $table->string('department');
      $table->string('checked_by');
      $table->string('status')->default('Active');
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
    Schema::dropIfExists('assets');
  }
}
