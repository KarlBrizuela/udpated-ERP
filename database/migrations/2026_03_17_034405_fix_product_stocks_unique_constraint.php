<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixProductStocksUniqueConstraint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Safely drop any corrupted or old unique indices
        try { DB::statement('ALTER TABLE product_stocks DROP INDEX product_stocks_product_id_location_unique'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE product_stocks DROP INDEX product_stocks_location_unique'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE product_stocks DROP INDEX location'); } catch (\Exception $e) {}
        
        // Safely try adding the new index (will be ignored if it already exists or if there are duplicates)
        try {
            DB::statement('ALTER TABLE product_stocks ADD UNIQUE INDEX product_stocks_book_id_location_unique (book_id, location)');
        } catch (\Exception $e) {
            // Already exists or duplicate data
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::statement('ALTER TABLE product_stocks DROP INDEX product_stocks_book_id_location_unique');
        } catch (\Exception $e) {}
    }
}
