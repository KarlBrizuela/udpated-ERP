<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SeparateBooksFromProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Disable foreign key checks for the duration of the migration
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Create the Books table (The Master Registry)
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('size')->nullable();
            $table->integer('pages')->nullable();
            $table->string('copyright')->nullable();
            $table->string('book_type')->nullable();
            $table->string('weight')->nullable();
            $table->string('cover_type')->nullable();
            $table->string('royalty')->nullable();
            $table->string('article')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            
            // Stock related (Source of truth)
            $table->integer('stock')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->integer('max_stock')->default(0);
            $table->string('unit')->default('pcs');
            
            // Purchase/Accounting info related to the physical item
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('cogs_account')->nullable();
            $table->text('purchase_description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Migrate existing data from products to books
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            DB::table('books')->insert([
                'id' => $product->id, 
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'author' => $product->author,
                'publisher' => $product->publisher,
                'size' => $product->size,
                'pages' => $product->pages,
                'copyright' => $product->copyright,
                'book_type' => $product->book_type,
                'weight' => $product->weight,
                'cover_type' => $product->cover_type,
                'royalty' => $product->royalty,
                'article' => $product->article,
                'sub_category' => $product->sub_category,
                'email' => $product->email,
                'contact_number' => $product->contact_number,
                'stock' => $product->stock,
                'reorder_point' => $product->reorder_point,
                'max_stock' => $product->max_stock,
                'unit' => $product->unit,
                'cost' => $product->cost,
                'cogs_account' => $product->cogs_account,
                'purchase_description' => $product->purchase_description,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }

        // 3. Rename old products table and create new one
        Schema::rename('products', 'products_old');

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->text('sales_description')->nullable();
            $table->string('asset_account')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Populate NEW products table from OLD products table
        foreach ($products as $product) {
            DB::table('products')->insert([
                'id' => $product->id, 
                'book_id' => $product->id, 
                'name' => $product->name,
                'price' => $product->price,
                'category' => $product->category,
                'image' => $product->image,
                'sales_description' => $product->sales_description,
                'asset_account' => $product->asset_account,
                'is_active' => $product->is_active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }

        // 5. Relink Inventory Tables
        // Relink product_stocks
        Schema::table('product_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('product_stocks', 'product_id')) {
                try { 
                    $table->dropForeign(['product_id']); 
                } catch (\Exception $e) {}
                
                // Drop the unique composite index if it exists
                try {
                    $table->dropUnique(['product_id', 'location']);
                } catch (\Exception $e) {}
            }
            
            if (!Schema::hasColumn('product_stocks', 'book_id')) {
                $table->unsignedBigInteger('book_id')->nullable()->after('product_id');
            }
        });
        
        if (Schema::hasColumn('product_stocks', 'product_id')) {
            DB::statement('UPDATE product_stocks SET book_id = product_id');
            Schema::table('product_stocks', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }

        // Drop index if we added book_id and drop unique if needed for book_id
        Schema::table('product_stocks', function (Blueprint $table) {
            try { $table->unique(['book_id', 'location']); } catch (\Exception $e) {}
        });
        
        // Relink inventory_transactions
        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_transactions', 'product_id')) {
                try { 
                    $table->dropForeign(['product_id']); 
                } catch (\Exception $e) {}
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'book_id')) {
                $table->unsignedBigInteger('book_id')->nullable()->after('product_id');
            }
        });

        if (Schema::hasColumn('inventory_transactions', 'product_id')) {
            DB::statement('UPDATE inventory_transactions SET book_id = product_id');
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('products');
        Schema::rename('products_old', 'products');
        Schema::dropIfExists('books');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
