<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mis_service_requests', function (Blueprint $table) {
            $table->id('service_req_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('requestor_name');
            $table->date('date');
            $table->text('nature_of_request');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mis_service_requests');
    }
};
