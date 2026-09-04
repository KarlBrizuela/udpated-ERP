<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PettyCashVoucher;
use App\Models\ChartOfAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Get the sidebar for the current user based on their division
     */
    protected function getUserSidebar()
    {
        // 1. Check if sidebar parameter is explicitly passed in the request
        if (request()->has('sidebar')) {
            $sidebar = request()->query('sidebar');
            if (in_array($sidebar, ['admin-finance', 'marketing', 'production', 'unified'])) {
                session(['petty_cash_sidebar' => $sidebar]);
                return $sidebar;
            }
        }

        // 2. Check if there's a persisted sidebar in the session
        if (session()->has('petty_cash_sidebar')) {
            return session('petty_cash_sidebar');
        }

        $user = auth()->user();
        if (!$user) return 'admin-finance';

        $division = $user->division ?? 'Admin & Finance Division';
        
        if (strpos($division, 'Marketing') !== false) {
            return 'marketing';
        } elseif (strpos($division, 'Production') !== false) {
            return 'production';
        }
        
        return 'admin-finance';
    }

    public function index()
    {
        $vouchers = PettyCashVoucher::with('creator')
            ->withSum('items', 'amount')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin-finance.petty-cash.index', [
            'title' => 'Petty Cash Vouchers',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'vouchers' => $vouchers
        ]);
    }

    public function create()
    {
        $expenseAccounts = ChartOfAccount::where('is_active', 1)
            ->where('is_postable', 1)
            ->where(function($query) {
                $query->where('type', 'Expense')
                      ->orWhereIn('code', ['6000', '6010', '6020', '6030']);
            })
            ->orderBy('code')
            ->get();

        // Generate automatic PCV number
        $lastVoucher = PettyCashVoucher::orderBy('id', 'desc')->first();
        $nextNum = 1;
        if ($lastVoucher && preg_match('/PCV-\d{4}-(\d+)/', $lastVoucher->pcv_number, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $pcvNumber = 'PCV-' . date('Y') . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        return view('admin-finance.petty-cash.create', [
            'title' => 'New Petty Cash Voucher',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'expenseAccounts' => $expenseAccounts,
            'pcvNumber' => $pcvNumber,
        ]);
    }

    public function store(Request $request)
    {
        // Automatically generate PCV number to override any user input
        $lastVoucher = PettyCashVoucher::orderBy('id', 'desc')->first();
        $nextNum = 1;
        if ($lastVoucher && preg_match('/PCV-\d{4}-(\d+)/', $lastVoucher->pcv_number, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $pcvNumber = 'PCV-' . date('Y') . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        $request->merge(['pcv_number' => $pcvNumber]);

        $validated = $request->validate([
            'pcv_number' => 'required|unique:petty_cash_vouchers,pcv_number',
            'type' => 'required|string|in:fund,freight',
            'date' => 'required|date',
            'pay_to' => 'required|string',
            'approved_by' => 'nullable|string',
            'received_by' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.particulars' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Enforce maximum 1000 limit
        $totalAmount = collect($request->items)->sum('amount');
        if ($totalAmount > 1000) {
            return back()->withInput()->with('error', 'Petty Cash Voucher total cannot exceed ₱1,000. For amounts above ₱1,000, please create a Material Request or Cash Advance.');
        }

        try {
            DB::transaction(function () use ($validated) {
                $voucher = PettyCashVoucher::create([
                    'pcv_number' => $validated['pcv_number'],
                    'type'       => $validated['type'],
                    'date'       => $validated['date'],
                    'pay_to'     => $validated['pay_to'],
                    'approved_by' => $validated['approved_by'] ?? null,
                    'received_by' => $validated['received_by'] ?? null,
                    'created_by' => auth()->id(),
                    'status'     => 'pending',
                ]);

                foreach ($validated['items'] as $item) {
                    $voucher->items()->create($item);
                }
            });

            return redirect()->route('admin-finance.petty-cash.index')->with('success', 'Petty Cash Voucher created successfully and sent to Cashier for approval.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $voucher = PettyCashVoucher::with(['items.expenseAccount', 'creator'])->findOrFail($id);

        return view('admin-finance.petty-cash.show', [
            'title' => 'Petty Cash Voucher Details',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'voucher' => $voucher
        ]);
    }

    public function summary(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        
        $vouchers = PettyCashVoucher::where('date', 'like', "$month%")
            ->with(['items'])
            ->withSum('items', 'amount')
            ->get();

        return view('admin-finance.petty-cash.summary', [
            'title' => 'Petty Cash Summary (' . $month . ')',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'vouchers' => $vouchers,
            'selectedMonth' => $month
        ]);
    }

    public function liquidate(Request $request)
    {
        $month = $request->input('month');
        $vouchers = PettyCashVoucher::where('status', 'completed')
            ->where('date', 'like', "$month%")
            ->with('items.expenseAccount')
            ->get();

        if ($vouchers->isEmpty()) {
            return back()->with('error', 'No completed vouchers found for this month to liquidate.');
        }

        try {
            DB::transaction(function () use ($vouchers, $month) {
                // Sum all items across all vouchers
                $totalAmount = $vouchers->flatMap->items->sum('amount');

                if ($totalAmount <= 0) {
                    throw new \Exception('Total amount must be greater than zero.');
                }

                // Post as a single petty cash expense line (code 6000 = Petty Cash Expenses)
                // AccountingService will fall back to first Expense account if 6000 doesn't exist
                $entry = $this->accounting->postPettyCashLiquidation([
                    'month'        => $month,
                    'expenses'     => ['6000' => $totalAmount],
                    'total_amount' => $totalAmount,
                    'memo'         => "Petty Cash Liquidation - " . $month,
                ]);

                // Mark all vouchers as liquidated
                foreach ($vouchers as $voucher) {
                    $voucher->update([
                        'status'           => 'liquidated',
                        'journal_entry_id' => $entry->id,
                    ]);
                }
            });

            return redirect()->route('admin-finance.petty-cash.index')->with('success', 'Petty Cash Vouchers liquidated and journal entry posted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to liquidate: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $voucher = PettyCashVoucher::findOrFail($id);
        $voucher->items()->delete();
        $voucher->delete();

        return redirect()->route('admin-finance.petty-cash.index')->with('success', 'Petty Cash Voucher deleted successfully.');
    }

    public function cashierIndex()
    {
        $vouchers = PettyCashVoucher::with('creator')
            ->withSum('items', 'amount')
            ->withCount('items')
            ->latest()
            ->get();

        return view('admin-finance.accounting.cashier.index', [
            'title' => 'Cashier Petty Cash Approvals',
            'role' => 'Cashier',
            'sidebar' => 'admin-finance',
            'vouchers' => $vouchers
        ]);
    }

    public function cashierApprove($id)
    {
        if (auth()->user()->position !== 'Cashier' && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only the Cashier can approve Petty Cash Vouchers.');
        }

        $voucher = PettyCashVoucher::findOrFail($id);
        $voucher->update(['status' => 'ongoing']);
        return redirect()->back()->with('success', 'Petty Cash Voucher #' . $voucher->pcv_number . ' has been approved and status updated to ongoing.');
    }

    public function cashierReject($id)
    {
        if (auth()->user()->position !== 'Cashier' && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only the Cashier can reject Petty Cash Vouchers.');
        }

        $voucher = PettyCashVoucher::findOrFail($id);
        $voucher->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Petty Cash Voucher #' . $voucher->pcv_number . ' has been rejected.');
    }

    public function cashierComplete($id)
    {
        if (auth()->user()->position !== 'Cashier' && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only the Cashier can mark this voucher as completed.');
        }

        $voucher = PettyCashVoucher::findOrFail($id);
        if (!$voucher->proof_attachment) {
            return redirect()->back()->with('error', 'Cannot complete. No proof attachment uploaded yet.');
        }
        $voucher->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Petty Cash Voucher #' . $voucher->pcv_number . ' has been completed successfully.');
    }

    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'proof_file' => 'required|file|max:5120', // Limit to 5MB
        ]);

        $voucher = PettyCashVoucher::findOrFail($id);

        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('petty_cash', 'public');
            $voucher->update(['proof_attachment' => $path]);
        }

        return redirect()->back()->with('success', 'Proof of payment / cheque copy attached successfully.');
    }
}
