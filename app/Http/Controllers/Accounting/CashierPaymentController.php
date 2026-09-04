<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\RiderCollection;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierPaymentController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Show all collections awaiting verification
     */
    public function index()
    {
        $collections = RiderCollection::whereIn('status', ['handed_over', 'collected'])
            ->with('salesOrder', 'salesOrder.customer', 'rider')
            ->orderBy('handed_over_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'awaiting_verification' => RiderCollection::whereIn('status', ['handed_over', 'collected'])->count(),
            'total_pending' => RiderCollection::whereIn('status', ['collected', 'handed_over'])
                ->sum('amount_collected'),
            'verified_today' => RiderCollection::where('status', 'verified')
                ->whereDate('verified_at', now())
                ->count(),
            'total_verified_today' => RiderCollection::where('status', 'verified')
                ->whereDate('verified_at', now())
                ->sum('amount_collected'),
        ];

        return view('accounting.cashier.collections-index', [
            'collections' => $collections,
            'stats' => $stats,
            'title' => 'COD Collections Verification',
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance'
        ]);
    }

    /**
     * Show collection details for verification
     */
    public function show($id)
    {
        $collection = RiderCollection::with('salesOrder', 'salesOrder.customer', 'salesOrder.items', 'rider')
            ->findOrFail($id);

        $discrepancy = $collection->getDiscrepancyAmount();
        $hasDiscrepancy = $collection->hasDiscrepancy();

        return view('accounting.cashier.collection-show', [
            'collection' => $collection,
            'discrepancy' => $discrepancy,
            'hasDiscrepancy' => $hasDiscrepancy,
            'title' => 'Verify Payment Collection',
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance'
        ]);
    }

    /**
     * Verify and record payment from COD collection
     */
    public function verify(Request $request, $id)
    {
        $collection = RiderCollection::findOrFail($id);

        // Check authorization
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('admin_finance.accounting')) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        if ($collection->status === 'verified') {
            return response()->json(['message' => 'Collection has already been verified.'], 400);
        }

        $validated = $request->validate([
            'amount_received' => 'required|numeric|min:0',
            'discrepancy_notes' => 'nullable|string|max:500',
            'attach_proof' => 'nullable|file|image|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Upload proof if provided
            $proofPath = null;
            if ($request->hasFile('attach_proof')) {
                $proofPath = $request->file('attach_proof')->store('cashier-verification', 'public');
            }

            // Check for discrepancy
            $discrepancyAmount = $validated['amount_received'] - $collection->amount_collected;
            $discrepancyNotes = $validated['discrepancy_notes'] ?? null;

            if ($discrepancyAmount != 0) {
                $discrepancyNotes = ($discrepancyNotes ? $discrepancyNotes . ' | ' : '') . 
                    "System discrepancy: PHP " . abs($discrepancyAmount) . 
                    ($discrepancyAmount > 0 ? " over-collected" : " under-collected");
            }

            // Update rider collection as verified
            $collection->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'amount_discrepancy' => $discrepancyAmount,
                'discrepancy_notes' => $discrepancyNotes,
                'reference_photo' => $proofPath ?? $collection->reference_photo,
            ]);

            // Create Payment record
            $payment = Payment::create([
                'customer_id' => $collection->salesOrder->customer_id,
                'sales_order_id' => $collection->sales_order_id,
                'rider_collection_id' => $collection->id,
                'amount' => $validated['amount_received'],
                'payment_method' => 'cod_cash',
                'payment_date' => now()->toDateString(),
                'status' => 'received',
                'reference_number' => $collection->salesOrder->so_number,
                'collected_by' => $collection->rider_id,
                'handed_over_by' => auth()->id(), // Cashier
                'verified_by' => auth()->id(),
                'notes' => 'COD Collection from ' . $collection->salesOrder->so_number,
            ]);

            // Update Sales Order payment status
            $collection->salesOrder->update([
                'collection_status' => 'reconciled',
                'payment_status' => 'paid',
            ]);

            // Post to Accounting (General Ledger) - optional, don't block if it fails
            try {
                $this->postCodPaymentToGL($collection->salesOrder, $payment);
            } catch (\Exception $glError) {
                \Log::warning('COD GL Post skipped: ' . $glError->getMessage());
                // Continue - payment is already recorded
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and recorded successfully!',
                'payment_id' => $payment->id,
                'collection' => $collection->load('salesOrder'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('COD Payment Verification Error: ' . $e->getMessage(), ['id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject collection due to discrepancy or issues
     */
    public function reject(Request $request, $id)
    {
        $collection = RiderCollection::findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasPermission('admin_finance.accounting')) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Mark as rejected (keep as handed_over but log rejection)
            $collection->update([
                'discrepancy_notes' => 'REJECTED: ' . $validated['reason'],
            ]);

            // Update SO collection status
            $collection->salesOrder->update([
                'collection_status' => 'handed_over', // Back to handed over for reinspection
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Collection rejected. Rider has been notified to review.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post COD payment to General Ledger
     */
    private function postCodPaymentToGL($salesOrder, $payment)
    {
        try {
            // Post to accounting system
            // This integrates with your existing accounting service
            // Example: Credit Cash/Bank account, Debit AR (reduce accounts receivable)
            
            $this->accounting->postPaymentEntry(
                salesOrder: $salesOrder,
                payment: $payment,
                paymentMethod: 'cod_cash',
                amount: $payment->amount
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('GL Post Error for COD Payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Daily cashier report
     */
    public function dailyReport()
    {
        $today = now()->startOfDay();

        $report = [
            'verified_count' => RiderCollection::where('status', 'verified')
                ->whereDate('verified_at', $today)
                ->count(),
            'verified_amount' => RiderCollection::where('status', 'verified')
                ->whereDate('verified_at', $today)
                ->sum('amount_collected'),
            'discrepancies' => RiderCollection::where('status', 'verified')
                ->whereDate('verified_at', $today)
                ->whereNotNull('amount_discrepancy')
                ->where('amount_discrepancy', '!=', 0)
                ->with('rider', 'salesOrder')
                ->get(),
            'collections_by_rider' => RiderCollection::where('status', 'verified')
                ->whereDate('verified_at', $today)
                ->selectRaw('rider_id, COUNT(*) as count, SUM(amount_collected) as total')
                ->with('rider')
                ->groupBy('rider_id')
                ->get(),
        ];

        return view('accounting.cashier.daily-report', compact('report'));
    }

    /**
     * Export collections for accounting
     */
    public function exportForAccounting(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');

        $collections = RiderCollection::where('status', 'verified')
            ->whereBetween('verified_at', [$from, $to])
            ->with('salesOrder', 'salesOrder.customer', 'rider', 'verifiedBy')
            ->orderBy('verified_at', 'desc')
            ->get();

        // Generate CSV or Excel
        $filename = 'cod-collections-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($collections) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date', 'SO Number', 'Customer', 'Rider', 'Amount Collected', 
                'Discrepancy', 'Verified By', 'Status'
            ]);

            // Data rows
            foreach ($collections as $collection) {
                fputcsv($file, [
                    $collection->verified_at->format('Y-m-d H:i'),
                    $collection->salesOrder->so_number,
                    $collection->salesOrder->customer->customer_name ?? $collection->salesOrder->customer->company_name ?? '',
                    $collection->rider->name ?? '',
                    $collection->amount_collected,
                    $collection->amount_discrepancy ?? 0,
                    $collection->verifiedBy->name ?? '',
                    $collection->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
