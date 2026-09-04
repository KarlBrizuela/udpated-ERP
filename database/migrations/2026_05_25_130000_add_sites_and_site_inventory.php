<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('location')->nullable();
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('site_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('reorder_point')->nullable();
            $table->integer('max_stock')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of site and book
            $table->unique(['site_id', 'book_id']);
        });

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_site_id')->constrained('sites')->onDelete('restrict');
            $table->foreignId('to_site_id')->constrained('sites')->onDelete('restrict');
            $table->foreignId('book_id')->constrained('books')->onDelete('restrict');
            $table->integer('quantity');
            $table->string('status')->default('pending'); // pending, completed, rejected
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('site_inventory');
        Schema::dropIfExists('sites');
    }
};
