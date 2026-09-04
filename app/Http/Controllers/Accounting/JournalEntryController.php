<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = JournalEntry::with('creator');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('entry_no', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%")
                  ->orWhere('entry_type', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->where('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('date', '<=', $dateTo);
        }

        $entries = $query->latest()->paginate(10)->withQueryString();

        return view('accounting.journal.index', compact('entries'));
    }

    public function show($id)
    {
        $entry = JournalEntry::with(['creator', 'items.account'])->findOrFail($id);
        return view('accounting.journal.show', compact('entry'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $entry = JournalEntry::findOrFail($id);
            
            // Delete associated items first (handled by DB if cascade set, but safe to do here)
            JournalEntryItem::where('journal_entry_id', $entry->id)->delete();
            $entry->delete();
            
            DB::commit();
            return redirect()->route('accounting.journal.index')->with('success', 'Journal Entry deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('accounting.journal.index')->with('error', 'Error deleting entry: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $bankAccounts = \App\Models\CompanyBankAccount::orderBy('bank_name')->get();
        
        foreach ($bankAccounts as $bank) {
            $code = $bank->account_code ?: ('BANK-' . $bank->id);
            $name = 'Bank: ' . $bank->bank_name . ' (' . $bank->account_number . ' - ' . $bank->account_name . ')';
            
            $existingAccount = ChartOfAccount::withTrashed()->where('code', $code)->first();
            if ($existingAccount) {
                $existingAccount->update([
                    'name' => $name,
                    'type' => 'Asset',
                    'category' => 'Cash & Bank',
                    'is_active' => 1,
                    'is_postable' => 1,
                    'deleted_at' => null,
                ]);
            } else {
                ChartOfAccount::create([
                    'code' => $code,
                    'name' => $name,
                    'type' => 'Asset',
                    'category' => 'Cash & Bank',
                    'is_active' => 1,
                    'is_postable' => 1,
                ]);
            }
        }

        // Safely check parent column schema
        $hasParentId = \Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'parent_id');
        $hasParentCode = \Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'parent_code');
        $hasIsPostable = \Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'is_postable');

        if ($hasParentId) {
            $parentIds = ChartOfAccount::whereNotNull('parent_id')->pluck('parent_id')->unique()->toArray();
            if (!empty($parentIds) && $hasIsPostable) {
                ChartOfAccount::whereIn('id', $parentIds)->update(['is_postable' => 0]);
            }
        } elseif ($hasParentCode) {
            $parentCodes = ChartOfAccount::whereNotNull('parent_code')->pluck('parent_code')->unique()->toArray();
            if (!empty($parentCodes) && $hasIsPostable) {
                ChartOfAccount::whereIn('code', $parentCodes)->update(['is_postable' => 0]);
            }
        }

        // Fetch all active, postable accounts from Chart of Accounts sorted hierarchically
        $query = ChartOfAccount::select('chart_of_accounts.*');
        
        if ($hasParentId) {
            $query->leftJoin('chart_of_accounts as p', 'chart_of_accounts.parent_id', '=', 'p.id');
        }

        $query->where('chart_of_accounts.is_active', 1);
        
        if ($hasIsPostable) {
            $query->where('chart_of_accounts.is_postable', 1);
        }

        if ($hasParentId) {
            $query->orderByRaw("
                CASE chart_of_accounts.type
                    WHEN 'Asset' THEN 1
                    WHEN 'Liability' THEN 2
                    WHEN 'Equity' THEN 3
                    WHEN 'Income' THEN 4
                    WHEN 'Expense' THEN 5
                    ELSE 6
                END,
                COALESCE(p.display_order, chart_of_accounts.display_order),
                COALESCE(p.code, chart_of_accounts.code),
                chart_of_accounts.display_order,
                chart_of_accounts.code
            ");
        } else {
            $query->orderByRaw("
                CASE chart_of_accounts.type
                    WHEN 'Asset' THEN 1
                    WHEN 'Liability' THEN 2
                    WHEN 'Equity' THEN 3
                    WHEN 'Income' THEN 4
                    WHEN 'Expense' THEN 5
                    ELSE 6
                END,
                chart_of_accounts.display_order,
                chart_of_accounts.code
            ");
        }

        $accounts = $query->get();
        
        // Generate next Entry No (JV-YEAR-SEQ)
        $year = now()->year;
        $prefix = "JV-{$year}";
        $lastEntry = JournalEntry::where('entry_no', 'like', "{$prefix}-%")->orderBy('entry_no', 'desc')->first();
        
        if ($lastEntry) {
            $lastSeq = (int) substr($lastEntry->entry_no, -4);
            $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }
        $entryNo = "{$prefix}-{$newSeq}";

        // Fetch Customers and Suppliers names for GJE
        $customers = \App\Models\Customer::select('customer_name as name')->whereNotNull('customer_name')->get();
        $suppliers = \App\Models\Supplier::select('company_name as name')->whereNotNull('company_name')->get();
        $names = $customers->concat($suppliers)
            ->pluck('name')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return view('accounting.journal.create', compact('accounts', 'entryNo', 'names'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_no' => 'required|unique:journal_entries,entry_no',
            'date' => 'required|date',
            'currency' => 'nullable|string|in:PHP,USD',
            'exchange_rate' => 'nullable|numeric|min:0.0001',
            'reference' => 'nullable|string',
            'memo' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
            'items.*.memo' => 'nullable|string',
            'items.*.name' => 'nullable|string',
        ]);

        // Validate Balances
        $totalDebit = collect($request->items)->sum('debit');
        $totalCredit = collect($request->items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', 'The journal entry must balance. Total Debit: ' . number_format($totalDebit, 2) . ', Total Credit: ' . number_format($totalCredit, 2));
        }

        if ($totalDebit <= 0) {
            return back()->withInput()->with('error', 'Total amount must be greater than zero.');
        }

        try {
            DB::beginTransaction();

            $entry = JournalEntry::create([
                'entry_no' => $request->entry_no,
                'entry_type' => 'GJE',
                'date' => $request->date,
                'reference' => $request->reference,
                'memo' => $request->memo,
                'currency' => $request->currency ?: 'PHP',
                'exchange_rate' => $request->exchange_rate ?: 1.0000,
                'created_by' => auth()->id(),
                'status' => 'posted',
            ]);

            foreach ($request->items as $item) {
                if (($item['debit'] ?? 0) > 0 || ($item['credit'] ?? 0) > 0) {
                    JournalEntryItem::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $item['account_id'],
                        'debit' => $item['debit'] ?? 0,
                        'credit' => $item['credit'] ?? 0,
                        'memo' => $item['memo'] ?? null,
                        'name' => $item['name'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('accounting.journal.index')->with('success', 'Journal Entry ' . $entry->entry_no . ' posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error posting entry: ' . $e->getMessage());
        }
    }
}
