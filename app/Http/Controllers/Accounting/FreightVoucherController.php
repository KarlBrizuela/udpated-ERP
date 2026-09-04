<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\FreightVoucher;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FreightVoucherController extends Controller
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
                session(['freight_voucher_sidebar' => $sidebar]);
                return $sidebar;
            }
        }

        // 2. Check if there's a persisted sidebar in the session
        if (session()->has('freight_voucher_sidebar')) {
            return session('freight_voucher_sidebar');
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
        $vouchers = FreightVoucher::with('creator', 'supplier')
            ->withSum('items', 'amount')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin-finance.freight-voucher.index', [
            'title' => 'Freight Vouchers',
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
                      ->orWhereIn('code', ['5000', '5010', '5020', '5030', '6000']);
            })
            ->orderBy('code')
            ->get();

        return view('admin-finance.freight-voucher.create', [
            'title' => 'New Freight Voucher',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'expenseAccounts' => $expenseAccounts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fv_number' => 'required|unique:freight_vouchers,fv_number',
            'date' => 'required|date',
            'pay_to' => 'required|string',
            'approved_by' => 'nullable|string',
            'received_by' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.particulars' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $voucher = FreightVoucher::create([
                    'fv_number' => $validated['fv_number'],
                    'date'       => $validated['date'],
                    'pay_to' => $validated['pay_to'],
                    'approved_by' => $validated['approved_by'] ?? null,
                    'received_by' => $validated['received_by'] ?? null,
                    'created_by' => auth()->id(),
                    'status'     => 'open',
                ]);

                foreach ($validated['items'] as $item) {
                    $voucher->items()->create($item);
                }

                // Post as advance payment (Asset: Freight Prepayment)
                $totalAmount = collect($validated['items'])->sum('amount');
                
                $this->accounting->postFreightVoucherAdvance([
                    'voucher_id'   => $voucher->id,
                    'fv_number'    => $validated['fv_number'],
                    'amount'       => $totalAmount,
                    'supplier_id'  => $validated['supplier_id'],
                ]);
            });

            return redirect()->route('admin-finance.freight-voucher.index')->with('success', 'Freight Voucher created successfully as advance payment.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $voucher = FreightVoucher::with(['items.expenseAccount', 'creator', 'supplier'])->findOrFail($id);

        return view('admin-finance.freight-voucher.show', [
            'title' => 'Freight Voucher Details',
            'role' => 'Finance Manager',
            'sidebar' => $this->getUserSidebar(),
            'voucher' => $voucher
        ]);
    }

    public function destroy($id)
    {
        $voucher = FreightVoucher::findOrFail($id);
        $voucher->items()->delete();
        $voucher->delete();

        return redirect()->route('admin-finance.freight-voucher.index')->with('success', 'Freight Voucher deleted successfully.');
    }
}
