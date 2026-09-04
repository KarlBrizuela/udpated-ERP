<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\SalesOrder;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Dynamically resolve or fallback-create a ChartOfAccount model
     */
    public function getAccountBySymbol(string $code, string $nameKeyword, string $defaultName, string $type = 'Asset', string $category = 'Current Asset'): ChartOfAccount
    {
        // Prioritize matching by name keyword so account code changes (e.g. 1010 -> 1020) work dynamically
        $account = ChartOfAccount::where('name', 'like', "%{$nameKeyword}%")
            ->orWhere('code', $code)
            ->first();

        if (!$account) {
            $account = ChartOfAccount::firstOrCreate(
                ['name' => $defaultName],
                ['code' => $code, 'type' => $type, 'category' => $category]
            );
        }

        return $account;
    }

    /**
     * Post a journal entry for a Sales Order (POS or SI)
     * 
     * Target Flow:
     * DR Accounts Receivable (or Bank/Cash)
     * DR Cost of Sales
     * CR Sales Revenue
     * CR Inventory
     */
    public function postSalesOrderEntry(SalesOrder $order)
    {
        if ($order->type === 'complimentary') {
            return $this->postComplimentaryEntry($order);
        }

        return DB::transaction(function () use ($order) {
            $activeInvoice = null;
            if (in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                $activeInvoice = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'cancelled')->latest()->first();
            }

            $totalAmount = $activeInvoice ? $activeInvoice->total_amount : $order->total_amount;

            // 1. Determine Accounts dynamically via getAccountBySymbol helper
            $arAccount       = $this->getAccountBySymbol('1200', 'Receivable', 'Trade/Accounts Receivable', 'Asset', 'Current Asset');
            $cashAccount     = $this->getAccountBySymbol('1000', 'Cash in Bank', 'Cash in Bank', 'Asset', 'Current Asset');
            $cashHandAccount = $this->getAccountBySymbol('1010', 'Cash on Hand', 'Cash on Hand', 'Asset', 'Current Asset');
            $ewalletAccount  = $this->getAccountBySymbol('1020', 'E-Wallet', 'Cash Equivalents - E-Wallet', 'Asset', 'Current Asset');
            $salesAccount    = $this->getAccountBySymbol('4000', 'Sales', 'Sales - Books', 'Income', 'Revenue');

            $inventoryAccount = ChartOfAccount::where('code', '1300')
                ->orWhere('name', 'like', '%Inventory%')
                ->first();

            $cogsAccount = ChartOfAccount::where('code', '5000')
                ->orWhere('name', 'like', '%Cost of Sales%')
                ->orWhere('name', 'like', '%COGS%')
                ->first();

            // Determine which account to debit based on payment_method / order type
            $paymentMethod = strtolower($order->payment_method ?? '');

            if (in_array($paymentMethod, ['gcash', 'maya', 'paymaya', 'e-wallet', 'ewallet'])) {
                $debitAccount = $ewalletAccount;
            } elseif ($paymentMethod === 'cash') {
                $debitAccount = $cashHandAccount;
            } elseif (in_array($paymentMethod, ['bank_transfer', 'check', 'card'])) {
                $debitAccount = $cashAccount;
            } elseif ($order->type === 'calculator_pos' || $order->type === 'ecom_direct') {
                $debitAccount = $cashAccount;
            } else {
                $debitAccount = $arAccount;
            }

            // 2. Create Journal Entry Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'SALE',
                'date' => now(),
                'reference' => $order->so_number,
                'memo' => "Sales recognize for Order #" . $order->so_number,
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);
            
            // 3. Create Items (Compound Entry)
            
            // Line 1: DR Receivables/Cash (Total Amount)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $debitAccount->id,
                'debit' => $totalAmount,
                'credit' => 0,
                'memo' => "Payment/Receivable for " . $order->so_number,
            ]);

            // Line 2: CR Sales Revenue (Total Amount)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $totalAmount,
                'memo' => "Revenue recognition",
            ]);

            // Note: In this simplified phase, we might not have exact COGS/Inventory values 
            // per item since the 'Book' cost field is optional. 
            // For now, we only post the Revenue lines unless cost is available.
            
            $totalCost = 0;
            if ($activeInvoice) {
                $items = $activeInvoice->items()->with('book')->get();
                foreach ($items as $item) {
                    $book = $item->book;
                    if ($book && $book->cost > 0) {
                        $totalCost += ($book->cost * $item->quantity);
                    }
                }
            } else {
                $items = $order->items()->with('book')->get();
                foreach ($items as $item) {
                    $book = $item->book;
                    if ($book && $book->cost > 0) {
                        $totalCost += ($book->cost * $item->quantity);
                    }
                }
            }

            if ($totalCost > 0) {
                if (!$cogsAccount) {
                    $cogsAccount = ChartOfAccount::firstOrCreate(
                        ['code' => '5000'],
                        ['name' => 'Cost of Sales', 'type' => 'Expense', 'category' => 'COGS']
                    );
                }
                if (!$inventoryAccount) {
                    $inventoryAccount = ChartOfAccount::firstOrCreate(
                        ['code' => '1300'],
                        ['name' => 'Inventory - Books', 'type' => 'Asset', 'category' => 'Current Asset']
                    );
                }

                // Line 3: DR Cost of Sales
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $cogsAccount->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'memo' => "Cost of Sales for " . $order->so_number,
                ]);

                // Line 4: CR Inventory
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                    'memo' => "Inventory reduction",
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Receiving Report
     * 
     * Target Flow:
     * DR Inventory
     * CR Accounts Payable
     */
    public function postReceivingReportEntry(\App\Models\ReceivingReport $rr)
    {
        return DB::transaction(function () use ($rr) {
            $totalAmount = $rr->items->sum('total_cost');

            if ($totalAmount <= 0) return null;

            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no'      => $this->generateEntryNumber('RR'),
                'entry_type'    => 'RR',
                'date'          => $rr->received_date,
                'reference'     => $rr->rr_number,
                'memo'          => "Inventory receipt via RR #" . $rr->rr_number,
                'currency'      => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by'    => auth()->id() ?? 1,
                'status'        => 'posted',
            ]);

            // 2. Accounts
            $inventoryAccount = ChartOfAccount::where('code', '1300')->first();
            $apAccount        = ChartOfAccount::where('code', '2000')->first();

            // 3. Items — only post journal lines if both accounts exist
            if ($inventoryAccount && $apAccount) {
                // DR Inventory
                JournalEntryItem::create([
                    'journal_entry_id'    => $entry->id,
                    'chart_of_account_id' => $inventoryAccount->id,
                    'debit'               => $totalAmount,
                    'credit'              => 0,
                    'memo'                => "Increase inventory stock",
                ]);

                // CR Accounts Payable
                JournalEntryItem::create([
                    'journal_entry_id'    => $entry->id,
                    'chart_of_account_id' => $apAccount->id,
                    'debit'               => 0,
                    'credit'              => $totalAmount,
                    'memo'                => "Liability to supplier",
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Check Voucher (Supplier Payment)
     * 
     * Target Flow:
     * DR Accounts Payable
     * CR Cash in Bank
     */
    public function postCheckVoucherEntry(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $data should contain: amount, payee, check_no, date, memo, items (array)
            
            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('CV'),
                'entry_type' => 'CV',
                'date' => $data['date'] ?? now(),
                'reference' => $data['check_no'] ?? 'N/A',
                'memo' => $data['memo'] ?: ("Payment to " . ($data['payee'] ?? 'Supplier') . " via CV #" . ($data['check_no'] ?? '')),
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Process Items
            if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
                foreach ($data['items'] as $itemData) {
                    $name = $itemData['account_name'] ?? '';
                    $debit = $itemData['debit'] ?? 0;
                    $credit = $itemData['credit'] ?? 0;

                    if ($debit == 0 && $credit == 0) continue;

                    // Try to find a matching account
                    $account = ChartOfAccount::where('name', 'like', "%{$name}%")
                                ->orWhere('code', $name)
                                ->first();
                    
                    if (!$account) {
                        // Default to Cash in Bank if it's a credit, otherwise Accounts Payable
                        if ($credit > 0) {
                            $account = ChartOfAccount::where('code', '1000')->first();
                        } else {
                            $account = ChartOfAccount::where('code', '2000')->first();
                        }
                    }

                    if (!$account) {
                        $account = ChartOfAccount::first();
                    }

                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $account ? $account->id : 1,
                        'debit' => $debit,
                        'credit' => $credit,
                        'memo' => $name, // PRESERVE user input
                        'name' => $data['payee'] ?? null,
                    ]);
                }
            } else {
                // FALLBACK: Legacy hardcoded logic for simple amount if no items provided
                $apAccount = ChartOfAccount::where('code', '2000')->first();
                $bankAccount = ChartOfAccount::where('code', '1000')->first();

                // DR Accounts Payable
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $apAccount->id,
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'memo' => "Settlement of liability",
                ]);

                // CR Cash in Bank
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $bankAccount->id,
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'memo' => "Check payment",
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Cash Advance Disbursement
     * 
     * Target Flow:
     * DR Receivables - Employees (1150)
     * CR Petty Cash Fund (1020)
     */
    public function postCashAdvanceDisbursement(\App\Models\EmployeeCashAdvance $advance)
    {
        return DB::transaction(function () use ($advance) {
            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'CA',
                'date' => now(),
                'reference' => 'CA-' . $advance->id,
                'memo' => "Cash advance to " . $advance->employee_name,
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Accounts
            $receivableAccount = ChartOfAccount::firstOrCreate(
                ['code' => '1150'],
                ['name' => 'Receivables - Employees', 'type' => 'Asset', 'category' => 'Current Asset']
            );
            $pcfAccount = ChartOfAccount::where('code', '1020')->first();

            // 3. Items
            // DR Receivables - Employees
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $receivableAccount->id,
                'debit' => $advance->amount,
                'credit' => 0,
                'memo' => "Employee cash advance",
            ]);

            // CR Petty Cash Fund
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $pcfAccount->id,
                'debit' => 0,
                'credit' => $advance->amount,
                'memo' => "Disbursement from PCF",
            ]);

            return $entry;
        });
    }

    /**
     * Post a journal entry for Petty Cash Liquidation/Replenishment
     * 
     * Target Flow:
     * DR Various Expenses
     * CR Petty Cash Fund (1020)
     */
    public function postPettyCashLiquidation(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $data should contain: month, expenses (array of code => amount), total_amount, memo
            
            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'LIQ',
                'date' => now(), // Liquidation happens today
                'reference' => "PCV-" . str_replace('-', '', $data['month']),
                'memo' => $data['memo'] ?: "Petty Cash Liquidation for " . $data['month'],
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Account for Petty Cash Fund
            $pcfAccount = ChartOfAccount::where('code', '1020')->first();

            // 3. Expense Items (DR)
            foreach ($data['expenses'] as $code => $amount) {
                if ($amount <= 0) continue;

                $expenseAccount = ChartOfAccount::where('code', $code)->first();
                if (!$expenseAccount) {
                    $expenseAccount = ChartOfAccount::where('type', 'Expense')->first(); // Fallback
                }

                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $expenseAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'memo' => "PCV Expenses: " . $expenseAccount->name,
                ]);
            }

            // 4. Credit Petty Cash Fund (Total)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $pcfAccount->id,
                'debit' => 0,
                'credit' => $data['total_amount'],
                'memo' => "Petty Cash Replenishment/Liquidation",
            ]);

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Cash Advance Liquidation
     * 
     * Target Flow:
     * DR Various Expenses
     * CR Receivables - Employees (1150)
     */
    public function postLiquidationEntry(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $data should contain: amount_liquidated, employee_name, reference, memo, expenses (array of category => amount)
            
            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'LIQ',
                'date' => $data['date'] ?? now(),
                'reference' => $data['reference'] ?? 'N/A',
                'memo' => "Liquidation of CA for " . ($data['employee_name'] ?? 'Employee'),
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Accounts
            $receivableAccount = ChartOfAccount::where('code', '1150')->first();

            // 3. Expense Items
            foreach ($data['expenses'] as $category => $amount) {
                if ($amount <= 0) continue;

                // Simple mapping (can be expanded)
                $code = '6000'; // Default Supplies
                if (str_contains(strtoupper($category), 'TRAVEL')) $code = '6010';
                if (str_contains(strtoupper($category), 'COMMUNICATION')) $code = '6020';
                if (str_contains(strtoupper($category), 'REPRESENTATION')) $code = '6030';

                $expenseAccount = ChartOfAccount::where('code', $code)->first();

                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $expenseAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                    'memo' => "Liquidation: " . $category,
                ]);
            }

            // 4. Credit Receivable
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $receivableAccount->id,
                'debit' => 0,
                'credit' => $data['amount_liquidated'],
                'memo' => "Clearing of cash advance",
            ]);

            return $entry;
        });
    }

    /**
     * Convert a Journal Voucher Request into a Journal Entry and post it.
     * Creates a single DR to Accounts Receivable and per-item CR lines to Sales.
     */
    public function postJournalVoucherRequest(\App\Models\JournalVoucherRequest $jv)
    {
        return DB::transaction(function () use ($jv) {
            $total = $jv->total_amount ?: $jv->items->sum('amount');
            if ($total <= 0) return null;

            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'JV',
                'date' => $jv->date ?? now(),
                'reference' => $jv->jv_number,
                'memo' => "Journal Voucher from Request #" . $jv->jv_number,
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // Debit: Accounts Receivable (total)
            $arAccount = ChartOfAccount::where('code', '1200')->first();
            if (!$arAccount) {
                $arAccount = ChartOfAccount::where('name', 'like', '%Receivable%')->first()
                    ?? ChartOfAccount::where('type', 'Asset')->first();
            }

            $salesAccount = ChartOfAccount::where('code', '4000')->first();
            if (!$salesAccount) {
                $salesAccount = ChartOfAccount::where('name', 'like', '%Sales%')->first()
                    ?? ChartOfAccount::where('type', 'Income')->first();
            }

            if ($arAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $arAccount->id,
                    'debit' => $total,
                    'credit' => 0,
                    'memo' => "JV Request #" . $jv->jv_number,
                ]);
            }

            // Credit: create per-item credit lines (defaulting to Sales account)
            if ($salesAccount) {
                foreach ($jv->items as $item) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $salesAccount->id,
                        'debit' => 0,
                        'credit' => $item->amount,
                        'memo' => $item->remarks ?? ($item->reference_no ?? 'JV Item'),
                    ]);
                }
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for Freight Voucher (Advance Payment)
     * 
     * Freight Voucher is recorded as an advance payment to a supplier
     * Target Flow:
     * DR Prepaid Freight Expense (or Freight Prepayment Asset) - 1500
     * CR Accounts Payable (or Cash)
     */
    public function postFreightVoucherAdvance(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $data should contain: voucher_id, fv_number, amount, supplier_id
            
            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('FV'),
                'entry_type' => 'ADV', // Advance payment
                'date' => now(),
                'reference' => $data['fv_number'] ?? 'FV-' . $data['voucher_id'],
                'memo' => "Freight Voucher - Advance Payment #" . ($data['fv_number'] ?? $data['voucher_id']),
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Accounts
            // Prepaid Freight Expense (Asset Account) - typically 1500 or similar
            $prepaidFreightAccount = ChartOfAccount::where('code', '1500')->first();
            if (!$prepaidFreightAccount) {
                // Fallback to first Asset account
                $prepaidFreightAccount = ChartOfAccount::where('type', 'Asset')->first();
            }

            // Accounts Payable - typically 2000
            $payableAccount = ChartOfAccount::where('code', '2000')->first();
            if (!$payableAccount) {
                // Fallback to first Liability account
                $payableAccount = ChartOfAccount::where('type', 'Liability')->first();
            }

            // 3. Debit Prepaid Freight Expense
            if ($prepaidFreightAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $prepaidFreightAccount->id,
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'memo' => "Freight Advance Payment - " . ($data['fv_number'] ?? 'FV'),
                ]);
            }

            // 4. Credit Accounts Payable
            if ($payableAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $payableAccount->id,
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'memo' => "Freight Advance Payable to Supplier",
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Payment (COD or AR Collection)
     * 
     * Target Flow:
     * DR Cash/Bank (increase cash)
     * CR Accounts Receivable (reduce AR)
     */
    public function postPaymentEntry($salesOrder, $payment, $paymentMethod = 'cod_cash', $amount = null)
    {
        return DB::transaction(function () use ($salesOrder, $payment, $paymentMethod, $amount) {
            $paymentAmount = $amount ?? $payment->amount;

            // 1. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('PM'),
                'entry_type' => 'PMT',
                'date' => now(),
                'reference' => $payment->id ?? 'PMT-' . $salesOrder->id,
                'memo' => "Payment received for SO #" . $salesOrder->so_number . " via " . $paymentMethod,
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Determine Cash Account based on payment method
            $cashAccount = null;
            if ($paymentMethod === 'cod_cash') {
                // Cash on Hand
                $cashAccount = $this->getAccountBySymbol('1010', 'Cash on Hand', 'Cash on Hand', 'Asset', 'Current Asset');
            } elseif ($paymentMethod === 'bank_transfer' || $paymentMethod === 'check') {
                // Cash in Bank
                $cashAccount = $this->getAccountBySymbol('1000', 'Cash in Bank', 'Cash in Bank', 'Asset', 'Current Asset');
            } else {
                // Default to Cash on Hand
                $cashAccount = $this->getAccountBySymbol('1010', 'Cash on Hand', 'Cash on Hand', 'Asset', 'Current Asset');
            }

            // Fallback if accounts don't exist
            if (!$cashAccount) {
                $cashAccount = ChartOfAccount::where('type', 'Asset')->where('name', 'like', '%Cash%')->first();
            }

            // 3. Get AR Account
            $arAccount = ChartOfAccount::where('code', '1200')->first(); // Accounts Receivable

            // If required accounts don't exist, still create the entry but log it
            if (!$cashAccount || !$arAccount) {
                \Log::warning('Payment entry posting: Required accounts missing', [
                    'cash_account' => $cashAccount?->code,
                    'ar_account' => $arAccount?->code,
                ]);
            }

            // 4. Create Items
            if ($cashAccount) {
                // DR Cash
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $cashAccount->id,
                    'debit' => $paymentAmount,
                    'credit' => 0,
                    'memo' => "Cash receipt from SO #" . $salesOrder->so_number,
                ]);
            }

            if ($arAccount) {
                // CR Accounts Receivable
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $arAccount->id,
                    'debit' => 0,
                    'credit' => $paymentAmount,
                    'memo' => "AR collection for SO #" . $salesOrder->so_number,
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Complimentary Sales Order
     * 
     * Target Flow:
     * DR Complimentary & Donation Expense (Expense Account 5100)
     * CR Inventory - Books (Asset Account 1300)
     */
    public function postComplimentaryEntry(SalesOrder $order)
    {
        return DB::transaction(function () use ($order) {
            // Find or create Complimentary Expense Account
            $expenseAccount = ChartOfAccount::where('code', '5100')
                ->orWhere('name', 'like', '%Complimentary%')
                ->orWhere('name', 'like', '%Donation%')
                ->orWhere('name', 'like', '%Promotions%')
                ->first();
            if (!$expenseAccount) {
                $expenseAccount = ChartOfAccount::firstOrCreate(
                    ['code' => '5100'],
                    ['name' => 'Complimentary & Donation Expense', 'type' => 'Expense', 'category' => 'Operating Expense']
                );
            }

            // Find or create Inventory Account
            $inventoryAccount = ChartOfAccount::where('code', '1300')
                ->orWhere('name', 'like', '%Inventory%')
                ->first();
            if (!$inventoryAccount) {
                $inventoryAccount = ChartOfAccount::firstOrCreate(
                    ['code' => '1300'],
                    ['name' => 'Inventory - Books', 'type' => 'Asset', 'category' => 'Current Asset']
                );
            }

            // Calculate total valuation based on item cost or unit price
            $totalValuation = 0;
            $items = $order->items()->with('book')->get();
            foreach ($items as $item) {
                $cost = ($item->book && $item->book->cost > 0) ? $item->book->cost : ($item->unit_price > 0 ? $item->unit_price : 0);
                $totalValuation += ($cost * $item->quantity);
            }

            if ($totalValuation <= 0 && $order->total_amount > 0) {
                $totalValuation = $order->total_amount;
            }

            // 1. Create Journal Entry Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'EXPENSE',
                'date' => now(),
                'reference' => $order->so_number,
                'memo' => "Complimentary / Donation expense recognition for Order #" . $order->so_number,
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            if ($totalValuation > 0) {
                // Line 1: DR Complimentary & Donation Expense
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $expenseAccount->id,
                    'debit' => $totalValuation,
                    'credit' => 0,
                    'memo' => "Complimentary item expense for " . $order->so_number,
                ]);

                // Line 2: CR Inventory Reduction
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $totalValuation,
                    'memo' => "Inventory reduction for complimentary order " . $order->so_number,
                ]);
            }

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Sales Return
     * 
     * Target Flow:
     * DR Sales Returns and Allowances (Contra-Revenue)
     * CR Accounts Receivable (or Bank/Cash)
     * If physical inventory returned:
     * DR Inventory
     * CR Cost of Sales (COGS reversal)
     */
    public function postSalesReturnEntry(\App\Models\SalesReturn $return)
    {
        return DB::transaction(function () use ($return) {
            $order = $return->salesOrder;
            if (!$order) return null;

            // 1. Determine Accounts dynamically via getAccountBySymbol helper
            $salesReturnAccount = $this->getAccountBySymbol('4050', 'Sales Return', 'Sales Returns and Allowances', 'Income', 'Revenue');
            $arAccount          = $this->getAccountBySymbol('1200', 'Receivable', 'Trade/Accounts Receivable', 'Asset', 'Current Asset');
            $cashAccount        = $this->getAccountBySymbol('1000', 'Cash in Bank', 'Cash in Bank', 'Asset', 'Current Asset');
            $cashHandAccount    = $this->getAccountBySymbol('1010', 'Cash on Hand', 'Cash on Hand', 'Asset', 'Current Asset');
            $ewalletAccount     = $this->getAccountBySymbol('1020', 'E-Wallet', 'Cash Equivalents - E-Wallet', 'Asset', 'Current Asset');

            $paymentMethod = strtolower($order->payment_method ?? '');
            if (in_array($paymentMethod, ['gcash', 'maya', 'paymaya', 'e-wallet', 'ewallet'])) {
                $creditAccount = $ewalletAccount;
            } elseif ($paymentMethod === 'cash') {
                $creditAccount = $cashHandAccount;
            } elseif (in_array($paymentMethod, ['bank_transfer', 'check', 'card'])) {
                $creditAccount = $cashAccount;
            } elseif ($order->type === 'calculator_pos' || $order->type === 'ecom_direct') {
                $creditAccount = $cashAccount;
            } else {
                $creditAccount = $arAccount;
            }

            // 2. Create Header
            $entry = JournalEntry::create([
                'entry_no' => $this->generateEntryNumber('JV'),
                'entry_type' => 'RETURN',
                'date' => $return->return_date ?? now(),
                'reference' => $return->return_no,
                'memo' => "Sales Return recognition for Return #" . $return->return_no . " (Order #" . $order->so_number . ")",
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 3. Create items (Compound Entry)
            // Line 1: DR Sales Returns and Allowances (Refund amount)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $salesReturnAccount->id,
                'debit' => $return->refund_amount,
                'credit' => 0,
                'memo' => "Reversal of revenue for " . $order->so_number,
            ]);

            // Line 2: CR Accounts Receivable / Cash (Refund amount)
            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $creditAccount->id,
                'debit' => 0,
                'credit' => $return->refund_amount,
                'memo' => "Refund or credit to customer",
            ]);

            // 4. Reverse COGS / Restore Inventory if inventory was returned
            if ($return->inventory_restored) {
                $inventoryAccount = ChartOfAccount::where('code', '1300')
                    ->orWhere('name', 'like', '%Inventory%')
                    ->first();
                $cogsAccount = ChartOfAccount::where('code', '5000')
                    ->orWhere('name', 'like', '%Cost of Sales%')
                    ->orWhere('name', 'like', '%COGS%')
                    ->first();

                $totalCost = 0;
                foreach ($return->items as $item) {
                    $book = $item->book;
                    if ($book && $book->cost > 0) {
                        $totalCost += ($book->cost * $item->returned_qty);
                    }
                }

                if ($totalCost > 0) {
                    if (!$cogsAccount) {
                        $cogsAccount = ChartOfAccount::firstOrCreate(
                            ['code' => '5000'],
                            ['name' => 'Cost of Sales', 'type' => 'Expense', 'category' => 'COGS']
                        );
                    }
                    if (!$inventoryAccount) {
                        $inventoryAccount = ChartOfAccount::firstOrCreate(
                            ['code' => '1300'],
                            ['name' => 'Inventory - Books', 'type' => 'Asset', 'category' => 'Current Asset']
                        );
                    }

                    // Line 3: DR Inventory (Restoring asset)
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $inventoryAccount->id,
                        'debit' => $totalCost,
                        'credit' => 0,
                        'memo' => "Restored inventory stock value",
                    ]);

                    // Line 4: CR Cost of Sales (Reducing expense)
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $cogsAccount->id,
                        'debit' => 0,
                        'credit' => $totalCost,
                        'memo' => "Reversal of COGS for returned items",
                    ]);
                }
            }

            // Link journal entry back to return
            $return->update(['journal_entry_id' => $entry->id]);

            return $entry;
        });
    }

    /**
     * Post a journal entry for a Purchase Return (PR)
     * 
     * Target Flow:
     * DR Accounts Payable (for outstanding unpaid balance portion)
     * DR Supplier Receivable / Refund Receivable (for paid portion)
     * CR Inventory
     */
    public function postPurchaseReturnEntry(\App\Models\PurchaseReturn $purchaseReturn)
    {
        return DB::transaction(function () use ($purchaseReturn) {
            $totalRefund = $purchaseReturn->refund_amount;

            // 1. Create the base Journal Entry header
            $entryNo = $this->generateEntryNumber('JV'); // Using general JV prefix
            $entry = JournalEntry::create([
                'entry_no' => $entryNo,
                'date' => $purchaseReturn->return_date,
                'entry_type' => 'JV',
                'memo' => "Purchase Return Reversal - " . $purchaseReturn->return_no,
                'reference' => $purchaseReturn->return_no,
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            // 2. Resolve Chart of Accounts
            // Accounts Payable - typically 2000
            $payableAccount = ChartOfAccount::where('code', '2000')
                ->orWhere('code', '2010')
                ->orWhere('name', 'like', '%Accounts Payable%')
                ->first();
            if (!$payableAccount) {
                $payableAccount = ChartOfAccount::where('type', 'Liability')->first();
            }

            // Refund/Supplier Receivable (Asset) - typically 1210 or 1200
            $receivableAccount = ChartOfAccount::where('code', '1210')
                ->orWhere('name', 'like', '%Supplier Receivable%')
                ->orWhere('name', 'like', '%Refund%Receivable%')
                ->first();
            if (!$receivableAccount) {
                $receivableAccount = ChartOfAccount::where('code', '1200')
                    ->orWhere('name', 'like', '%Accounts Receivable%')
                    ->first();
            }

            // Inventory Asset - typically 1300
            $inventoryAccount = ChartOfAccount::where('code', '1300')
                ->orWhere('name', 'like', '%Inventory%')
                ->first();
            if (!$inventoryAccount) {
                $inventoryAccount = ChartOfAccount::where('type', 'Asset')->first();
            }

            // 3. Determine unpaid outstanding portion vs already paid portion
            $apDebitAmount = 0.00;
            $arDebitAmount = 0.00;

            if ($purchaseReturn->supplierInvoice) {
                $invoice = $purchaseReturn->supplierInvoice;
                $outstandingBalance = $invoice->balance; // total_amount - amount_paid

                if ($outstandingBalance >= $totalRefund) {
                    // Entire refund reduces outstanding payable balance
                    $apDebitAmount = $totalRefund;
                } else {
                    // Reduces outstanding payable to zero, rest goes to receivable
                    $apDebitAmount = $outstandingBalance;
                    $arDebitAmount = $totalRefund - $outstandingBalance;
                }
            } else {
                // If no supplier invoice is linked, default to full accounts payable reduction
                $apDebitAmount = $totalRefund;
            }

            // 4. Create DEBIT items
            // A. Debit Accounts Payable if applicable
            if ($apDebitAmount > 0 && $payableAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $payableAccount->id,
                    'debit' => $apDebitAmount,
                    'credit' => 0,
                    'memo' => "Debit Accounts Payable for returned goods - " . $purchaseReturn->return_no,
                ]);
            }

            // B. Debit Supplier/Refund Receivable if applicable
            if ($arDebitAmount > 0 && $receivableAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $receivableAccount->id,
                    'debit' => $arDebitAmount,
                    'credit' => 0,
                    'memo' => "Debit Refund Receivable for paid returned goods - " . $purchaseReturn->return_no,
                ]);
            }

            // 5. Create CREDIT item
            // Credit Inventory for the returned cost
            if ($inventoryAccount) {
                JournalEntryItem::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $totalRefund,
                    'memo' => "Credit Inventory for returned goods - " . $purchaseReturn->return_no,
                ]);
            }

            // Link journal entry back to purchase return
            $purchaseReturn->update(['journal_entry_id' => $entry->id]);

            return $entry;
        });
    }

    private function generateEntryNumber($prefix)
    {
        $year = now()->year;
        $fullPrefix = "{$prefix}-{$year}";
        $lastEntry = JournalEntry::where('entry_no', 'like', "{$fullPrefix}-%")->orderBy('entry_no', 'desc')->first();
        
        if ($lastEntry) {
            $lastSeq = (int) substr($lastEntry->entry_no, -4);
            $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }
        return "{$fullPrefix}-{$newSeq}";
    }
}
