<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePaymentSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // gcash, paymaya, bank, pos_config
            $table->json('value'); // Stores the configuration as JSON
            $table->timestamps();
        });

        // Insert default POS configuration
        DB::table('payment_settings')->insert([
            [
                'key' => 'pos_config',
                'value' => json_encode([
                    'taxRate' => 12,
                    'currencySymbol' => '₱',
                    'orderPrefix' => 'POS'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_settings');
    }
}
