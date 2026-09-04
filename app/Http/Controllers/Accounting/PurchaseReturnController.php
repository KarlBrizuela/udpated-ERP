<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SupplierInvoice;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    /**
     * Display a listing of purchase returns.
     */
    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['supplierInvoice.purchaseOrder', 'supplier', 'creator'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                      $supplierQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplierInvoice', function ($invoiceQuery) use ($search) {
                      $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }

        $returns = $query->paginate(15);

        return view('admin-finance.accounting.purchase-returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new purchase return.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('supplier_invoice_id')) {
                $invoice = SupplierInvoice::with([
                    'supplier',
                    'receivingReport.items.product.book',
                    'receivingReport.items.product.item',
                    'purchaseOrder.items.product.book',
                    'purchaseOrder.items.product.item'
                ])->find($request->supplier_invoice_id);

                if (!$invoice) {
                    return response()->json(['error' => 'Supplier Invoice not found'], 404);
                }

                $itemsData = [];

                // Resolve which items source to use: receiving report is preferred, otherwise purchase order
                if ($invoice->receivingReport) {
                    foreach ($invoice->receivingReport->items as $item) {
                        $productId = $item->product_id;
                        $previouslyReturned = PurchaseReturnItem::whereHas('purchaseReturn', function($q) use ($invoice) {
                            $q->where('supplier_invoice_id', $invoice->id);
                        })->where('product_id', $productId)->sum('returned_qty');

                        $availableQty = $item->quantity_received - $previouslyReturned;

                        $productName = $item->product->name ?? ($item->product->book->name ?? ($item->product->item->name ?? 'Unknown'));

                        $itemsData[] = [
                            'product_id' => $productId,
                            'title' => $productName,
                            'original_qty' => $item->quantity_received,
                            'previously_returned' => $previouslyReturned,
                            'available_qty' => $availableQty,
                            'unit_cost' => $item->unit_cost,
                        ];
                    }
                } elseif ($invoice->purchaseOrder) {
                    foreach ($invoice->purchaseOrder->items as $item) {
                        $productId = $item->product_id;
                        $previouslyReturned = PurchaseReturnItem::whereHas('purchaseReturn', function($q) use ($invoice) {
                            $q->where('supplier_invoice_id', $invoice->id);
                        })->where('product_id', $productId)->sum('returned_qty');

                        $availableQty = $item->quantity - $previouslyReturned;

                        $productName = $item->product->name ?? ($item->product->book->name ?? ($item->product->item->name ?? 'Unknown'));

                        $itemsData[] = [
                            'product_id' => $productId,
                            'title' => $productName,
                            'original_qty' => $item->quantity,
                            'previously_returned' => $previouslyReturned,
                            'available_qty' => $availableQty,
                            'unit_cost' => $item->price ?? 0, // In PO, items price is unit cost
                        ];
                    }
                }

                return response()->json([
                    'invoice_number' => $invoice->invoice_number,
                    'supplier' => $invoice->supplier->name ?? 'N/A',
                    'items' => $itemsData,
                ]);
            }
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        // Fetch active supplier invoices that can be returned
        $invoices = SupplierInvoice::with('supplier')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        return view('admin-finance.accounting.purchase-returns.create', compact('invoices'));
    }

    /**
     * Store a newly created purchase return in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_invoice_id' => 'required|exists:supplier_invoices,id',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.returned_qty' => 'required|integer|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            $entry = DB::transaction(function () use ($request) {
                $invoice = SupplierInvoice::findOrFail($request->supplier_invoice_id);

                // Create Purchase Return header
                $purchaseReturn = PurchaseReturn::create([
                    'return_no' => $this->generateReturnNumber(),
                    'supplier_id' => $invoice->supplier_id,
                    'supplier_invoice_id' => $invoice->id,
                    'supplier_invoice_no' => $invoice->invoice_number,
                    'return_date' => $request->return_date,
                    'refund_amount' => 0.00, // Will update below
                    'inventory_deducted' => true,
                    'refund_status' => $invoice->balance > 0 ? 'applied_to_payable' : 'receivable',
                    'notes' => $request->notes,
                    'prepared_by' => auth()->id() ?? 1,
                ]);

                $totalRefundAmount = 0.00;

                foreach ($request->items as $itemData) {
                    $returnedQty = (int) $itemData['returned_qty'];
                    if ($returnedQty <= 0) {
                        continue;
                    }

                    $unitCost = (float) $itemData['unit_cost'];
                    $subtotal = $returnedQty * $unitCost;
                    $totalRefundAmount += $subtotal;

                    // Create return item
                    PurchaseReturnItem::create([
                        'purchase_return_id' => $purchaseReturn->id,
                        'product_id' => $itemData['product_id'],
                        'returned_qty' => $returnedQty,
                        'unit_cost' => $unitCost,
                        'subtotal' => $subtotal,
                    ]);

                    // Deduct stock from the inventory registry!
                    $product = Product::with(['book', 'item'])->findOrFail($itemData['product_id']);
                    
                    // Deduct from book or item stock depending on product type
                    if ($product->book_id) {
                        $product->book->decrement('stock', $returnedQty);
                    } elseif ($product->item_id) {
                        $product->item->decrement('stock', $returnedQty);
                    }

                    // Log inventory transaction (type = out)
                    InventoryTransaction::create([
                        'book_id' => $product->book_id,
                        'item_id' => $product->item_id,
                        'type' => 'out',
                        'quantity' => $returnedQty,
                        'location' => 'Main Warehouse',
                        'source' => 'Purchase Return',
                        'reference_number' => $purchaseReturn->return_no,
                        'notes' => "Returned to supplier. Invoice: " . $invoice->invoice_number,
                        'user_id' => auth()->id() ?? 1,
                    ]);
                }

                if ($totalRefundAmount <= 0) {
                    throw new \Exception("Cannot process a return with zero total value.");
                }

                // Update total refund amount on header
                $purchaseReturn->update(['refund_amount' => $totalRefundAmount]);

                // Update the Supplier Invoice total_amount / payable balance
                $outstandingBalance = $invoice->balance; // total_amount - amount_paid

                if ($outstandingBalance >= $totalRefundAmount) {
                    // Revert full amount from outstanding payable total
                    $invoice->decrement('total_amount', $totalRefundAmount);
                } else {
                    // Revert outstanding balance portion
                    $invoice->decrement('total_amount', $outstandingBalance);
                }

                // Trigger Double-Entry accounting posting
                $this->accounting->postPurchaseReturnEntry($purchaseReturn);

                return $purchaseReturn;
            });

            return redirect()->route('admin-finance.accounting.purchase-returns.index')
                ->with('success', "Purchase Return {$entry->return_no} has been recorded successfully and ledger entries are posted.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified purchase return details.
     */
    public function show($id)
    {
        $return = PurchaseReturn::with([
            'supplierInvoice',
            'supplier',
            'creator',
            'items.product.book',
            'items.product.item',
            'journalEntry.items.account'
        ])->findOrFail($id);

        return view('admin-finance.accounting.purchase-returns.show', compact('return'));
    }

    /**
     * Delete/Void the specified purchase return and reverse all its inventory and accounting actions.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchaseReturn = PurchaseReturn::with(['items.product.book', 'items.product.item', 'supplierInvoice'])->findOrFail($id);

                // 1. Revert Inventory Stock & Transactions if stock was deducted
                if ($purchaseReturn->inventory_deducted) {
                    foreach ($purchaseReturn->items as $item) {
                        $product = $item->product;
                        if ($product) {
                            // Re-add/increment returned qty back to stock
                            if ($product->book_id) {
                                $product->book->increment('stock', $item->returned_qty);
                            } elseif ($product->item_id) {
                                $product->item->increment('stock', $item->returned_qty);
                            }

                            // Log a voiding transaction
                            InventoryTransaction::create([
                                'book_id' => $product->book_id,
                                'item_id' => $product->item_id,
                                'type' => 'in',
                                'quantity' => $item->returned_qty,
                                'location' => 'Main Warehouse',
                                'source' => 'Void Purchase Return',
                                'reference_number' => $purchaseReturn->return_no,
                                'notes' => "Voided return " . $purchaseReturn->return_no,
                                'user_id' => auth()->id() ?? 1,
                            ]);
                        }
                    }
                }

                // 2. Revert Supplier Invoice adjustments
                if ($purchaseReturn->supplierInvoice) {
                    // Re-add the refunded amount back to the invoice total amount
                    $purchaseReturn->supplierInvoice->increment('total_amount', $purchaseReturn->refund_amount);
                }

                // 3. Revert Journal Entries
                if ($purchaseReturn->journal_entry_id) {
                    $entry = \App\Models\JournalEntry::find($purchaseReturn->journal_entry_id);
                    if ($entry) {
                        \App\Models\JournalEntryItem::where('journal_entry_id', $entry->id)->delete();
                        $entry->delete();
                    }
                }

                // 4. Delete PurchaseReturnItems & PurchaseReturn header
                $purchaseReturn->items()->delete();
                $purchaseReturn->delete();
            });

            return redirect()->route('admin-finance.accounting.purchase-returns.index')
                ->with('success', "Purchase Return has been successfully voided/deleted, inventory levels restored, and ledger entries reversed.");

        } catch (\Exception $e) {
            return back()->with('error', "Failed to delete Purchase Return: " . $e->getMessage());
        }
    }

    /**
     * Generate unique return number.
     */
    private function generateReturnNumber()
    {
        $year = now()->year;
        $prefix = "PR-{$year}";
        $lastReturn = PurchaseReturn::where('return_no', 'like', "{$prefix}-%")
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
