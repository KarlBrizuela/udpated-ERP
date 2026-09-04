<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customers', function (Blueprint $table) {
      $table->id('customer_id');
      $table->string('customer_name');
      $table->string('company_name');
      $table->string('account_number')->unique();

      $table->decimal('opening_balance', 15, 2)->default(0.00);
      $table->date('opening_balance_date')->nullable();
      $table->enum('currency_code', ['PHP', 'US'])->nullable();

      $table->enum('customer_type', ['Team A', 'Team B'])->nullable();
      $table->enum('rep', ['CLE', 'MKT'])->nullable();
      $table->enum('class', ['LAG', 'MNL'])->nullable();

      // Contact person details
      $table->string('title', 10)->nullable();
      $table->string('first_name', 100)->nullable();
      $table->string('middle_initial', 10)->nullable();
      $table->string('last_name', 100)->nullable();
      $table->string('job_title', 100)->nullable();

      // Contact methods (denormalized - matches your form structure)
      $table->string('main_phone')->nullable();
      $table->string('home_phone')->nullable();
      $table->string('work_phone')->nullable();
      $table->string('mobile')->nullable();
      $table->string('fax')->nullable();
      $table->string('main_email')->nullable();
      $table->string('cc_email')->nullable();
      $table->string('website')->nullable();
      $table->string('other_contact')->nullable();


      // Addresses
      $table->text('billing_address')->nullable();
      $table->text('shipping_address')->nullable();
      $table->boolean('is_default_shipping')->default(false);


      // / Payment settings
      $table->enum('payment_terms', ['Net 15', 'Net 30', 'Net 60', 'Due on receipt'])->nullable();
      $table->enum('preferred_delivery_method', ['Lazada', 'Shopee', 'Main Warehouse'])->nullable();
      $table->enum('preferred_payment_method', ['check', 'cash'])->nullable();
      $table->decimal('credit_limit', 15, 2)->nullable();
      $table->enum('price_level', ['standard', 'wholesale'])->nullable();

      // Credit card info (WARNING: See security note below)
      $table->char('card_number_last4', 4)->nullable();
      $table->char('card_exp_month', 2)->nullable();
      $table->char('card_exp_year', 4)->nullable();
      $table->string('card_name')->nullable();
      $table->text('card_billing_address')->nullable();
      $table->string('card_zip', 20)->nullable();

      // Custom fields
      $table->string('custom_contact_person')->nullable();
      $table->string('custom_customer_field')->nullable();

      // Status
      $table->boolean('is_inactive')->default(false);

      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('customer_name');
      $table->index('rep');
      $table->index('class');
      $table->index('is_inactive');
      $table->index('main_email');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('customers');
  }
}
