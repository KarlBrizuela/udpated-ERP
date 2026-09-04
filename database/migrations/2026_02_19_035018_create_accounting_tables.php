<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Chart of Accounts
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['Asset', 'Liability', 'Equity', 'Income', 'Expense']);
            $table->string('category')->nullable(); // e.g., Current Asset, Fixed Asset
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Journal Entries (Header)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_no')->unique(); // e.g., JV-2026-0001
            $table->date('date');
            $table->string('reference')->nullable();
            $table->text('memo')->nullable();
            $table->string('currency')->default('PHP');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->foreignId('created_by')->constrained('users');
            $table->string('status')->default('posted'); // posted, draft, void
            $table->timestamps();
        });

        // 3. Journal Entry Items (Lines)
        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('memo')->nullable();
            $table->string('name')->nullable(); // For entity tagging (Customer/Supplier/Employee)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_entry_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
}
