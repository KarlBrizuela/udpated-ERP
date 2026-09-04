<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('journal_voucher_requests')) {
            Schema::create('journal_voucher_requests', function (Blueprint $group) {
                $group->id();
                $group->string('jv_number')->unique();
                $group->date('date');
                $group->string('status')->default('draft'); // draft, pending_manager, pending_accounting, posted
                $group->foreignId('requested_by')->constrained('users');
                $group->foreignId('approved_by')->nullable()->constrained('users');
                $group->text('reason')->nullable();
                $group->string('category')->nullable(); // Freight, Account Statement, Ads/Promo
                $group->decimal('total_amount', 15, 2)->default(0);
                $group->string('documents')->nullable(); // e.g. "Billing Request"
                $group->text('accounting_remarks')->nullable();
                $group->timestamps();
            });
        }

        if (!Schema::hasTable('journal_voucher_items')) {
            Schema::create('journal_voucher_items', function (Blueprint $group) {
                $group->id();
                $group->foreignId('jv_request_id')->constrained('journal_voucher_requests')->onDelete('cascade');
                $group->string('reference_no')->nullable();
                $group->string('customer_name');
                $group->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id');
                $group->decimal('amount', 15, 2);
                $group->string('remarks')->nullable(); // e.g. "QB Entry"
                $group->string('type')->default('item'); // allows grouping in summary report
                $group->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('journal_voucher_items');
        Schema::dropIfExists('journal_voucher_requests');
    }
};
