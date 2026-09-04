<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\StockTransfer;
use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sites,name',
            'code' => 'nullable|string|unique:sites,code',
            'location' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            $site = Site::create([
                'name' => $request->name,
                'code' => $request->code,
                'location' => $request->location,
                'description' => $request->description,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site created successfully',
                'site' => $site
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating site: ' . $e->getMessage()
            ], 422);
        }
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'reorder_point' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0'
        ]);

        try {
            $siteInventory = SiteInventory::firstOrCreate(
                [
                    'site_id' => $request->site_id,
                    'book_id' => $request->book_id
                ],
                [
                    'quantity' => 0
                ]
            );

            // Increment quantity
            $siteInventory->increment('quantity', $request->quantity);

            // Update reorder point and max stock if provided
            if ($request->reorder_point !== null) {
                $siteInventory->update(['reorder_point' => $request->reorder_point]);
            }
            if ($request->max_stock !== null) {
                $siteInventory->update(['max_stock' => $request->max_stock]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully to ' . $siteInventory->site->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: ' . $e->getMessage()
            ], 422);
        }
    }

    public function updateSite(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:sites,name,' . $id,
            'code' => 'nullable|string|unique:sites,code,' . $id,
            'location' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            $site = Site::findOrFail($id);
            $site->update([
                'name' => $request->name,
                'code' => $request->code,
                'location' => $request->location,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating site: ' . $e->getMessage()
            ], 422);
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_site_id' => 'required|exists:sites,id',
            'to_site_id' => 'required|exists:sites,id',
            'book_id' => 'nullable|exists:books,id',
            'book_index_id' => 'nullable|exists:book_indices,id',
            'book_bundle_id' => 'nullable|exists:book_bundles,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Ensure from and to are different
        if ($request->from_site_id === $request->to_site_id) {
            return response()->json([
                'success' => false,
                'message' => 'Source and destination sites must be different'
            ], 422);
        }

        $hasBook = $request->filled('book_id');
        $hasIndex = $request->filled('book_index_id');
        $hasBundle = $request->filled('book_bundle_id');

        if (($hasBook ? 1 : 0) + ($hasIndex ? 1 : 0) + ($hasBundle ? 1 : 0) !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide exactly one of: Book, Book Index, or Book Bundle'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check if source has enough stock
            $invQuery = SiteInventory::where('site_id', $request->from_site_id);
            if ($hasBook) {
                $invQuery->where('book_id', $request->book_id);
            } elseif ($hasIndex) {
                $invQuery->where('book_index_id', $request->book_index_id);
            } elseif ($hasBundle) {
                $invQuery->where('book_bundle_id', $request->book_bundle_id);
            }
            
            $sourceInventory = $invQuery->lockForUpdate()->first();

            if (!$sourceInventory || $sourceInventory->quantity < $request->quantity) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock at source site'
                ], 422);
            }

            $user = auth()->user();
            $isSuperAdmin = $user && $user->isSuperAdmin();

            $transfer = StockTransfer::create([
                'from_site_id'      => $request->from_site_id,
                'to_site_id'        => $request->to_site_id,
                'book_id'           => $request->book_id,
                'book_index_id'     => $request->book_index_id,
                'book_bundle_id'    => $request->book_bundle_id,
                'quantity'          => $request->quantity,
                'approval_division' => StockTransfer::approvalDivisionForUser($user),
                'notes'             => $request->notes,
                'created_by'        => $user->id,
                'status'            => 'pending'
            ]);

                $bookIdToLog = $transfer->book_id;
                if (!$bookIdToLog && $transfer->book_index_id) {
                    $idx = \App\Models\BookIndex::find($transfer->book_index_id);
                    $bookIdToLog = $idx ? $idx->book_id : null;
                }
                if ($bookIdToLog) {
                    $bk = \App\Models\Book::find($bookIdToLog);
                    $fromSiteObj = \App\Models\Site::find($request->from_site_id);
                    $toSiteObj   = \App\Models\Site::find($request->to_site_id);

                    \App\Models\InventoryTransaction::create([
                        'book_id'          => $bookIdToLog,
                        'type'             => 'out',
                        'quantity'         => $request->quantity,
                        'location'         => $fromSiteObj->name ?? 'Site',
                        'source'           => 'Stock Transfer',
                        'reference_number' => 'ST-' . sprintf('%04d', $transfer->id),
                        'unit_cost'        => $bk->cost ?? 0,
                        'total_cost'       => $request->quantity * ($bk->cost ?? 0),
                        'notes'            => 'Stock Transfer from ' . ($fromSiteObj->name ?? 'Site') . ' to ' . ($toSiteObj->name ?? 'Site'),
                        'status'           => 'completed',
                        'transaction_date' => now(),
                        'user_id'          => $user->id ?? 1,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transfer request submitted for ' . $transfer->approval_division . ' approval'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function transferBatch(Request $request)
    {
        $request->validate([
            'from_site_id'  => 'required|exists:sites,id',
            'to_site_id'    => 'required|exists:sites,id',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.type'          => 'required|in:book,index,bundle',
            'items.*.item_id'       => 'required|integer|min:1',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        if ($request->from_site_id == $request->to_site_id) {
            return response()->json([
                'success' => false,
                'message' => 'Source and destination sites cannot be the same'
            ], 422);
        }

        $batchId = \Illuminate\Support\Str::uuid()->toString();
        $user = auth()->user();
        $isSuperAdmin = $user && $user->isSuperAdmin();
        $approvalDivision = StockTransfer::approvalDivisionForUser($user);
        $errors = [];
        $created = [];

        DB::beginTransaction();
        try {
            foreach ($request->items as $index => $item) {
                $type     = $item['type'];
                $itemId   = $item['item_id'];
                $quantity = $item['quantity'];

                // Build the source inventory query
                $invQuery = SiteInventory::where('site_id', $request->from_site_id);
                if ($type === 'book') {
                    $invQuery->where('book_id', $itemId);
                } elseif ($type === 'index') {
                    $invQuery->where('book_index_id', $itemId);
                } elseif ($type === 'bundle') {
                    $invQuery->where('book_bundle_id', $itemId);
                }

                $sourceInventory = $invQuery->lockForUpdate()->first();

                // Auto-sync if source site is Main Warehouse and SiteInventory is missing or has less than master stock
                $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
                $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                if ((int)$request->from_site_id === (int)$mainSiteId) {
                    $availableMasterStock = 0;
                    if ($type === 'book') {
                        $bk = Book::find($itemId);
                        $availableMasterStock = $bk ? (int)$bk->stock : 0;
                    } elseif ($type === 'index') {
                        $idx = BookIndex::find($itemId);
                        $availableMasterStock = $idx ? (int)($idx->stock ?? $idx->quantity ?? 0) : 0;
                    } elseif ($type === 'bundle') {
                        $bd = BookBundle::find($itemId);
                        $availableMasterStock = $bd ? (int)($bd->stock ?? $bd->quantity ?? 0) : 0;
                    }

                    if ($availableMasterStock > 0 && (!$sourceInventory || $sourceInventory->quantity < $availableMasterStock)) {
                        $keyAttrs = ['site_id' => $mainSiteId];
                        if ($type === 'book') $keyAttrs['book_id'] = $itemId;
                        elseif ($type === 'index') $keyAttrs['book_index_id'] = $itemId;
                        elseif ($type === 'bundle') $keyAttrs['book_bundle_id'] = $itemId;

                        $sourceInventory = SiteInventory::updateOrCreate($keyAttrs, ['quantity' => $availableMasterStock]);
                    }
                }

                if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
                    $label = $type === 'book'
                        ? ('Book #' . $itemId)
                        : ($type === 'index' ? ('Index #' . $itemId) : ('Bundle #' . $itemId));
                    $errors[] = "Insufficient stock for {$label} (have " . ($sourceInventory->quantity ?? 0) . ", need {$quantity})";
                    continue;
                }

                $transfer = StockTransfer::create([
                    'from_site_id'      => $request->from_site_id,
                    'to_site_id'        => $request->to_site_id,
                    'book_id'           => $type === 'book'   ? $itemId : null,
                    'book_index_id'     => $type === 'index'  ? $itemId : null,
                    'book_bundle_id'    => $type === 'bundle' ? $itemId : null,
                    'quantity'          => $quantity,
                    'approval_division' => $approvalDivision,
                    'notes'             => $request->notes,
                    'batch_id'          => $batchId,
                    'created_by'        => $user->id,
                    'status'            => 'pending',
                ]);

                    $bookIdToLog = $transfer->book_id;
                    if (!$bookIdToLog && $transfer->book_index_id) {
                        $idx = \App\Models\BookIndex::find($transfer->book_index_id);
                        $bookIdToLog = $idx ? $idx->book_id : null;
                    }
                    if ($bookIdToLog) {
                        $bk = \App\Models\Book::find($bookIdToLog);
                        $fromSiteObj = \App\Models\Site::find($request->from_site_id);
                        $toSiteObj   = \App\Models\Site::find($request->to_site_id);

                        \App\Models\InventoryTransaction::create([
                            'book_id'          => $bookIdToLog,
                            'type'             => 'out',
                            'quantity'         => $quantity,
                            'location'         => $fromSiteObj->name ?? 'Site',
                            'source'           => 'Stock Transfer',
                            'reference_number' => 'ST-' . sprintf('%04d', $transfer->id),
                            'unit_cost'        => $bk->cost ?? 0,
                            'total_cost'       => $quantity * ($bk->cost ?? 0),
                            'notes'            => 'Stock Transfer from ' . ($fromSiteObj->name ?? 'Site') . ' to ' . ($toSiteObj->name ?? 'Site'),
                            'status'           => 'completed',
                            'transaction_date' => now(),
                            'user_id'          => $user->id ?? 1,
                        ]);
                    }

                $created[] = $transfer;
            }

            if (!empty($errors) && empty($created)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => implode('; ', $errors)
                ], 422);
            }

            DB::commit();

            $msg = $isSuperAdmin
                ? (count($created) . ' item(s) stock transfer completed immediately (Direct Transfer).')
                : (count($created) . ' item(s) transfer request submitted for ' . $approvalDivision . ' approval.');

            if (!empty($errors)) {
                $msg .= ' Skipped: ' . implode('; ', $errors);
            }

            return response()->json([
                'success'    => true,
                'message'    => $msg,
                'batch_id'   => $batchId,
                'created'    => count($created),
                'skipped'    => count($errors),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Batch transfer failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveTransfer(Request $request, $id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);

            $user = auth()->user();

            if (!$transfer->canBeApprovedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the assigned ' . ($transfer->approval_division ?? 'division') . ' manager/supervisor can approve this transfer'
                ], 403);
            }

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not pending'
                ], 422);
            }

            $remarks = $request->input('remarks');

            DB::transaction(function () use ($transfer, $remarks) {
                $query = $transfer->batch_id 
                    ? StockTransfer::where('batch_id', $transfer->batch_id) 
                    : StockTransfer::where('id', $transfer->id);

                $query->update([
                    'status' => 'accounting_review',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'remarks' => $remarks
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfer approved and forwarded to Accounting'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function approveAccountingTransfer(Request $request, $id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);
            $user = auth()->user();

            if (!$transfer->canBeReviewedByAccounting($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Accounting/Admin & Finance can review this transfer'
                ], 403);
            }

            if ($transfer->status !== 'accounting_review') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not waiting for Accounting review'
                ], 422);
            }

            $newRemarks = $request->input('remarks');

            DB::transaction(function () use ($transfer, $user, $newRemarks) {
                $query = $transfer->batch_id 
                    ? StockTransfer::where('batch_id', $transfer->batch_id) 
                    : StockTransfer::where('id', $transfer->id);

                foreach ($query->get() as $t) {
                    $finalRemarks = $t->remarks;
                    if ($newRemarks) {
                        if ($finalRemarks) {
                            $finalRemarks .= "\n[Accounting Review]: " . $newRemarks;
                        } else {
                            $finalRemarks = "[Accounting Review]: " . $newRemarks;
                        }
                    }
                    $t->update([
                        'status' => 'logistics_assignment',
                        'accounting_reviewed_by' => $user->id,
                        'accounting_reviewed_at' => now(),
                        'remarks' => $finalRemarks
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfer reviewed and forwarded to Logistics'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reviewing transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function assignLogisticsTransfer(Request $request, $id)
    {
        $request->validate([
            'logistics_assigned_to' => 'required|exists:users,id'
        ]);

        try {
            $transfer = StockTransfer::findOrFail($id);
            $user = auth()->user();

            if (!$transfer->canBeAssignedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only authorized staff/supervisors can assign this transfer'
                ], 403);
            }

            if (!in_array($transfer->status, ['pending', 'logistics_assignment', 'logistics_assigned'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not ready for Logistics assignment'
                ], 422);
            }

            $assignee = User::findOrFail($request->logistics_assigned_to);

            $updateData = [
                'status' => 'logistics_assigned',
                'logistics_assigned_to' => $assignee->id,
                'logistics_assigned_by' => $user->id,
                'logistics_assigned_at' => now()
            ];

            if ($transfer->status === 'pending') {
                $updateData['approved_by'] = $user->id;
                $updateData['approved_at'] = now();
            }

            $query = $transfer->batch_id 
                ? StockTransfer::where('batch_id', $transfer->batch_id) 
                : StockTransfer::where('id', $transfer->id);

            $query->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Transfer assigned to ' . $assignee->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function completeLogisticsTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);

            $user = auth()->user();

            if (!$transfer->canBeCompletedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the assigned Logistics staff can complete this transfer'
                ], 403);
            }

            DB::transaction(function () use ($transfer, $user) {
                $transfers = $transfer->batch_id 
                    ? StockTransfer::where('batch_id', $transfer->batch_id)->get() 
                    : collect([$transfer]);

                foreach ($transfers as $t) {
                    if (!$t->executeStockMovement()) {
                        throw new \Exception('Insufficient stock at source site for item ' . $t->item_name);
                    }
                    $t->update([
                        'status' => 'completed',
                        'completed_by' => $user->id,
                        'completed_at' => now()
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed and stock moved'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function rejectTransfer($id)
    {
        try {
            $transfer = StockTransfer::findOrFail($id);

            $user = auth()->user();

            if (!$transfer->canBeApprovedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the assigned ' . ($transfer->approval_division ?? 'division') . ' manager/supervisor can reject this transfer'
                ], 403);
            }

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not pending'
                ], 422);
            }

            $query = $transfer->batch_id 
                ? StockTransfer::where('batch_id', $transfer->batch_id) 
                : StockTransfer::where('id', $transfer->id);

            $query->update([
                'status' => 'rejected',
                'rejection_reason' => request('rejection_reason'),
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer rejected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getInventory($siteId)
    {
        try {
            $site = Site::findOrFail($siteId);
            
            // Get real-time site-specific inventory only (excluding deleted/orphan books)
            $inventory = SiteInventory::where('site_id', $siteId)
                ->with(['book', 'bookIndex.book', 'bookBundle'])
                ->where('quantity', '>', 0)
                ->get()
                ->filter(function($item) {
                    if ($item->book_index_id) {
                        return !empty($item->bookIndex) && !empty($item->bookIndex->book);
                    } elseif ($item->book_bundle_id) {
                        return !empty($item->bookBundle);
                    }
                    return !empty($item->book);
                })
                ->map(function($item) {
                    $type = 'book';
                    $itemId = $item->book_id;
                    $name = $item->book->name ?? 'Unknown';

                    if ($item->book_index_id) {
                        $type = 'index';
                        $itemId = $item->book_index_id;
                        $bookName = $item->bookIndex->book->name ?? 'Unknown';
                        $name = $bookName . ' ' . ($item->bookIndex->index_value ?? '');
                    } elseif ($item->book_bundle_id) {
                        $type = 'bundle';
                        $itemId = $item->book_bundle_id;
                        $name = $item->bookBundle->name ?? 'Unknown Bundle';
                    }

                    return [
                        'book_id' => $item->book_id,
                        'book_index_id' => $item->book_index_id,
                        'book_bundle_id' => $item->book_bundle_id,
                        'item_id' => $itemId,
                        'type' => $type,
                        'name' => $name,
                        'book' => [
                            'id' => $item->book_id,
                            'name' => $name
                        ],
                        'quantity' => $item->quantity
                    ];
                });

            return response()->json([
                'success' => true,
                'site_id' => $siteId,
                'site_name' => $site->name,
                'inventory' => $inventory->values()->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching inventory: ' . $e->getMessage()
            ], 422);
        }
    }

    public function deleteSite($id)
    {
        DB::beginTransaction();
        try {
            $site = Site::findOrFail($id);
            
            // Prevent deleting Main Warehouse
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;
            if ((int)$id === (int)$mainSiteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Main Warehouse cannot be deleted.'
                ], 422);
            }

            // 1. Find a primary target site to re-link or merge into
            $cleanName = trim(preg_replace('/^(site\s+|warehouse\s+|team\s+)+/i', '', $site->name));
            $targetSite = Site::where('id', '!=', $id)
                ->where(function($q) use ($site, $cleanName) {
                    $q->where('name', $site->name)
                      ->orWhere('name', $cleanName)
                      ->orWhere('name', 'Site ' . $cleanName)
                      ->orWhere('name', 'Team ' . $cleanName)
                      ->orWhere('name', 'Area Sales - ' . $cleanName)
                      ->orWhere('code', $site->code)
                      ->orWhereRaw('LOWER(name) = ?', [strtolower($site->name)])
                      ->orWhereRaw('LOWER(name) = ?', [strtolower($cleanName)]);
                })
                ->first();

            // If no matching duplicate site is found, fallback to Main Warehouse for transfer re-linking
            $fallbackSiteId = $targetSite ? $targetSite->id : $mainSiteId;

            // 2. Transfer / Merge any existing stock from SiteInventory
            $siteInventories = SiteInventory::where('site_id', $id)->get();
            $transferredStockCount = 0;
            foreach ($siteInventories as $inv) {
                if ($inv->quantity > 0) {
                    $targetInv = SiteInventory::firstOrNew([
                        'site_id'        => $fallbackSiteId,
                        'book_id'        => $inv->book_id,
                        'book_index_id'  => $inv->book_index_id,
                        'book_bundle_id' => $inv->book_bundle_id,
                    ]);
                    $targetInv->quantity = ($targetInv->quantity ?? 0) + $inv->quantity;
                    $targetInv->save();
                    $transferredStockCount += $inv->quantity;
                }
            }

            // Delete site inventory for this site
            SiteInventory::where('site_id', $id)->delete();

            // 3. Re-link foreign keys in stock_transfers to prevent constraint violation
            StockTransfer::where('to_site_id', $id)->update(['to_site_id' => $fallbackSiteId]);
            StockTransfer::where('from_site_id', $id)->update(['from_site_id' => $fallbackSiteId]);

            // 4. Re-link lost_inventories if any
            if (\Illuminate\Support\Facades\Schema::hasTable('lost_inventories')) {
                \App\Models\LostInventory::where('site_id', $id)->update(['site_id' => $fallbackSiteId]);
            }

            // 5. Delete the site
            $site->delete();

            DB::commit();

            $msg = 'Site deleted successfully.';
            if ($transferredStockCount > 0) {
                $destName = $targetSite ? $targetSite->name : ($mainWarehouse ? $mainWarehouse->name : 'Main Warehouse');
                $msg .= " Transferred {$transferredStockCount} pcs stock to {$destName} to preserve inventory accuracy.";
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting site: ' . $e->getMessage()
            ], 422);
        }
    }
}
