<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_cash_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Snapshot of employee details at time of request
            $table->string('employee_name');
            $table->string('employee_number');
            $table->string('department');
            $table->string('position');
            
            // Request Details
            $table->string('request_type')->default('Official Business'); // e.g., Travel, Official Business, Salary Loan
            $table->decimal('amount', 15, 2);
            $table->text('purpose');
            $table->date('date_needed');
            
            // Repayment/Liquidation Logic
            $table->string('repayment_method')->default('Cash'); // e.g., Salary Deduction, Cash
            $table->integer('installment_count')->default(1);
            $table->date('repayment_start_date')->nullable();
            $table->boolean('is_liquidation_required')->default(false);
            
            // Disbursement Details (to be filled by accounting)
            $table->string('disbursement_method')->nullable(); // e.g., Check, Cash, Bank Transfer
            $table->string('disbursement_reference')->nullable(); // Check #, Ref #
            $table->date('disbursement_date')->nullable();
            $table->string('gl_account_code')->nullable(); // For accounting integration
            
            // Status and Workflow
            // Simplified workflow: no draft stage
            $table->enum('status', [
                'pending approval', 
                'Pending Final Approval', 
                'forwarded to accounting', 
                'received', 
                'rejected'
            ])->default('pending approval');
            
            $table->text('rejection_reason')->nullable();
            
            // Approval Tracking
            $table->foreignId('approved_by_manager')->nullable()->constrained('users');
            $table->timestamp('manager_approved_at')->nullable();
            
            $table->foreignId('approved_by_director')->nullable()->constrained('users');
            $table->timestamp('director_approved_at')->nullable();
            
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            
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
        Schema::dropIfExists('employee_cash_advances');
    }
};
