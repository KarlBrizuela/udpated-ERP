<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryItem;

class GeneralLedgerController extends Controller
{
    /**
     * Display the General Ledger report.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $sidebar = 'admin-finance';
        $role = 'Finance Manager';

        // 1. Fetch active, postable accounts (Asset, Liability, Equity only) sorted by type hierarchy, display_order, and code
        $accounts = ChartOfAccount::where('is_active', 1)
            ->where('is_postable', 1)
            ->whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->orderByRaw("
                CASE type
                    WHEN 'Asset' THEN 1
                    WHEN 'Liability' THEN 2
                    WHEN 'Equity' THEN 3
                    ELSE 4
                END,
                display_order,
                code
            ")
            ->get();

        // 2. Determine selected account
        $accountId = $request->input('account_id');
        if (!$accountId && $accounts->isNotEmpty()) {
            $accountId = $accounts->first()->id;
        }

        $selectedAccount = null;
        if ($accountId) {
            $selectedAccount = ChartOfAccount::find($accountId);
        }

        // 3. Determine date range (default to current year start to current date)
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $totalDebits = 0;
        $totalCredits = 0;
        $items = collect([]);

        if ($selectedAccount) {
            // 4. Calculate Aggregate Totals for the entire selected period
            $totalDebits = JournalEntryItem::where('chart_of_account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->where('status', 'posted');
                })
                ->sum('debit') ?: 0;

            $totalCredits = JournalEntryItem::where('chart_of_account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->where('status', 'posted');
                })
                ->sum('credit') ?: 0;

            // 5. Set up paginated query for chronological journal postings
            $paginatedQuery = JournalEntryItem::where('chart_of_account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->where('status', 'posted');
                })
                ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
                ->orderBy('journal_entries.date')
                ->orderBy('journal_entries.id')
                ->orderBy('journal_entry_items.id')
                ->select('journal_entry_items.*')
                ->with(['journalEntry']);

            $items = $paginatedQuery->paginate(25);
        }

        return view('admin-finance.accounting.general-ledger', compact(
            'accounts',
            'accountId',
            'selectedAccount',
            'startDate',
            'endDate',
            'items',
            'totalDebits',
            'totalCredits',
            'sidebar',
            'role'
        ));
    }
}
