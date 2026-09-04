<?php

namespace App\Http\Controllers;

use App\Models\RiderCollection;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderCollectionController extends Controller
{
    /**
     * Show pending collections for the rider
     */
    public function index()
    {
        $riderId = auth()->id();
        
        $collections = RiderCollection::where('rider_id', $riderId)
            ->whereIn('status', ['pending', 'collected'])
            ->with('salesOrder', 'salesOrder.customer', 'salesOrder.items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'pending' => RiderCollection::where('rider_id', $riderId)->where('status', 'pending')->count(),
            'collected' => RiderCollection::where('rider_id', $riderId)->where('status', 'collected')->count(),
            'handed_over' => RiderCollection::where('rider_id', $riderId)->where('status', 'handed_over')->count(),
            'total_to_collect' => RiderCollection::where('rider_id', $riderId)
                ->whereIn('status', ['pending', 'collected'])
                ->sum('amount_to_collect'),
            'total_collected' => RiderCollection::where('rider_id', $riderId)
                ->where('status', 'collected')
                ->sum('amount_collected'),
        ];

        return view('rider.collections.index', [
            'collections' => $collections,
            'stats' => $stats,
            'title' => 'COD Collections',
            'role' => auth()->user()->position,
            'sidebar' => 'production'
        ]);
    }

    /**
     * Show collection details
     */
    public function show($id)
    {
        $collection = RiderCollection::with('salesOrder', 'salesOrder.customer', 'salesOrder.items', 'rider')
            ->findOrFail($id);

        // Verify the collection belongs to the current rider
        if ($collection->rider_id != auth()->id() && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('rider.collections.show', [
            'collection' => $collection,
            'title' => 'Collection Details',
            'role' => auth()->user()->position,
            'sidebar' => 'production'
        ]);
    }

    /**
     * Record collection - Mark as collected with amount and proof
     */
    public function recordCollection(Request $request, $id)
    {
        $collection = RiderCollection::findOrFail($id);

        // Verify the collection belongs to the current rider
        if ($collection->rider_id != auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        if ($collection->status !== 'pending') {
            return response()->json(['message' => 'Collection has already been recorded.'], 400);
        }

        $validated = $request->validate([
            'amount_collected' => 'required|numeric|min:0',
            'collection_notes' => 'nullable|string|max:500',
            'customer_signature_photo' => 'nullable|file|image|max:5120', // 5MB
            'reference_photo' => 'nullable|file|image|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Handle photo uploads
            $signaturePhotoPath = null;
            if ($request->hasFile('customer_signature_photo')) {
                $signaturePhotoPath = $request->file('customer_signature_photo')->store('rider-collections/signatures', 'public');
            }

            $referencePhotoPath = null;
            if ($request->hasFile('reference_photo')) {
                $referencePhotoPath = $request->file('reference_photo')->store('rider-collections/references', 'public');
            }

            // Mark collection as collected
            $collection->update([
                'amount_collected' => $validated['amount_collected'],
                'status' => 'collected',
                'collected_at' => now(),
                'collection_notes' => $validated['collection_notes'],
                'customer_signature_photo' => $signaturePhotoPath,
                'reference_photo' => $referencePhotoPath,
            ]);

            // Update Sales Order collection status
            $collection->salesOrder->update([
                'collection_status' => 'collected',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Collection recorded successfully!',
                'collection' => $collection->load('salesOrder'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error recording collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark collection as handed over to cashier
     */
    public function handOver(Request $request, $id)
    {
        $collection = RiderCollection::findOrFail($id);

        // Verify the collection belongs to the current rider
        if ($collection->rider_id != auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        if ($collection->status !== 'collected') {
            return response()->json(['message' => 'Collection must be in collected status to hand over.'], 400);
        }

        try {
            DB::beginTransaction();

            $collection->markAsHandedOver();

            // Update Sales Order
            $collection->salesOrder->update([
                'collection_status' => 'handed_over',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Collection handed over successfully! Waiting for cashier verification.',
                'collection' => $collection->load('salesOrder'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error handing over collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get collections awaiting handover (for supervisors)
     */
    public function awaitingHandover()
    {
        $collections = RiderCollection::where('status', 'collected')
            ->with('salesOrder', 'salesOrder.customer', 'rider')
            ->orderBy('collected_at', 'asc')
            ->paginate(20);

        return view('rider.collections.awaiting-handover', [
            'collections' => $collections,
            'title' => 'Awaiting Handover',
            'role' => auth()->user()->position,
            'sidebar' => 'production'
        ]);
    }

    /**
     * Daily summary report for rider
     */
    public function dailySummary()
    {
        $riderId = auth()->id();
        $today = now()->startOfDay();

        $summary = [
            'total_collections' => RiderCollection::where('rider_id', $riderId)
                ->whereDate('collected_at', $today)
                ->count(),
            'total_amount_collected' => RiderCollection::where('rider_id', $riderId)
                ->whereDate('collected_at', $today)
                ->sum('amount_collected'),
            'collections' => RiderCollection::where('rider_id', $riderId)
                ->whereDate('collected_at', $today)
                ->with('salesOrder', 'salesOrder.customer')
                ->orderBy('collected_at', 'desc')
                ->get(),
        ];

        return view('rider.collections.daily-summary', [
            'summary' => $summary,
            'title' => 'Daily Summary',
            'role' => auth()->user()->position,
            'sidebar' => 'production'
        ]);
    }

    /**
     * Record customer item selection for evaluation orders
     * Receives: { book_id => { sent_qty, purchased_qty, returned_qty } }
     */
    public function recordEvaluationSelection(Request $request, $id)
    {
        // Ensure JSON response for API requests
        $request->headers->set('Accept', 'application/json');
        
        try {
            $collection = RiderCollection::with('salesOrder', 'salesOrder.items')->findOrFail($id);

            // Verify the collection belongs to the current rider
            if ($collection->rider_id != auth()->id() && !auth()->user()->isSuperAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }

            // Only evaluation orders can record item selection
            if (!$collection->isEvaluationCollection()) {
                return response()->json(['success' => false, 'message' => 'This is not an evaluation collection.'], 400);
            }

            $validated = $request->validate([
                'items_selection' => 'required|array',
                'items_selection.*.sent_qty' => 'required|integer|min:0',
                'items_selection.*.purchased_qty' => 'required|integer|min:0',
                'items_selection.*.returned_qty' => 'required|integer|min:0',
                'evaluation_notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Format items selection for storage
            $itemsSelection = [];
            foreach ($validated['items_selection'] as $bookId => $selection) {
                $itemsSelection[$bookId] = [
                    'book_id' => intval($bookId),
                    'sent_qty' => $selection['sent_qty'],
                    'purchased_qty' => $selection['purchased_qty'],
                    'returned_qty' => $selection['returned_qty'],
                    'status' => $selection['purchased_qty'] === 0 ? 'fully_returned' : 
                               ($selection['purchased_qty'] === $selection['sent_qty'] ? 'full' : 'partial'),
                ];
            }

            // Update collection with item selection
            $collection->recordItemSelection($itemsSelection);
            $collection->collection_notes = $validated['evaluation_notes'] ?? $collection->collection_notes;
            $collection->save();

            // Update SalesOrderItems with selected quantities
            foreach ($collection->salesOrder->items as $soItem) {
                if (isset($itemsSelection[$soItem->book_id])) {
                    $selection = $itemsSelection[$soItem->book_id];
                    $soItem->update([
                        'sent_qty' => $selection['sent_qty'],
                        'selected_qty' => $selection['purchased_qty'],
                        'returned_qty' => $selection['returned_qty'],
                        'customer_selected_qty' => $selection['purchased_qty'],
                    ]);

                    // Create inventory transactions for returned items
                    if ($selection['returned_qty'] > 0) {
                        \App\Models\InventoryTransaction::create([
                            'book_id' => $soItem->book_id,
                            'sales_order_item_id' => $soItem->id,
                            'rider_collection_id' => $collection->id,
                            'type' => 'in_return_evaluation',
                            'quantity' => $selection['returned_qty'],
                            'location' => 'Main Warehouse',
                            'source' => 'Evaluation Return',
                            'reference_number' => $collection->salesOrder->so_number,
                            'notes' => "Customer declined - returned from evaluation",
                            'status' => 'completed',
                            'transaction_date' => now(),
                            'user_id' => auth()->id(),
                        ]);

                        // Increment book stock for returned items
                        $soItem->book->increment('stock', $selection['returned_qty']);
                    }

                    // Create inventory transactions for sold/selected items
                    if ($selection['purchased_qty'] > 0) {
                        \App\Models\InventoryTransaction::create([
                            'book_id' => $soItem->book_id,
                            'sales_order_item_id' => $soItem->id,
                            'rider_collection_id' => $collection->id,
                            'type' => 'out_sold_evaluation',
                            'quantity' => $selection['purchased_qty'],
                            'location' => 'Main Warehouse',
                            'source' => 'Evaluation Sale',
                            'reference_number' => $collection->salesOrder->so_number,
                            'notes' => "Customer selected and purchased",
                            'status' => 'completed',
                            'transaction_date' => now(),
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            }

            // Update SO total amount based on selected items only
            $newTotal = $collection->salesOrder->items->sum(function ($item) {
                return ($item->customer_selected_qty ?? 0) * $item->price;
            });

            $collection->salesOrder->update([
                'total_amount' => $newTotal,
                'amount_to_collect' => $newTotal,
            ]);

            // Update collection amount to collect
            $collection->update([
                'amount_to_collect' => $newTotal,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer item selection recorded successfully!',
                'collection' => $collection->load('salesOrder', 'salesOrder.items'),
                'summary' => $collection->getEvaluationSummary(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error recording item selection: ' . $e->getMessage()
            ], 500);
        }
    }
}
