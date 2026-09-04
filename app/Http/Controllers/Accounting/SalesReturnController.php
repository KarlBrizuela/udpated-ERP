<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\InventoryTransaction;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Display a listing of sales returns.
     */
    public function index(Request $request)
    {
        $query = SalesReturn::with(['salesOrder.customer', 'creator'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('salesOrder', function ($soQuery) use ($search) {
                      $soQuery->where('so_number', 'like', "%{$search}%")
                              ->orWhereHas('customer', function ($cQuery) use ($search) {
                                  $cQuery->where('customer_name', 'like', "%{$search}%");
                              });
                  });
            });
        }

        $returns = $query->paginate(15);

        return view('admin-finance.accounting.sales-returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new sales return.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('sales_order_id')) {
                $salesOrder = SalesOrder::with(['items.book', 'customer'])->find($request->sales_order_id);
                if (!$salesOrder) {
                    return response()->json(['error' => 'Sales Order not found'], 404);
                }

                $itemsData = [];
                foreach ($salesOrder->items as $item) {
                    $previouslyReturned = SalesReturnItem::where('sales_order_item_id', $item->id)->sum('returned_qty');
                    $availableQty = $item->quantity - $previouslyReturned;

                    $itemsData[] = [
                        'sales_order_item_id' => $item->id,
                        'book_id' => $item->book_id,
                        'title' => $item->item_name ?? ($item->book->name ?? 'Unknown'),
                        'original_qty' => $item->quantity,
                        'previously_returned' => $previouslyReturned,
                        'available_qty' => $availableQty,
                        'price' => $item->price,
                    ];
                }

                return response()->json([
                    'so_number' => $salesOrder->so_number,
                    'customer' => $salesOrder->customer->customer_name ?? 'Walk-in',
                    'items' => $itemsData,
                ]);
            }
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        // Fetch completed or paid sales orders that can be returned
        $salesOrders = SalesOrder::whereNotIn('status', ['draft', 'cancelled'])
            ->with('customer')
            ->latest()
            ->get();

        return view('admin-finance.accounting.sales-returns.create', compact('salesOrders'));
    }

    /**
     * Store a newly created sales return in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'return_date' => 'required|date',
            'refund_method' => 'nullable|string',
            'remarks' => 'nullable|string',
            'items' => 'required|array',
            'items.*.sales_order_item_id' => 'required|exists:sales_order_items,id',
            'items.*.returned_qty' => 'required|integer|min:0',
        ]);

        try {
            $entry = DB::transaction(function () use ($request) {
                $salesOrder = SalesOrder::findOrFail($request->sales_order_id);
                $inventoryRestored = $request->has('inventory_restored') && $request->inventory_restored == 1;

                // Create return header
                $salesReturn = SalesReturn::create([
                    'return_no' => $this->generateReturnNumber(),
                    'sales_order_id' => $salesOrder->id,
                    'return_date' => $request->return_date,
                    'refund_amount' => 0.00, // Will update after calculating items
                    'refund_method' => $request->refund_method,
                    'inventory_restored' => $inventoryRestored,
                    'remarks' => $request->remarks,
                    'created_by' => auth()->id(),
                ]);

                $totalRefundAmount = 0;

                foreach ($request->items as $itemData) {
                    $returnedQty = (int) $itemData['returned_qty'];
                    if ($returnedQty <= 0) {
                        continue;
                    }

                    $salesOrderItem = SalesOrderItem::findOrFail($itemData['sales_order_item_id']);
                    
                    // Validate returned qty doesn't exceed original qty minus previously returned
                    $previouslyReturned = SalesReturnItem::where('sales_order_item_id', $salesOrderItem->id)->sum('returned_qty');
                    $maxAvailable = $salesOrderItem->quantity - $previouslyReturned;

                    if ($returnedQty > $maxAvailable) {
                        throw new \Exception("Returned quantity for item '{$salesOrderItem->item_name}' cannot exceed available quantity of {$maxAvailable}.");
                    }

                    $subtotal = $returnedQty * $salesOrderItem->price;
                    $totalRefundAmount += $subtotal;

                    // Create return item detail
                    SalesReturnItem::create([
                        'sales_return_id' => $salesReturn->id,
                        'sales_order_item_id' => $salesOrderItem->id,
                        'book_id' => $salesOrderItem->book_id,
                        'returned_qty' => $returnedQty,
                        'price' => $salesOrderItem->price,
                        'subtotal' => $subtotal,
                    ]);

                    // Inventory stock restoration
                    if ($inventoryRestored && $salesOrderItem->book_id) {
                        $book = $salesOrderItem->book;
                        if ($book) {
                            $book->increment('stock', $returnedQty);

                            InventoryTransaction::create([
                                'book_id' => $book->id,
                                'type' => 'in',
                                'quantity' => $returnedQty,
                                'location' => 'Main Warehouse',
                                'source' => 'Sales Return',
                                'reference_number' => $salesReturn->return_no,
                                'notes' => "Physical restock via return " . $salesReturn->return_no,
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }
                }

                if ($totalRefundAmount <= 0) {
                    throw new \Exception("Cannot process a return with zero total value.");
                }

                // Update total refund amount on header
                $salesReturn->update(['refund_amount' => $totalRefundAmount]);

                // Trigger Double-Entry accounting posting
                $this->accounting->postSalesReturnEntry($salesReturn);

                return $salesReturn;
            });

            return redirect()->route('admin-finance.accounting.sales-returns.index')
                ->with('success', "Sales Return {$entry->return_no} has been recorded successfully and ledger entries are posted.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified sales return details.
     */
    public function show($id)
    {
        $return = SalesReturn::with([
            'salesOrder.customer',
            'creator',
            'items.book',
            'journalEntry.items.account'
        ])->findOrFail($id);

        return view('admin-finance.accounting.sales-returns.show', compact('return'));
    }

    /**
     * Delete/Void the specified sales return and reverse all its inventory and accounting actions.
     */
    public function destroy($id)
    {
        try {
            \DB::transaction(function () use ($id) {
                $salesReturn = SalesReturn::with('items.book')->findOrFail($id);

                // 1. Revert Inventory Stock & Transactions if stock was restored
                if ($salesReturn->inventory_restored) {
                    foreach ($salesReturn->items as $item) {
                        if ($item->book_id) {
                            $book = $item->book;
                            if ($book) {
                                // Deduct the returned qty from stock
                                $book->decrement('stock', $item->returned_qty);

                                // Log a voiding transaction
                                \App\Models\InventoryTransaction::create([
                                    'book_id' => $book->id,
                                    'type' => 'out',
                                    'quantity' => $item->returned_qty,
                                    'location' => 'Main Warehouse',
                                    'source' => 'Void Sales Return',
                                    'reference_number' => $salesReturn->return_no,
                                    'notes' => "Voided return " . $salesReturn->return_no,
                                    'user_id' => auth()->id(),
                                ]);
                            }
                        }
                    }
                }

                // 2. Revert Journal Entries
                if ($salesReturn->journal_entry_id) {
                    $entry = \App\Models\JournalEntry::find($salesReturn->journal_entry_id);
                    if ($entry) {
                        \App\Models\JournalEntryItem::where('journal_entry_id', $entry->id)->delete();
                        $entry->delete();
                    }
                }

                // 3. Delete SalesReturnItems & SalesReturn header
                $salesReturn->items()->delete();
                $salesReturn->delete();
            });

            return redirect()->route('admin-finance.accounting.sales-returns.index')
                ->with('success', "Sales Return has been successfully voided/deleted, inventory levels restored, and ledger entries reversed.");

        } catch (\Exception $e) {
            return back()->with('error', "Failed to delete Sales Return: " . $e->getMessage());
        }
    }

    /**
     * Generate unique return number.
     */
    private function generateReturnNumber()
    {
        $year = now()->year;
        $prefix = "SR-{$year}";
        $lastReturn = SalesReturn::where('return_no', 'like', "{$prefix}-%")
            ->orderBy('return_no', 'desc')
            ->first();

        if ($lastReturn) {
            $lastSeq = (int) substr($lastReturn->return_no, -4);
            $newSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }
        return "{$prefix}-{$newSeq}";
    }
}
