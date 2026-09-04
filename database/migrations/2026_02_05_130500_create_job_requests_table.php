<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();
            $table->string('job_no')->nullable();
            $table->string('project_title')->nullable();
            $table->text('specifications')->nullable();
            $table->date('due_date')->nullable();
            $table->date('date')->nullable(); // Request date
            
            // Foreign key to departments table
            // Assuming the select input 'department' corresponds to dept_id
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->foreign('department_id')->references('dept_id')->on('departments')->onDelete('set null');
            $table->string('status')->default('Pending'); // Add status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
