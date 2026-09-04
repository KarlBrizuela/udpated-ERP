<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mis_qb_requests', function (Blueprint $table) {
            $table->id('qb_req_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_item_name');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('mis_qb_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qb_req_id')->constrained('mis_qb_requests', 'qb_req_id')->onDelete('cascade');
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mis_qb_items');
        Schema::dropIfExists('mis_qb_requests');
    }
};
