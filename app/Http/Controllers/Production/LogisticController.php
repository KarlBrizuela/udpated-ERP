<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AccountingService;

class LogisticController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }
    private function excludeTeamSalesOrders($query)
    {
        return $query->where(function ($q) {
            $q->whereDoesntHave('areaSalesStaff', function ($u) {
                $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
            })
            ->whereDoesntHave('preparedBy', function ($u) {
                $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
            });
        });
    }

    private function excludeTeamPickLists($query)
    {
        return $query->whereDoesntHave('salesOrder', function ($soQuery) {
            $soQuery->where(function ($q) {
                $q->whereHas('areaSalesStaff', function ($u) {
                    $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
                })
                ->orWhereHas('preparedBy', function ($u) {
                    $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
                });
            });
        });
    }

    public function pickListManagement(Request $request)
    {
        // Get existing pick lists (not completed) - EXCLUDING Team A, B, C
        $pickListsQuery = \App\Models\PickList::with(['salesOrder', 'salesOrder.customer', 'pickListItems.salesOrderItem.book', 'pickListItems.salesOrderItem.bundle', 'pickListItems.salesOrderItem.bookIndex.book', 'preparedByUser'])
            ->where('status', '!=', 'completed');
        $this->excludeTeamPickLists($pickListsQuery);
        $pickLists = $pickListsQuery->latest()->get();

        // Get completed pick lists for recreation option - EXCLUDING Team A, B, C
        $completedPickListsQuery = \App\Models\PickList::with(['salesOrder', 'salesOrder.customer', 'pickListItems.salesOrderItem.book', 'pickListItems.salesOrderItem.bundle', 'pickListItems.salesOrderItem.bookIndex.book', 'preparedByUser'])
            ->where('status', 'completed');
        $this->excludeTeamPickLists($completedPickListsQuery);
        $completedPickLists = $completedPickListsQuery->latest()->paginate(10);

        // Get pending Sales Orders ready for picking - EXCLUDING Team A, B, C
        $pendingOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.book')
            ->where('status', 'picking')
            ->whereDoesntHave('pickLists', function($query) {
                $query->where('status', '!=', 'completed');
            });
        $this->excludeTeamSalesOrders($pendingOrdersQuery);
        $pendingOrders = $pendingOrdersQuery->latest()->get();

        // If pickListId is provided, preload that pick list
        $preloadPickListId = $request->input('pickListId');
        
        // If so_id is provided (from ecom direct invoice workflow), preload that order
        $preloadOrderId = $request->input('so_id');
        $preloadOrder = null;
        if ($preloadOrderId) {
            $preloadOrder = \App\Models\SalesOrder::with('customer', 'items.book')->find($preloadOrderId);
        }

        $teamStockPickLists = \App\Models\TeamStockTransfer::with('transferredByUser', 'items')
            ->whereIn('status', ['pending_picklist', 'picking'])
            ->latest()
            ->get();

        return view('production.logistic.pick-list-management', [
            'pickLists' => $pickLists,
            'completedPickLists' => $completedPickLists,
            'pendingOrders' => $pendingOrders,
            'preloadPickListId' => $preloadPickListId,
            'preloadOrder' => $preloadOrder,
            'teamStockPickLists' => $teamStockPickLists,
        ]);
    }

    public function pickListList()
    {
        // Auto-heal: Ensure all sales orders in 'picking' status have a PickList (excluding Team A, B, C)
        $pickingOrdersNoListQuery = \App\Models\SalesOrder::with('items')
            ->where('status', 'picking')
            ->whereDoesntHave('pickLists');
        $this->excludeTeamSalesOrders($pickingOrdersNoListQuery);
        $pickingOrdersNoList = $pickingOrdersNoListQuery->get();

        foreach ($pickingOrdersNoList as $pOrder) {
            if ($pOrder->items && $pOrder->items->count() > 0) {
                $pl = \App\Models\PickList::create([
                    'sales_order_id'   => $pOrder->id,
                    'pick_list_number' => 'PL-' . $pOrder->so_number . '-' . date('YmdHis'),
                    'status'           => 'in_progress',
                    'prepared_by'      => $pOrder->prepared_by ?: (auth()->id() ?: 1),
                ]);
                foreach ($pOrder->items as $pItem) {
                    \App\Models\PickListItem::create([
                        'pick_list_id'        => $pl->id,
                        'sales_order_item_id' => $pItem->id,
                        'requested_qty'       => $pItem->quantity,
                        'picked_qty'          => 0,
                        'status'              => 'pending',
                    ]);
                }
            }
        }

        // Get active pick lists (not completed) - EXCLUDING e-commerce direct, complimentary, and Team A, B, C
        $pickListsQuery = \App\Models\PickList::with('salesOrder', 'salesOrder.customer', 'preparedByUser', 'pickListItems')
            ->whereHas('salesOrder', function($query) {
                $query->whereNotIn('type', ['ecom_direct', 'complimentary']);
            })
            ->where('status', '!=', 'completed');
        $this->excludeTeamPickLists($pickListsQuery);
        $pickLists = $pickListsQuery->latest()->get();

        // Get e-commerce pick lists (type='ecom_direct' or with ecom_platform) - EXCLUDING Team A, B, C
        $ecomPickListsQuery = \App\Models\PickList::with('salesOrder', 'salesOrder.customer', 'preparedByUser', 'pickListItems')
            ->whereHas('salesOrder', function($query) {
                $query->where('type', 'ecom_direct')
                      ->orWhere(function($sub) {
                          $sub->whereNotNull('ecom_platform')->where('ecom_platform', '!=', '');
                      });
            })
            ->where('status', '!=', 'completed');
        $this->excludeTeamPickLists($ecomPickListsQuery);
        $ecomPickLists = $ecomPickListsQuery->latest()->get();

        // Get complimentary pick lists (type='complimentary') - EXCLUDING Team A, B, C
        $complimentaryPickListsQuery = \App\Models\PickList::with('salesOrder', 'salesOrder.customer', 'preparedByUser', 'pickListItems')
            ->whereHas('salesOrder', function($query) {
                $query->where('type', 'complimentary');
            })
            ->where('status', '!=', 'completed');
        $this->excludeTeamPickLists($complimentaryPickListsQuery);
        $complimentaryPickLists = $complimentaryPickListsQuery->latest()->get();

        // Organize e-com pick lists by platform
        $ecomByPlatform = [
            'lazada' => $ecomPickLists->filter(function($item) {
                return strtolower($item->salesOrder->ecom_platform ?? '') === 'lazada';
            })->values(),
            'shopee' => $ecomPickLists->filter(function($item) {
                return strtolower($item->salesOrder->ecom_platform ?? '') === 'shopee';
            })->values(),
            'tiktok' => $ecomPickLists->filter(function($item) {
                return strtolower($item->salesOrder->ecom_platform ?? '') === 'tiktok';
            })->values(),
            'cob' => $ecomPickLists->filter(function($item) {
                $p = strtolower($item->salesOrder->ecom_platform ?? '');
                return !in_array($p, ['lazada', 'shopee', 'tiktok']);
            })->values(),
        ];

        // Get pending Sales Orders ready for picking - EXCLUDING Team A, B, C
        $pendingOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.book')
            ->where('status', 'picking')
            ->whereDoesntHave('pickLists', function($query) {
                $query->where('status', '!=', 'completed');
            });
        $this->excludeTeamSalesOrders($pendingOrdersQuery);
        $pendingOrders = $pendingOrdersQuery->latest()->get();

        \Illuminate\Support\Facades\Log::debug('PickListList pick lists count: ' . $pickLists->count());
        if ($pickLists->count() > 0) {
            \Illuminate\Support\Facades\Log::debug('First pick list: ' . $pickLists->first()->pick_list_number);
        }

        $teamStockPickLists = \App\Models\TeamStockTransfer::with(['transferredByUser', 'items.book', 'items.bookIndex.book', 'items.bookBundle'])
            ->whereIn('status', ['pending_picklist', 'picking'])
            ->latest()
            ->get();

        return view('production.logistic.pick-list-list', [
            'title' => 'Pick Lists',
            'role' => 'Logistics Staff',
            'sidebar' => 'production',
            'pickLists' => $pickLists,
            'ecomPickLists' => $ecomPickLists,
            'ecomByPlatform' => $ecomByPlatform,
            'complimentaryPickLists' => $complimentaryPickLists,
            'pendingOrders' => $pendingOrders,
            'teamStockPickLists' => $teamStockPickLists,
        ]);
    }

    public function showPickList($id)
    {
        try {
            \Log::info('Loading pick list with ID: ' . $id);
            
            $pickList = \App\Models\PickList::with([
                'salesOrder.items.book', 
                'salesOrder.items.bundle', 
                'salesOrder.items.bookIndex.book', 
                'salesOrder.customer', 
                'pickListItems.salesOrderItem.book', 
                'pickListItems.salesOrderItem.bundle', 
                'pickListItems.salesOrderItem.bookIndex.book', 
                'preparedByUser'
            ])->findOrFail($id);

            // Auto-heal: If pickListItems is empty but SalesOrder has items, auto-generate PickListItems
            if ($pickList->pickListItems->isEmpty() && $pickList->salesOrder && $pickList->salesOrder->items->count() > 0) {
                \DB::transaction(function() use ($pickList) {
                    foreach ($pickList->salesOrder->items as $soItem) {
                        \App\Models\PickListItem::create([
                            'pick_list_id' => $pickList->id,
                            'sales_order_item_id' => $soItem->id,
                            'requested_qty' => $soItem->quantity,
                            'picked_qty' => 0,
                            'status' => 'pending',
                        ]);
                    }
                });
                $pickList->load([
                    'pickListItems.salesOrderItem.book', 
                    'pickListItems.salesOrderItem.bundle', 
                    'pickListItems.salesOrderItem.bookIndex.book'
                ]);
            }

            \Log::info('Pick List loaded successfully:', [
                'id' => $pickList->id,
                'pick_list_number' => $pickList->pick_list_number,
                'items_count' => $pickList->pickListItems->count(),
                'sales_order_id' => $pickList->salesOrder->id ?? null,
                'prepared_by' => $pickList->preparedByUser->name ?? 'System'
            ]);

            return view('production.logistic.pick-list-details', [
                'pickList' => $pickList
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading pick list: ' . $e->getMessage(), [
                'id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function markAsGathered(Request $request)
    {
        try {
            // Get order_id from request (JSON) or route parameter
            $orderId = $request->input('order_id') ?? $request->route('id');
            
            if (!$orderId) {
                return response()->json(['success' => false, 'message' => 'Order ID is required'], 400);
            }
            
            $order = \App\Models\SalesOrder::with('items')->findOrFail($orderId);
            
            // Sync submitted items if passed (e.g. from Mark as Gathered form serialization)
            $submittedItems = $request->input('picked_items', $request->input('items', []));
            if (empty($submittedItems) && $request->has('picked_items_json')) {
                $submittedItems = json_decode($request->input('picked_items_json'), true) ?: [];
            }

            if (!empty($submittedItems) && is_array($submittedItems)) {
                $currentPickList = \App\Models\PickList::where('sales_order_id', $order->id)
                    ->where('status', '!=', 'completed')
                    ->first();
                if ($currentPickList) {
                    foreach ($submittedItems as $itemData) {
                        $plItem = null;
                        if (!empty($itemData['id'])) {
                            $plItem = \App\Models\PickListItem::where('pick_list_id', $currentPickList->id)->find($itemData['id']);
                        } elseif (!empty($itemData['so_item_id'])) {
                            $plItem = \App\Models\PickListItem::where('pick_list_id', $currentPickList->id)->where('sales_order_item_id', $itemData['so_item_id'])->first();
                        }
                        if ($plItem) {
                            if (isset($itemData['picked_qty'])) {
                                $plItem->picked_qty = floatval($itemData['picked_qty']);
                            }
                            if (isset($itemData['status'])) {
                                $plItem->status = $itemData['status'];
                            }
                            if (isset($itemData['notes'])) {
                                $plItem->notes = $itemData['notes'];
                            }
                            $plItem->save();
                        }
                    }
                }
            }

            // Pick list gathered: move order to appropriate next queue
            $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']);
            if ($order->type === 'ecom_direct') {
                $newStatus = 'ready_for_delivery';
                $targetQueue = 'Packing Management';
            } elseif ($order->type === 'complimentary') {
                $newStatus = 'pending_ar_prep';
                $targetQueue = 'Acknowledgement Receipt (Complimentary) Preparation';
            } else {
                $newStatus = $isConsignment ? 'pending_dr_prep' : 'pending_si_prep';
                $targetQueue = $isConsignment ? 'Delivery Receipt (DR) Preparation' : 'Sales Invoice (SI) Preparation';
            }

            $order->update([
                'status' => $newStatus,
                'gathered_at' => now(),
                'gathered_by' => auth()->id()
            ]);

            // Retrieve pending pick lists for this sales order to deduct stock and forward only picked qty
            $pendingPickLists = \App\Models\PickList::where('sales_order_id', $orderId)
                ->where('status', '!=', 'completed')
                ->with('pickListItems.salesOrderItem.book')
                ->get();

            $soCreator = $order->preparedBy ?: auth()->user();
            $salesTeam = $soCreator ? $soCreator->sales_team : null;

            foreach ($pendingPickLists as $pl) {
                foreach ($pl->pickListItems as $plItem) {
                    $soItem = $plItem->salesOrderItem;
                    if ($soItem) {
                        $origQty = (float)$soItem->quantity;
                        // Preserve original quantity in sent_qty if not already set
                        if (empty($soItem->sent_qty) || (float)$soItem->sent_qty == 0) {
                            $soItem->sent_qty = $origQty;
                        }
                        // If picked_qty was recorded (> 0) and item is no longer pending, use it; otherwise fallback to requested or original item quantity
                        $pickedQty = ($plItem->status !== 'pending' && $plItem->picked_qty !== null && (float)$plItem->picked_qty > 0) 
                            ? (float)$plItem->picked_qty 
                            : ((float)$plItem->requested_qty > 0 ? (float)$plItem->requested_qty : $origQty);

                        $unfulfilledQty = max(0, $origQty - $pickedQty);
                        if ($unfulfilledQty > 0) {
                            \App\Services\StockDeductionService::adjustForDRReturn($order, [[
                                'book_id' => $soItem->book_id,
                                'book_index_id' => $soItem->book_index_id,
                                'book_bundle_id' => $soItem->book_bundle_id,
                                'quantity' => $unfulfilledQty,
                            ]]);
                        }
                        
                        // Record Inventory Transaction for audit trail
                        if ($soItem->book && $pickedQty > 0) {
                            \App\Models\InventoryTransaction::create([
                                'book_id' => $soItem->book->id,
                                'type' => 'out',
                                'quantity' => $pickedQty,
                                'location' => $salesTeam ? ($salesTeam . ' Stock') : 'Main Warehouse',
                                'source' => 'Sales Order Picklist',
                                'reference_number' => $order->so_number,
                                'unit_cost' => $soItem->book->cost ?? 0,
                                'total_cost' => $pickedQty * ($soItem->book->cost ?? 0),
                                'notes' => 'Sales Order #' . $order->so_number . ' - Picked from Picklist #' . $pl->pick_list_number . ($salesTeam ? ' (' . $salesTeam . ')' : ''),
                                'status' => 'completed',
                                'transaction_date' => now(),
                                'user_id' => auth()->id()
                            ]);
                        }

                        // Update Sales Order Item quantity and subtotal to reflect actual picked quantity and discount
                        $soItem->quantity = $pickedQty;
                        $itemGross = $pickedQty * ($soItem->price ?? 0);
                        $itemDiscVal = (float)($soItem->discount_value ?? 0);
                        $itemDiscType = $soItem->discount_type ?? 'percentage';
                        $itemDiscAmt = (float)($soItem->discount_amount ?? 0);
                        if ($itemDiscAmt <= 0 && $itemDiscVal > 0) {
                            $itemDiscAmt = $itemDiscType === 'percentage' ? $itemGross * ($itemDiscVal / 100) : $itemDiscVal;
                        }
                        $itemDiscAmt = min($itemGross, max(0, $itemDiscAmt));
                        $soItem->discount_amount = $itemDiscAmt;
                        $soItem->subtotal = max(0, round($itemGross - $itemDiscAmt, 2));
                        $soItem->save();

                        // Mark pick list item status
                        $plItem->picked_qty = $pickedQty;
                        $plItem->status = ($pickedQty >= $plItem->requested_qty && $plItem->requested_qty > 0) ? 'picked' : ($pickedQty > 0 ? 'short' : 'pending');
                        $plItem->save();
                    }
                }
            }

            // Recalculate Sales Order total amount based on updated item quantities and order discounts
            $order->load('items');
            $itemsSubtotal = $order->items->sum('subtotal');
            $soDiscount = (float)($order->discount_amount ?? 0);
            if (($order->discount_percentage ?? 0) > 0) {
                $soDiscount = $itemsSubtotal * ((float)$order->discount_percentage / 100);
            }
            $newTotalAmount = max(0, $itemsSubtotal - $soDiscount);
            $order->update(['total_amount' => $newTotalAmount, 'discount_amount' => $soDiscount]);

            // Sync Sales Invoice if exists
            $si = \App\Models\SalesInvoice::where('so_id', $order->id)->first();
            if ($si) {
                $si->update(['total_amount' => $newTotalAmount]);
            }

            // Mark all associated pick lists as completed
            \App\Models\PickList::where('sales_order_id', $orderId)
                ->where('status', '!=', 'completed')
                ->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'completed_by' => auth()->id()
                ]);

            // Log the action
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Pick list gathered',
                'description' => 'Order marked as gathered for SO ' . $order->so_number,
                'reference_type' => 'SalesOrder',
                'reference_id' => $order->id,
                'details' => json_encode(['gathered_at' => now()])
            ]);

            // If AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order marked as gathered and moved to ' . $targetQueue
                ]);
            }

            // Otherwise redirect
            return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as gathered and moved to ' . $targetQueue . '.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public static function restoreSalesOrderStock(\App\Models\SalesOrder $so)
    {
        // Check if stock was gathered/deducted for this order
        $isGathered = $so->gathered_at != null || in_array($so->status, ['pending_dr_prep', 'pending_si_prep', 'ready_for_packing', 'ready_for_delivery', 'completed']);
        if (!$isGathered) {
            return;
        }

        $pickLists = \App\Models\PickList::where('sales_order_id', $so->id)
            ->with('pickListItems.salesOrderItem.book')
            ->get();

        $soCreator = $so->preparedBy ?: auth()->user();
        $salesTeam = $soCreator ? $soCreator->sales_team : null;

        foreach ($pickLists as $pl) {
            foreach ($pl->pickListItems as $plItem) {
                $soItem = $plItem->salesOrderItem;
                if ($soItem) {
                    $restoredQty = $plItem->picked_qty !== null ? (float)$plItem->picked_qty : (float)$soItem->quantity;
                    if ($restoredQty > 0) {
                        if ($salesTeam) {
                            // Restore Sales Team Stock
                            $teamStock = \App\Models\TeamStock::where('team_name', $salesTeam)
                                ->where(function($q) use ($soItem) {
                                    if ($soItem->book_index_id) $q->where('book_index_id', $soItem->book_index_id);
                                    elseif ($soItem->book_id) $q->where('book_id', $soItem->book_id);
                                    elseif ($soItem->bundle_id) $q->where('book_bundle_id', $soItem->bundle_id);
                                })->first();

                            if ($teamStock) {
                                $teamStock->quantity += $restoredQty;
                                $teamStock->save();
                            }

                            // Restore Team SiteInventory
                            $teamSite = \App\Models\Site::where('name', $salesTeam)->first();
                            if ($teamSite) {
                                $teamSiteInv = \App\Models\SiteInventory::where('site_id', $teamSite->id)
                                    ->where(function($q) use ($soItem) {
                                        if ($soItem->book_index_id) $q->where('book_index_id', $soItem->book_index_id);
                                        elseif ($soItem->book_id) $q->where('book_id', $soItem->book_id);
                                        elseif ($soItem->bundle_id) $q->where('book_bundle_id', $soItem->bundle_id);
                                    })->first();

                                if ($teamSiteInv) {
                                    $teamSiteInv->quantity += $restoredQty;
                                    $teamSiteInv->save();
                                }
                            }
                        } else if ($soItem->book) {
                            // Restore Main Warehouse Book Stock
                            $book = $soItem->book;
                            $book->stock += $restoredQty;
                            $book->save();

                            // Restore Main Warehouse SiteInventory
                            $mainSiteInv = \App\Models\SiteInventory::where('site_id', 1)
                                ->where('book_id', $book->id)
                                ->first();
                            if ($mainSiteInv) {
                                $mainSiteInv->quantity += $restoredQty;
                                $mainSiteInv->save();
                            }
                        }

                        // Record Inventory Transaction for audit trail
                        if ($soItem->book) {
                            \App\Models\InventoryTransaction::create([
                                'book_id' => $soItem->book->id,
                                'type' => 'in',
                                'quantity' => $restoredQty,
                                'remarks' => 'Stock restored due to deletion of Sales Order ' . $so->so_number,
                                'user_id' => auth()->id() ?? 1,
                            ]);
                        }
                    }
                }
            }
        }

        // Reset gathered status flag so stock is not restored twice
        $so->update([
            'gathered_at' => null,
            'gathered_by' => null,
        ]);
    }

    public function deletePickList($id)
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admin can delete pick lists.');
        }

        try {
            $pickList = \App\Models\PickList::findOrFail($id);

            if ($pickList->salesOrder) {
                self::restoreSalesOrderStock($pickList->salesOrder);
            }
            
            // Log the deletion
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Pick list deleted',
                'description' => 'Pick list ' . $pickList->pick_list_number . ' was deleted and stock restored',
                'reference_type' => 'PickList',
                'reference_id' => $pickList->id,
                'details' => json_encode(['pick_list_number' => $pickList->pick_list_number])
            ]);
            
            // Delete items and pick list
            \App\Models\PickListItem::where('pick_list_id', $pickList->id)->delete();
            $pickList->delete();
            
            return redirect()->back()->with('success', 'Pick list ' . $pickList->pick_list_number . ' deleted successfully and stock returned to inventory.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting pick list: ' . $e->getMessage());
        }
    }

    public function deliveryScheduling()
    {
        $allOrdersQuery = \App\Models\SalesOrder::with(['customer', 'preparedBy', 'freightQuotation', 'invoice', 'deliveryReceipt', 'items.book', 'items.bookIndex', 'items.bundle'])
            ->where(function($q) {
                $q->where('status', 'ready_for_delivery')
                  ->orWhere(function($sub) {
                      $sub->where('is_pickup', true)->whereIn('status', ['ready_for_delivery', 'ready_for_pickup', 'completed']);
                  });
            })
            ->where('status', '!=', 'ready_for_packing')
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct']);
        $this->excludeTeamSalesOrders($allOrdersQuery);
        $allOrders = $allOrdersQuery->orderByRaw('COALESCE(delivery_date, created_at) DESC')->get();

        $allOrders = $allOrders->reject(function($order) {
            if (in_array($order->status, ['ready_for_delivery', 'ready_for_pickup'])) {
                return false;
            }
            $hasCompletedInvoice = $order->invoice && in_array(strtolower($order->invoice->status ?? ''), ['approved', 'completed', 'posted', 'signed']);
            return $hasCompletedInvoice;
        });

        $landtripOrders = $allOrders->where('is_pickup', false)->where('status', 'ready_for_delivery')->values();
        $pickupOrders = $allOrders->where('is_pickup', true)->values();

        $approvedRequests = \App\Models\PickupRequest::with('createdByUser')
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('requested_date', 'asc')
            ->get();

        $drivers = \App\Models\User::where('position', 'Driver')
            ->where('status', true)
            ->get();

        return view('production.logistic.delivery-scheduling', [
            'orders' => $landtripOrders,
            'pickupOrders' => $pickupOrders,
            'approvedRequests' => $approvedRequests,
            'drivers' => $drivers,
            'title' => 'Delivery Scheduling',
            'role' => 'Dispatcher',
            'sidebar' => 'production'
        ]);
    }

    public function setAsPickup(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Please select at least one Sales Order to set as For Pickup.');
        }

        \App\Models\SalesOrder::whereIn('id', (array)$orderIds)->update([
            'is_pickup' => true
        ]);

        return redirect()->back()->with('success', count((array)$orderIds) . ' order(s) moved to For Pickup tab successfully.');
    }

    public function markAsPickedUp(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        $order->update([
            'status' => 'completed',
            'picked_up_at' => now(),
        ]);

        $dr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->orWhere('so_number', $order->so_number)->first();
        if ($dr) {
            $dr->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as completed (Picked Up).');
    }

    public function moveBackToDelivery(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        $order->update([
            'is_pickup' => false,
            'status' => 'ready_for_delivery',
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->so_number . ' moved back to Landtrip Manifest.');
    }

    public function pickupRequestsIndex()
    {
        $user = auth()->user();
        $query = \App\Models\PickupRequest::with(['createdByUser', 'driver'])->orderBy('id', 'desc');

        if ($user && $user->position === 'Driver') {
            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            $query->where(function($q) use ($user, $fullName) {
                $q->where('driver_id', $user->id);
                if ($fullName) {
                    $q->orWhere('driver_name', $fullName)
                      ->orWhere('driver_name', 'like', '%' . ($user->first_name ?: $fullName) . '%');
                }
            });
        }

        $requests = $query->get();
        $drivers = \App\Models\User::where('position', 'Driver')->where('status', true)->get();

        return view('production.logistic.pickup-requests.index', [
            'requests' => $requests,
            'drivers' => $drivers,
            'title' => 'Logistics Service Orders',
            'sidebar' => 'production'
        ]);
    }

    public function pickupRequestsCreate()
    {
        if (auth()->user()?->position === 'Driver') {
            return redirect()->route('production.logistic.pickup-requests.index')
                ->with('error', 'Unauthorized. Drivers cannot create logistics service orders.');
        }

        return view('production.logistic.pickup-requests.create', [
            'title' => 'Create Request',
            'sidebar' => 'production'
        ]);
    }

    public function pickupRequestsStore(Request $request)
    {
        if (auth()->user()?->position === 'Driver') {
            return redirect()->route('production.logistic.pickup-requests.index')
                ->with('error', 'Unauthorized. Drivers cannot create logistics service orders.');
        }

        $validated = $request->validate([
            'type' => 'required|in:delivery,pickup,pull_out,driver_vehicle',
            'client_name' => 'required|string|max:255',
            'address' => 'required|string',
            'requested_date' => 'required|date',
            'driver_id' => 'nullable',
            'driver_name' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
            'items_details' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if (!empty($validated['driver_id'])) {
            $driverUser = \App\Models\User::find($validated['driver_id']);
            if ($driverUser) {
                $validated['driver_name'] = trim(($driverUser->first_name ?? '') . ' ' . ($driverUser->last_name ?? ''));
            }
        }

        $validated['status'] = 'pending_approval';
        $validated['created_by'] = auth()->id();

        \App\Models\PickupRequest::create($validated);

        return redirect()->route('production.logistic.pickup-requests.index')->with('success', 'Request created successfully and sent to approval queue.');
    }

    public function pickupRequestsEdit($id)
    {
        if (auth()->user()?->position === 'Driver') {
            return redirect()->route('production.logistic.pickup-requests.index')
                ->with('error', 'Unauthorized. Drivers cannot edit logistics service orders.');
        }

        $requestItem = \App\Models\PickupRequest::findOrFail($id);
        return view('production.logistic.pickup-requests.edit', [
            'requestItem' => $requestItem,
            'title' => 'Edit Request',
            'sidebar' => 'production'
        ]);
    }

    public function pickupRequestsUpdate(Request $request, $id)
    {
        if (auth()->user()?->position === 'Driver') {
            return redirect()->route('production.logistic.pickup-requests.index')
                ->with('error', 'Unauthorized. Drivers cannot edit logistics service orders.');
        }

        $requestItem = \App\Models\PickupRequest::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:delivery,pickup,pull_out,driver_vehicle',
            'client_name' => 'required|string|max:255',
            'address' => 'required|string',
            'requested_date' => 'required|date',
            'driver_id' => 'nullable',
            'driver_name' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
            'items_details' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if (!empty($validated['driver_id'])) {
            $driverUser = \App\Models\User::find($validated['driver_id']);
            if ($driverUser) {
                $validated['driver_name'] = trim(($driverUser->first_name ?? '') . ' ' . ($driverUser->last_name ?? ''));
            }
        }

        $requestItem->update($validated);

        return redirect()->route('production.logistic.pickup-requests.index')->with('success', 'Request updated successfully.');
    }

    public function pickupRequestsDestroy($id)
    {
        if (auth()->user()?->position === 'Driver') {
            return redirect()->route('production.logistic.pickup-requests.index')
                ->with('error', 'Unauthorized. Drivers cannot delete logistics service orders.');
        }

        $requestItem = \App\Models\PickupRequest::findOrFail($id);
        $requestItem->delete();
        return redirect()->route('production.logistic.pickup-requests.index')->with('success', 'Request deleted successfully.');
    }

    public function pickupRequestsApprove(Request $request, $id)
    {
        $requestItem = \App\Models\PickupRequest::findOrFail($id);
        $requestItem->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Request approved successfully and sent to Delivery Scheduling.');
    }

    public function pickupRequestsReject(Request $request, $id)
    {
        $requestItem = \App\Models\PickupRequest::findOrFail($id);
        $requestItem->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);
        return redirect()->back()->with('success', 'Request rejected.');
    }

    public function pickupRequestsComplete(Request $request, $id)
    {
        $requestItem = \App\Models\PickupRequest::findOrFail($id);
        $requestItem->update([
            'status' => 'completed',
        ]);
        return redirect()->back()->with('success', 'Request marked as completed.');
    }

    public function pickupRequestsAssignDriver(Request $request, $id)
    {
        if (auth()->user()?->position === 'Driver') {
            return redirect()->route('production.logistic.pickup-requests.index')
                ->with('error', 'Unauthorized. Drivers cannot assign drivers to logistics service orders.');
        }

        $requestItem = \App\Models\PickupRequest::findOrFail($id);

        $validated = $request->validate([
            'driver_id' => 'nullable',
            'driver_name' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['driver_id'])) {
            $driverUser = \App\Models\User::find($validated['driver_id']);
            if ($driverUser) {
                $validated['driver_name'] = trim(($driverUser->first_name ?? '') . ' ' . ($driverUser->last_name ?? ''));
            }
        }

        $requestItem->update($validated);

        return redirect()->back()->with('success', 'Driver/Vehicle assigned to Request REQ-' . str_pad($requestItem->id, 5, '0', STR_PAD_LEFT) . ' successfully.');
    }

    public function markAsDelivered(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);
        
        // Check if this is a COD (Cash on Delivery) order - PAID orders can skip this check
        if ($order->type !== 'paid' && $order->transaction_type === 'COD') {
            // Verify that COD collection has been approved by accounting
            $collection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
            
            if (!$collection) {
                return redirect()->back()->with('error', 'Cannot mark delivery as complete: COD collection not found. Please create a rider collection first.');
            }
            
            if ($collection->status !== 'verified') {
                return redirect()->back()->with('error', 'Cannot mark delivery as complete: COD collection must be verified by accounting first. Current status: ' . $collection->status);
            }
        }
        
        $order->update([
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->so_number . ' marked as delivered.');
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'plate_number' => 'required|string|max:255',
            'helper' => 'nullable|string|max:255',
            'delivery_date' => 'required|date',
            'remarks' => 'nullable|string',
            'ref_number' => 'nullable|string|max:255',
        ]);

        $order = \App\Models\SalesOrder::findOrFail($id);
        $driver = \App\Models\User::findOrFail($request->driver_id);
        
        // Verify that the selected user is a Driver
        if ($driver->position !== 'Driver') {
            return redirect()->back()->with('error', 'Selected user is not a Driver. Only users with Driver position can be assigned.');
        }
        
        $order->update([
            'driver_id' => $request->driver_id,
            'driver' => $driver->first_name . ' ' . $driver->last_name,
            'plate_number' => $request->plate_number,
            'helper' => $request->helper,
            'delivery_date' => $request->delivery_date,
            'remarks' => $request->remarks,
            'ref_number' => $request->ref_number,
            'driver_approval_status' => 'pending_approval',
            'driver_approved_by' => null,
            'driver_approved_at' => null,
        ]);

        // Create RiderCollection if this is a COD (Cash on Delivery) order
        if ($order->transaction_type === 'COD') {
            // Check if RiderCollection already exists for this order
            $existingCollection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
            
            if (!$existingCollection) {
                // Create new rider collection
                \App\Models\RiderCollection::create([
                    'sales_order_id' => $order->id,
                    'rider_id' => $request->driver_id,
                    'amount_to_collect' => $order->total_amount,
                    'transaction_type' => $order->transaction_type,
                    'status' => 'pending',
                ]);
                
                // Update SO collection status
                $order->update([
                    'collection_status' => 'pending_collection',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Driver ' . $driver->first_name . ' ' . $driver->last_name . ' assigned to Order #' . $order->so_number . ' (Pending Approval).');
    }

    public function approveDriverAssignment(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if (!$order->driver_id && !$order->driver) {
            return redirect()->back()->with('error', 'Cannot approve: No driver assigned to this order.');
        }

        $order->update([
            'driver_approval_status' => 'approved',
            'driver_approved_by' => auth()->id(),
            'driver_approved_at' => now(),
        ]);

        // Create RiderCollection if COD order and not yet created
        if ($order->transaction_type === 'COD') {
            $existingCollection = \App\Models\RiderCollection::where('sales_order_id', $order->id)->first();
            if (!$existingCollection) {
                \App\Models\RiderCollection::create([
                    'sales_order_id' => $order->id,
                    'rider_id' => $order->driver_id,
                    'amount_to_collect' => $order->total_amount,
                    'transaction_type' => $order->transaction_type,
                    'status' => 'pending',
                ]);
                $order->update([
                    'collection_status' => 'pending_collection',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Driver assignment for Order #' . $order->so_number . ' approved successfully.');
    }

    public function rejectDriverAssignment(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        $order->update([
            'driver_id' => null,
            'driver' => null,
            'plate_number' => null,
            'helper' => null,
            'driver_approval_status' => 'rejected',
            'driver_approved_by' => auth()->id(),
            'driver_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Driver assignment for Order #' . $order->so_number . ' rejected.');
    }

    public function printTransmittal($id)
    {
        $order = \App\Models\SalesOrder::with('customer', 'items.book')->findOrFail($id);

        return view('production.logistic.transmittal-slip', [
            'order' => $order,
        ]);
    }

    public function purchaseOrder()
    {
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        $products = \App\Models\Product::where('is_active', true)->get();
        return view('production.logistic.purchase-order', [
            'suppliers' => $suppliers,
            'products' => $products,
            'title' => 'Purchase Order',
            'sidebar' => 'production'
        ]);
    }

    public function storePurchaseOrder(Request $request)
    {
        // Basic validation - would be more robust in production
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'required|unique:purchase_orders,po_number',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $po = \App\Models\PurchaseOrder::create([
            'po_number' => $request->po_number,
            'supplier_id' => $request->supplier_id,
            'date' => $request->date,
            'terms' => $request->terms,
            'invoice_number' => $request->invoice_number,
            'total_amount' => $request->total_amount,
            'prepared_by' => auth()->id(),
            'status' => 'ordered'
        ]);

        foreach ($request->items as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'isbn' => $item['isbn'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $item['total_amount'],
            ]);
        }

        return redirect()->route('production.logistic.purchase-order-list')->with('success', 'Purchase Order #' . $po->po_number . ' created successfully.');
    }

    public function purchaseOrderList()
    {
        $purchaseOrders = \App\Models\PurchaseOrder::with('supplier', 'preparedBy')
            ->latest()
            ->get();

        return view('production.logistic.purchase-order-list', [
            'purchaseOrders' => $purchaseOrders,
            'title' => 'Purchase Orders',
            'sidebar' => 'production'
        ]);
    }

    public function showPurchaseOrder(Request $request, $id)
    {
        $po = \App\Models\PurchaseOrder::with('supplier', 'items.product', 'preparedBy')->find($id);
        if (!$po) {
            $po = \App\Models\PurchaseOrder::with('supplier', 'items.product', 'preparedBy')->where('po_number', $id)->first();
        }
        if (!$po) {
            abort(404, 'Purchase Order not found');
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('production.logistic.partials.purchase-order-modal', compact('po'));
        }

        return view('production.logistic.purchase-order-show', [
            'po' => $po,
            'title' => 'Purchase Order Details',
            'sidebar' => 'production'
        ]);
    }

    public function destroyPurchaseOrder($id)
    {
        $po = \App\Models\PurchaseOrder::findOrFail($id);
        
        // Log the activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Purchase Order deleted',
            'description' => 'Purchase Order #' . $po->po_number . ' has been deleted from the system.',
            'reference_type' => 'PurchaseOrder',
            'reference_id' => $id,
            'details' => json_encode(['po_number' => $po->po_number, 'amount' => $po->total_amount])
        ]);

        $po->delete();

        return redirect()->route('production.logistic.purchase-order-list')->with('success', 'Purchase Order deleted successfully.');
    }

    public function receivingReportList()
    {
        $reports = \App\Models\ReceivingReport::with('purchaseOrder', 'supplier', 'receivedBy')
            ->latest()
            ->get();

        return view('production.logistic.receiving-report-list', [
            'reports' => $reports,
            'title' => 'Receiving Reports',
            'sidebar' => 'production'
        ]);
    }

    public function showReceivingReport(Request $request, $id)
    {
        $rr = \App\Models\ReceivingReport::with(['purchaseOrder', 'supplier', 'receivedBy', 'items.product'])->findOrFail($id);

        if ($request->ajax()) {
            return view('production.logistic.partials.receiving-report-modal', compact('rr'));
        }

        return view('production.logistic.receiving-report-show', [
            'rr' => $rr,
            'title' => 'Receiving Report Details',
            'sidebar' => 'production'
        ]);
    }

    public function createReceivingReport($po_id = null)
    {
        $purchaseOrder = null;
        if ($po_id) {
            $purchaseOrder = \App\Models\PurchaseOrder::with('items.product', 'supplier')->findOrFail($po_id);
        }

        $openPOs = \App\Models\PurchaseOrder::with('supplier')
            ->whereIn('status', ['ordered', 'partially_received'])
            ->get();

        return view('production.logistic.receiving-report-form', [
            'purchaseOrder' => $purchaseOrder,
            'openPOs' => $openPOs,
            'title' => 'Create Receiving Report',
            'sidebar' => 'production'
        ]);
    }

    public function storeReceivingReport(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'rr_number' => 'required|unique:receiving_reports,rr_number',
            'received_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $po = \App\Models\PurchaseOrder::findOrFail($request->purchase_order_id);

        $rr = \App\Models\ReceivingReport::create([
            'rr_number' => $request->rr_number,
            'purchase_order_id' => $po->id,
            'supplier_id' => $po->supplier_id,
            'received_date' => $request->received_date,
            'received_by' => auth()->id(),
            'status' => 'posted',
            'notes' => $request->notes
        ]);

        foreach ($request->items as $itemId => $data) {
            if ($data['quantity_received'] > 0) {
                $poItem = \App\Models\PurchaseOrderItem::findOrFail($itemId);
                
                $rr->items()->create([
                    'purchase_order_item_id' => $poItem->id,
                    'product_id' => $poItem->product_id,
                    'quantity_received' => $data['quantity_received'],
                    'unit_cost' => $poItem->unit_price,
                    'total_cost' => $data['quantity_received'] * $poItem->unit_price
                ]);

                // Update PO Item received quantity
                $poItem->increment('received_quantity', $data['quantity_received']);

                // Update Inventory
                if ($poItem->product_id) {
                    $product = \App\Models\Product::find($poItem->product_id);
                    if ($product) {
                        $source = $product->source;
                        if ($source) {
                            $source->increment('stock', $data['quantity_received']);
                            
                            // Explicitly sync to Main Warehouse Site Inventory
                            if ($source instanceof \App\Models\Book) {
                                \App\Models\SiteInventory::updateOrCreate(
                                    [
                                        'site_id' => 1, // Main Warehouse
                                        'book_id' => $source->id
                                    ],
                                    [
                                        'quantity' => $source->stock
                                    ]
                                );
                            }
                        }
                        
                        // Record Inventory Transaction
                        \App\Models\InventoryTransaction::create([
                            'product_id' => $product->id,
                            'type' => 'in',
                            'quantity' => $data['quantity_received'],
                            'location' => 'Main Warehouse', // Default
                            'source' => 'Purchase Order',
                            'supplier' => $po->supplier->company_name,
                            'reference_number' => $rr->rr_number,
                            'unit_cost' => $poItem->unit_price,
                            'total_cost' => $data['quantity_received'] * $poItem->unit_price,
                            'notes' => 'Received via RR #' . $rr->rr_number,
                            'status' => 'completed',
                            'transaction_date' => $request->received_date,
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }
        }

        // Update PO Overall Status based on total vs received quantity
        $allTotal = $po->items->sum('quantity');
        $allReceived = $po->items->sum('received_quantity');

        if ($allReceived >= $allTotal && $allTotal > 0) {
            $po->update(['status' => 'received']);
        } else if ($allReceived > 0) {
            $po->update(['status' => 'partially_received']);
        } else {
            $po->update(['status' => 'ordered']);
        }

        // --- ACCOUNTING INTEGRATION ---
        $this->accounting->postReceivingReportEntry($rr);

        return redirect()->route('production.logistic.receiving-report-list')->with('success', 'Receiving Report #' . $rr->rr_number . ' posted and inventory updated.');
    }

    public function driverDashboard(Request $request)
    {
        $user = auth()->user();
        $driverId = $user->id;
        $driverName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

        // 1. Fetch Today's Deliveries (Always active for today's date)
        $todayDate = date('Y-m-d');
        $todayDeliveries = \App\Models\SalesOrder::with(['customer', 'items.book', 'riderCollection'])
            ->where(function($q) use ($driverId, $driverName) {
                $q->where('driver_id', $driverId);
                if ($driverName) {
                    $q->orWhere('driver', $driverName);
                }
            })
            ->whereIn('status', ['ready_for_delivery', 'in_transit'])
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->whereDate('delivery_date', $todayDate)
            ->latest()
            ->get()
            ->sortBy(function($order) {
                return $order->status === 'in_transit' ? 0 : 1;
            })
            ->values();

        // 2. Fetch All Assigned Deliveries (With date range filter)
        $query = \App\Models\SalesOrder::with(['customer', 'items.book', 'riderCollection'])
            ->where(function($q) use ($driverId, $driverName) {
                $q->where('driver_id', $driverId);
                if ($driverName) {
                    $q->orWhere('driver', $driverName);
                }
            })
            ->whereIn('status', ['ready_for_delivery', 'in_transit'])
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct']);

        if ($request->filled('start_date')) {
            $query->whereDate('delivery_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('delivery_date', '<=', $request->end_date);
        }

        $allDeliveries = $query->latest()->get()
            ->sortBy(function($order) {
                return $order->status === 'in_transit' ? 0 : 1;
            })
            ->values();

        // 3. Fetch Assigned Logistics Service Orders (PickupRequests)
        $pickupQuery = \App\Models\PickupRequest::with('createdByUser')
            ->where(function($q) use ($driverId, $driverName, $user) {
                $q->where('driver_id', $driverId);
                if ($driverName) {
                    $q->orWhere('driver_name', $driverName)
                      ->orWhere('driver_name', 'like', '%' . ($user->first_name ?: $driverName) . '%');
                }
            });

        if ($request->filled('start_date')) {
            $pickupQuery->whereDate('requested_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $pickupQuery->whereDate('requested_date', '<=', $request->end_date);
        }

        $allPickupRequests = $pickupQuery->orderBy('requested_date', 'desc')->get();

        $todayPickupRequests = $allPickupRequests->filter(function($pr) use ($todayDate) {
            return $pr->requested_date && \Carbon\Carbon::parse($pr->requested_date)->format('Y-m-d') === $todayDate && $pr->status !== 'completed';
        })->values();

        return view('production.logistic.driver-dashboard', [
            'todayDeliveries' => $todayDeliveries,
            'allDeliveries' => $allDeliveries,
            'assignedDeliveries' => $allDeliveries, // Keep as alias for stats or fallback
            'todayPickupRequests' => $todayPickupRequests,
            'allPickupRequests' => $allPickupRequests,
            'title' => 'Driver Dashboard',
            'role' => 'Driver',
            'sidebar' => 'production'
        ]);
    }

    public function deliveryTracking(Request $request)
    {
        $query = \App\Models\SalesOrder::with('customer')
            ->whereIn('status', ['ready_for_delivery', 'in_transit', 'completed'])
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct']);

        // Search Filter (Ref Number, Customer Name, Driver, Plate Number, Address)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'like', "%{$search}%")
                  ->orWhere('driver', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%")
                  ->orWhere('shipping_address', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by Driver
        if ($request->filled('driver')) {
            if ($request->driver === 'unassigned') {
                $query->where(function($q) {
                    $q->whereNull('driver')->orWhere('driver', '');
                });
            } else {
                $query->where('driver', $request->driver);
            }
        }

        // Filter by Date (single date or range)
        if ($request->filled('date')) {
            $date = $request->date;
            $query->where(function($q) use ($date) {
                $q->whereDate('delivery_date', $date)
                  ->orWhere(function($sub) use ($date) {
                      $sub->whereNull('delivery_date')
                          ->where(function($dSub) use ($date) {
                              $dSub->whereDate('updated_at', $date)
                                   ->orWhereDate('created_at', $date);
                          });
                  });
            });
        } elseif ($request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('start_date')) {
                $startDate = $request->start_date;
                $query->where(function($q) use ($startDate) {
                    $q->whereDate('delivery_date', '>=', $startDate)
                      ->orWhere(function($sub) use ($startDate) {
                          $sub->whereNull('delivery_date')->whereDate('updated_at', '>=', $startDate);
                      });
                });
            }
            if ($request->filled('end_date')) {
                $endDate = $request->end_date;
                $query->where(function($q) use ($endDate) {
                    $q->whereDate('delivery_date', '<=', $endDate)
                      ->orWhere(function($sub) use ($endDate) {
                          $sub->whereNull('delivery_date')->whereDate('updated_at', '<=', $endDate);
                      });
                });
            }
        }

        $deliveries = $query->latest()->get();

        // Distinct Customers with orders
        $orderCustomerIds = \App\Models\SalesOrder::whereIn('status', ['ready_for_delivery', 'in_transit', 'completed'])
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');
        $customers = \App\Models\Customer::whereIn('customer_id', $orderCustomerIds)
            ->orderBy('customer_name')
            ->get(['customer_id', 'customer_name', 'company_name']);
        if ($customers->isEmpty()) {
            $customers = \App\Models\Customer::orderBy('customer_name')->limit(100)->get(['customer_id', 'customer_name', 'company_name']);
        }

        // Distinct Drivers from user accounts and sales orders
        $userDrivers = \App\Models\User::where('position', 'Driver')
            ->where('status', true)
            ->get()
            ->map(fn($u) => trim($u->first_name . ' ' . $u->last_name));
        $soDrivers = \App\Models\SalesOrder::whereIn('status', ['ready_for_delivery', 'in_transit', 'completed'])
            ->whereNotNull('driver')
            ->where('driver', '!=', '')
            ->distinct()
            ->pluck('driver');
        $drivers = $userDrivers->merge($soDrivers)->unique()->filter()->sort()->values();

        return view('production.logistic.delivery-tracking', [
            'deliveries' => $deliveries,
            'customers' => $customers,
            'drivers' => $drivers,
            'title' => 'Delivery Tracking',
            'role' => 'Dispatcher',
            'sidebar' => 'production'
        ]);
    }

    private function isAccountingUser($user)
    {
        if (!$user) return false;
        $dept = strtolower($user->department ?? '');
        $pos = strtolower($user->position ?? '');

        return str_contains($dept, 'accounting') || str_contains($dept, 'finance') || str_contains($dept, 'credit') ||
               str_contains($pos, 'accounting') || str_contains($pos, 'finance');
    }

    private function isAccountingOrderOrDr($order, $deliveryReceipt = null)
    {
        if ($order) {
            if (str_contains($order->remarks ?? '', '[ACCOUNTING_DR]')) {
                return true;
            }
            if ($order->drPreparedBy && $this->isAccountingUser($order->drPreparedBy)) {
                return true;
            }
            if (!$order->dr_prepared_by && $order->preparedBy && $this->isAccountingUser($order->preparedBy)) {
                return true;
            }
            // Any order created by a user with a sales team (or assigned area sales staff with sales team) belongs to Accounting DR
            $userTeam = (!empty($order->preparedBy?->sales_team) && trim($order->preparedBy->sales_team) !== '')
                ? trim($order->preparedBy->sales_team)
                : ((!empty($order->areaSalesStaff?->sales_team) && trim($order->areaSalesStaff->sales_team) !== '') ? trim($order->areaSalesStaff->sales_team) : null);
            if (!empty($userTeam)) {
                return true;
            }
        }
        if ($deliveryReceipt) {
            if (str_contains($deliveryReceipt->notes ?? '', '[ACCOUNTING_DR]')) {
                return true;
            }
            if ($deliveryReceipt->preparedByUser && $this->isAccountingUser($deliveryReceipt->preparedByUser)) {
                return true;
            }
            if ($deliveryReceipt->salesOrder) {
                return $this->isAccountingOrderOrDr($deliveryReceipt->salesOrder);
            }
        }
        return false;
    }

    public function deliveryReceiptList()
    {
        $isAccountingContext = request()->is('admin-finance*');

        // Get sales orders pending DR prep/approval
        $ordersQuery = \App\Models\SalesOrder::with('customer', 'preparedBy', 'drPreparedBy', 'items')
            ->whereNotIn('type', ['ecom_direct', 'calculator_pos'])
            ->where('so_number', 'not like', 'DI-ECOM-%')
            ->where('so_number', 'not like', 'POS-%')
            ->where(function($q) {
                $q->whereIn('status', ['pending_dr_prep', 'pending_dr_approval'])
                  ->orWhere(function($sq) {
                      $sq->where('status', 'si_created')
                         ->whereNull('dr_prepared_by')
                         ->whereNull('dr_prepared_at')
                         ->whereNull('dr_approved_by');
                  });
            });

        // Get sales orders where DR is completed
        $completedOrdersQuery = \App\Models\SalesOrder::with('customer', 'preparedBy', 'drPreparedBy', 'items')
            ->whereNotIn('type', ['ecom_direct', 'calculator_pos'])
            ->where('so_number', 'not like', 'DI-ECOM-%')
            ->where('so_number', 'not like', 'POS-%')
            ->where(function($q) {
                $q->whereIn('status', ['ready_for_packing', 'ready_for_delivery', 'completed', 'ar_created', 'cr_created', 'reconsignment_pending', 'pending_si_prep', 'pending_si_approval'])
                  ->orWhere(function($sq) {
                      $sq->where('status', 'si_created')
                         ->where(function($ssq) {
                             $ssq->whereNotNull('dr_prepared_by')
                                 ->orWhereNotNull('dr_prepared_at')
                                 ->orWhereNotNull('dr_approved_by');
                         });
                  });
            });

        if ($isAccountingContext) {
            $applyFilter = function ($query) {
                $query->where('so_number', 'not like', 'SO-NBS-%')
                ->where(function ($q) {
                    $q->whereHas('drPreparedBy', function ($u) {
                        $u->whereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                          ->orWhere('position', 'like', '%Accounting%')
                          ->orWhere('position', 'like', '%Finance%');
                    })
                    ->orWhereHas('preparedBy', function ($u) {
                        $u->whereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                          ->orWhere('position', 'like', '%Accounting%')
                          ->orWhere('position', 'like', '%Finance%')
                          ->orWhere(function ($st) {
                              $st->whereNotNull('sales_team')->where('sales_team', '!=', '');
                          });
                    })
                    ->orWhereHas('areaSalesStaff', function ($u) {
                        $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
                    })
                    ->orWhere('remarks', 'like', '%[ACCOUNTING_DR]%');
                });
            };

            $applyFilter($ordersQuery);
            $applyFilter($completedOrdersQuery);
        } else {
            $applyFilter = function ($query) {
                $query->where(function ($q) {
                    $q->where('so_number', 'like', 'SO-NBS-%')
                      ->orWhere(function ($sub) {
                          $sub->whereDoesntHave('drPreparedBy', function ($u) {
                              $u->whereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                                ->orWhere('position', 'like', '%Accounting%')
                                ->orWhere('position', 'like', '%Finance%');
                          })
                          ->whereDoesntHave('preparedBy', function ($u) {
                              $u->whereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                                ->orWhere('position', 'like', '%Accounting%')
                                ->orWhere('position', 'like', '%Finance%')
                                ->orWhere(function ($st) {
                                    $st->whereNotNull('sales_team')->where('sales_team', '!=', '');
                                });
                          })
                          ->whereDoesntHave('areaSalesStaff', function ($u) {
                              $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
                          });
                      });
                })
                ->where(function ($sub3) {
                    $sub3->whereNull('remarks')->orWhere('remarks', 'not like', '%[ACCOUNTING_DR]%');
                });
            };

            $applyFilter($ordersQuery);
            $applyFilter($completedOrdersQuery);
        }

        $orders = $ordersQuery->latest()->get();
        $completedOrders = $completedOrdersQuery->orderByRaw('COALESCE(dr_prepared_at, updated_at, created_at) DESC')->get();

        $sidebar = $isAccountingContext ? 'admin-finance' : 'production';

        return view('production.logistic.delivery-receipt-list', [
            'orders' => $orders,
            'completedOrders' => $completedOrders,
            'title' => 'Delivery Receipts',
            'sidebar' => $sidebar
        ]);
    }

    public function completeDR($id)
    {
        $user = auth()->user();
        $userPos = $user->position;
        $isSuperAdmin = $user->isSuperAdmin();
        $isAcct = $this->isAccountingUser($user);
        
        if (!$isSuperAdmin && !$isAcct &&
            !str_contains($userPos, 'Manager') && 
            !str_contains($userPos, 'Supervisor') && 
            !str_contains($userPos, 'Head') && 
            !str_contains($userPos, 'Senior Logistics Staff') && 
            !str_contains($userPos, 'Logistics Staff')) {
             return redirect()->back()->with('error', 'Only Super Admins, Accounting Staff, or Production/Logistics Managers, Supervisors, Heads, Senior Logistics Staff, or Logistics Staff can complete Delivery Receipts.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);

        $isAccountingContext = request()->is('admin-finance*') || str_contains(url()->previous(), 'admin-finance') || str_contains(request()->header('referer', ''), 'admin-finance') || str_contains($order->remarks ?? '', '[ACCOUNTING_DR]') || $this->isAccountingOrderOrDr($order);
        $remarks = $order->remarks ?? '';

        if ($isAccountingContext) {
            if (!str_contains($remarks, '[ACCOUNTING_DR]')) {
                $remarks = trim($remarks . ' [ACCOUNTING_DR]');
            }
            $newStatus = 'pending_dr_approval';
            $updateData = [
                'status' => $newStatus,
                'dr_prepared_at' => now(),
                'dr_prepared_by' => auth()->id(),
                'remarks' => $remarks
            ];
        } else {
            // Logistics DR: clicking complete DR moves order to packing management
            $newStatus = 'ready_for_packing';
            $updateData = [
                'status' => $newStatus,
                'dr_prepared_at' => now(),
                'dr_prepared_by' => auth()->id(),
                'dr_approved_at' => now(),
                'dr_approved_by' => auth()->id(),
                'signed_at' => now(),
                'remarks' => $remarks
            ];
        }

        $order->update($updateData);

        $dr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->first();
        if ($dr) {
            $drNotes = $dr->notes ?? '';
            if ($isAccountingContext && !str_contains($drNotes, '[ACCOUNTING_DR]')) {
                $drNotes = trim($drNotes . ' [ACCOUNTING_DR]');
            }
            $dr->update([
                'status' => $isAccountingContext ? 'pending_approval' : 'completed',
                'prepared_by' => auth()->id(),
                'notes' => $drNotes
            ]);
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $isAccountingContext ? 'DR Prepared & Submitted for Approval' : 'DR Completed & Moved to Packing',
            'description' => "Delivery Receipt for Sales Order {$order->so_number} " . ($isAccountingContext ? "prepared and submitted to Accounting for approval." : "completed and moved to Packing Management."),
            'reference_type' => 'SalesOrder',
            'reference_id' => $order->id,
        ]);

        $redirectRoute = $isAccountingContext ? 'admin-finance.accounting.delivery-receipt-list' : 'production.logistic.delivery-receipt-list';

        return redirect()->route($redirectRoute)->with('success', 'DR completed for Order #' . $order->so_number . '.');
    }


    public function requestReconsignment($id)
    {
        $order = \App\Models\SalesOrder::with('items')->findOrFail($id);
        
        // Ensure order is area_consignment or area_sales_consignment and status is in valid statuses
        if (!in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']) || !in_array($order->status, ['pending_dr_prep', 'ready_for_packing', 'ready_for_delivery', 'ar_created', 'cr_created', 'si_created', 'pending_si_approval', 'pending_si_prep', 'completed'])) {
            return redirect()->back()->with('error', 'Invalid order status for reconsignment.');
        }

        // Calculate remaining items to reconsign (Sent Qty - Picked Qty)
        $remainingCount = 0;
        foreach ($order->items as $item) {
            $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($order) {
                $query->where('so_id', $order->id)->where('status', '!=', 'cancelled');
            })->where('book_id', $item->book_id)->sum('quantity');

            $pickedQty = max($alreadyPurchasedQty, (int)($item->customer_selected_qty ?? 0));
            $rem = max(0, $item->quantity - $pickedQty);
            $remainingCount += $rem;
        }

        if ($remainingCount <= 0) {
            return redirect()->back()->with('error', 'All items in this order have been picked/purchased. No remaining items left to reconsign.');
        }

        // Update Sales Order status to reconsignment_pending
        $order->update(['status' => 'reconsignment_pending']);

        // Find the previous Delivery Receipt and close it
        $previousDr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->first();
        if ($previousDr) {
            $previousDr->update(['status' => 'completed']);
        }

        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Reconsignment Requested',
            'description' => "Reconsignment request submitted for Sales Order {$order->so_number} ({$remainingCount} remaining pcs to reconsign).",
            'affected_model' => 'SalesOrder',
            'affected_model_id' => $order->id,
        ]);

        return redirect()->back()->with('success', "Reconsignment request for {$remainingCount} remaining item(s) submitted to Credit and Collection.");
    }

    public function deliveryReceipt($id = null)
    {
        $user = auth()->user();
        $userPos = $user->position;
        $isSuperAdmin = $user->isSuperAdmin();
        $isAcct = $this->isAccountingUser($user);
        
        if (!$isSuperAdmin && !$isAcct && 
            !str_contains($userPos, 'Manager') && 
            !str_contains($userPos, 'Supervisor') && 
            !str_contains($userPos, 'Head') && 
            !str_contains($userPos, 'Senior Logistics Staff') && 
            !str_contains($userPos, 'Logistics Staff')) {
             return redirect()->back()->with('error', 'Only Super Admins, Accounting Staff, or Production/Logistics Managers, Supervisors, Heads, Senior Logistics Staff, or Logistics Staff can prepare Delivery Receipts.');
        }

        $deliveryReceipt = null;
        $order = null;
        $isAccountingContext = request()->is('admin-finance*');
        
        if ($id) {
            // Check if it's a delivery receipt ID
            $deliveryReceipt = \App\Models\DeliveryReceipt::with('salesOrder', 'customer', 'items', 'preparedByUser')->find($id);
            
            // If not found, try finding by so_id (Sales Order ID)
            if (!$deliveryReceipt) {
                $deliveryReceipt = \App\Models\DeliveryReceipt::with('salesOrder', 'customer', 'items', 'preparedByUser')
                    ->where('so_id', $id)
                    ->first();
            }

            // Set $order from the delivery receipt if it exists, otherwise find the Sales Order by ID
            if ($deliveryReceipt) {
                $order = $deliveryReceipt->salesOrder;
                if ($order) {
                    $order->load(['customer', 'items.book', 'items.bookIndex', 'items.bundle', 'items.product', 'items.pickListItems', 'preparedBy', 'drPreparedBy']);
                }
            } else {
                $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bookIndex', 'items.bundle', 'items.product', 'items.pickListItems', 'preparedBy', 'drPreparedBy'])->find($id);
            }

            if ($order || $deliveryReceipt) {
                $isAcctDR = $this->isAccountingOrderOrDr($order, $deliveryReceipt);
                if (!request()->has('print') && !request()->has('autoprint') && $isAccountingContext && !$isAcctDR) {
                    return redirect()->route('admin-finance.accounting.delivery-receipt-list')->with('error', 'The requested Delivery Receipt belongs to Logistics.');
                }
                if (!request()->has('print') && !request()->has('autoprint') && !$isAccountingContext && $isAcctDR) {
                    return redirect()->route('production.logistic.delivery-receipt-list')->with('error', 'The requested Delivery Receipt belongs to Accounting.');
                }
            }
        }

        // Get sales orders for dropdown, filtered by context
        $salesOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.product')
            ->whereNotIn('type', ['ecom_direct', 'calculator_pos'])
            ->where('so_number', 'not like', 'DI-ECOM-%')
            ->where('so_number', 'not like', 'POS-%')
            ->whereIn('status', ['gathered', 'pending_si_prep', 'pending_si_approval', 'ready_for_delivery']);

        if ($isAccountingContext) {
            $salesOrdersQuery->where(function ($q) {
                $q->whereHas('drPreparedBy', function ($u) {
                    $u->where('division', 'like', '%Admin%')
                      ->orWhere('division', 'like', '%Finance%')
                      ->orWhereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                      ->orWhere('position', 'like', '%Accounting%')
                      ->orWhere('position', 'like', '%Finance%');
                })
                ->orWhere(function ($sub) {
                    $sub->whereNull('dr_prepared_by')
                        ->whereHas('preparedBy', function ($u) {
                            $u->where('division', 'like', '%Admin%')
                              ->orWhere('division', 'like', '%Finance%')
                              ->orWhereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                              ->orWhere('position', 'like', '%Accounting%')
                              ->orWhere('position', 'like', '%Finance%')
                              ->orWhere(function ($st) {
                                  $st->whereNotNull('sales_team')->where('sales_team', '!=', '');
                              });
                        });
                })
                ->orWhereHas('areaSalesStaff', function ($u) {
                    $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
                })
                ->orWhere('remarks', 'like', '%[ACCOUNTING_DR]%');
            });
        } else {
            $salesOrdersQuery->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereDoesntHave('drPreparedBy', function ($u) {
                        $u->where('division', 'like', '%Admin%')
                          ->orWhere('division', 'like', '%Finance%')
                          ->orWhereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                          ->orWhere('position', 'like', '%Accounting%')
                          ->orWhere('position', 'like', '%Finance%');
                    })
                    ->where(function ($sub2) {
                        $sub2->whereNotNull('dr_prepared_by')
                             ->orWhereDoesntHave('preparedBy', function ($u) {
                                 $u->where('division', 'like', '%Admin%')
                                   ->orWhere('division', 'like', '%Finance%')
                                   ->orWhereIn('department', ['Accounting', 'Admin & Finance', 'Credit and Collection'])
                                   ->orWhere('position', 'like', '%Accounting%')
                                   ->orWhere('position', 'like', '%Finance%')
                                   ->orWhere(function ($st) {
                                       $st->whereNotNull('sales_team')->where('sales_team', '!=', '');
                                   });
                             });
                    })
                    ->whereDoesntHave('areaSalesStaff', function ($u) {
                        $u->whereNotNull('sales_team')->where('sales_team', '!=', '');
                    });
                })
                ->where(function ($sub3) {
                    $sub3->whereNull('remarks')->orWhere('remarks', 'not like', '%[ACCOUNTING_DR]%');
                });
            });
        }

        $salesOrders = $salesOrdersQuery->latest()->get();
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $sidebar = $isAccountingContext ? 'admin-finance' : 'production';

        return view('production.logistic.delivery-receipt', [
            'deliveryReceipt' => $deliveryReceipt,
            'order' => $order,
            'salesOrders' => $salesOrders,
            'customers' => $customers,
            'title' => 'Delivery Receipt',
            'sidebar' => $sidebar
        ]);
    }

    public function bulkPrintDR(Request $request)
    {
        $ids = $request->input('ids', $request->input('id', []));
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter((array)$ids);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No Delivery Receipts selected for bulk printing.');
        }

        $orders = \App\Models\SalesOrder::with([
            'customer', 
            'items.book', 
            'items.product', 
            'items.bundle',
            'items.bookIndex',
            'items.pickListItems',
            'preparedBy', 
            'drPreparedBy', 
            'drApprovedBy',
            'signedBy', 
            'acctApprovedBy', 
            'mktApprovedBy'
        ])
            ->whereIn('id', $ids)
            ->get();

        $deliveryReceiptsMap = \App\Models\DeliveryReceipt::with('items')
            ->whereIn('so_id', $ids)
            ->get()
            ->keyBy('so_id');

        $isSingle = $orders->count() === 1;
        $title = $isSingle ? 'Delivery Receipt - ' . $orders->first()->so_number : 'Bulk Print Delivery Receipts';

        return view('production.logistic.bulk-print-dr', [
            'orders' => $orders,
            'deliveryReceiptsMap' => $deliveryReceiptsMap,
            'title' => $title
        ]);
    }

    public function storeDeliveryReceipt(Request $request, $id = null)
    {
        $user = auth()->user();
        $userPos = $user->position;
        $isSuperAdmin = $user->isSuperAdmin();
        $isAcct = $this->isAccountingUser($user);
        
        if (!$isSuperAdmin && !$isAcct && 
            !str_contains($userPos, 'Manager') && 
            !str_contains($userPos, 'Supervisor') && 
            !str_contains($userPos, 'Head') && 
            !str_contains($userPos, 'Senior Logistics Staff') && 
            !str_contains($userPos, 'Logistics Staff')) {
             return redirect()->back()->with('error', 'Only Super Admins, Accounting Staff, or Production/Logistics Managers, Supervisors, Heads, Senior Logistics Staff, or Logistics Staff can prepare Delivery Receipts.');
        }

        $validated = $request->validate([
            'dr_number' => 'required|unique:delivery_receipts,dr_number' . ($id ? ',' . $id : ''),
            'so_id' => 'required|exists:sales_orders,id',
            'customer_id' => 'required|exists:customers,customer_id',
            'delivery_address' => 'nullable|string',
            'delivery_date' => 'required|date',
            'status' => 'required|in:pending,completed,in-transit',
            'items' => 'required|array',
        ]);

        $isAccountingContext = request()->is('admin-finance*') || str_contains(url()->previous(), 'admin-finance') || str_contains(request()->header('referer', ''), 'admin-finance');

        try {
            $so = \App\Models\SalesOrder::find($validated['so_id']);
            if ($so && $isAccountingContext) {
                $remarks = $so->remarks ?? '';
                if (!str_contains($remarks, '[ACCOUNTING_DR]')) {
                    $so->update(['remarks' => trim($remarks . ' [ACCOUNTING_DR]')]);
                }
            }

            if ($id) {
                $dr = \App\Models\DeliveryReceipt::findOrFail($id);
                if ($isAccountingContext && !str_contains($dr->notes ?? '', '[ACCOUNTING_DR]')) {
                    $validated['notes'] = trim(($dr->notes ?? '') . ' [ACCOUNTING_DR]');
                }
                $dr->update($validated);
                $dr->items()->delete();
            } else {
                $validated['so_number'] = $so ? $so->so_number : null;
                $validated['prepared_by'] = auth()->id();
                $validated['prepared_at'] = now();
                if ($isAccountingContext) {
                    $validated['notes'] = trim(($validated['notes'] ?? '') . ' [ACCOUNTING_DR]');
                }
                $dr = \App\Models\DeliveryReceipt::create($validated);
            }

            // Create/update items if provided
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    \App\Models\DeliveryReceiptItem::create([
                        'dr_id' => $dr->id,
                        'product_name' => $item['product_name'] ?? null,
                        'quantity' => $item['quantity'] ?? 0,
                        'unit_price' => $item['unit_price'] ?? 0,
                        'amount' => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0),
                    ]);
                }
            }

            // Calculate total amount
            $dr->total_amount = $dr->items()->sum('amount');
            $dr->save();

            $redirectRoute = $isAccountingContext ? 'admin-finance.accounting.delivery-receipt-list' : 'production.logistic.delivery-receipt-list';

            return redirect()->route($redirectRoute)
                ->with('success', 'Delivery Receipt #' . $dr->dr_number . ' saved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error saving delivery receipt: ' . $e->getMessage());
        }
    }

    public function markAsDRPrepared($id)
    {
        $user = auth()->user();
        $userPos = $user->position;
        $isSuperAdmin = $user->isSuperAdmin();
        $isAcct = $this->isAccountingUser($user);
        
        if (!$isSuperAdmin && !$isAcct && 
            !str_contains($userPos, 'Manager') && 
            !str_contains($userPos, 'Supervisor') && 
            !str_contains($userPos, 'Head') && 
            !str_contains($userPos, 'Senior Logistics Staff') && 
            !str_contains($userPos, 'Logistics Staff')) {
             return redirect()->back()->with('error', 'Only Super Admins, Accounting Staff, or Production/Logistics Managers, Supervisors, Heads, Senior Logistics Staff, or Logistics Staff can prepare Delivery Receipts.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        $order->update(['status' => 'pending_dr_approval']);

        // Send Notification to Director if status is "pending_dr_approval"
        $director = \App\Models\User::where('position', 'Director')->first();
        if ($director) {
            try {
                $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
            } catch (\Exception $e) {
                \Log::error("Failed to send Sales Order DR approval notification: " . $e->getMessage());
            }
        }

        $isAccountingContext = request()->is('admin-finance*') || str_contains(url()->previous(), 'admin-finance') || str_contains(request()->header('referer', ''), 'admin-finance');
        $redirectRoute = $isAccountingContext ? 'admin-finance.accounting.delivery-receipt-list' : 'production.logistic.delivery-receipt-list';

        return redirect()->route($redirectRoute)->with('success', 'DR marked as prepared for Order #' . $order->so_number . '. Pending approval.');
    }

    public function approveDR($id)
    {
        // Role Enforcement: Super Admin OR Production Manager or Logistics Supervisor/Head OR Accounting Staff
        $user = auth()->user();
        $userPos = $user->position;
        $isSuperAdmin = $user->isSuperAdmin();
        $isAcct = $this->isAccountingUser($user);
        
        if (!$isSuperAdmin && !$isAcct && 
            !str_contains($userPos, 'Manager') && 
            !str_contains($userPos, 'Supervisor') && 
            !str_contains($userPos, 'Head') && 
            !str_contains($userPos, 'Senior Logistics Staff') && 
            !str_contains($userPos, 'Logistics Staff')) {
             return redirect()->back()->with('error', 'Only Super Admins, Accounting Staff, or Production/Logistics Managers, Supervisors, Heads, Senior Logistics Staff, or Logistics Staff can approve Delivery Receipts.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        $isAccountingContext = request()->is('admin-finance*') || str_contains(url()->previous(), 'admin-finance') || str_contains(request()->header('referer', ''), 'admin-finance') || $this->isAccountingOrderOrDr($order);
        $isCharge = $order->type === 'charge' || strtolower($order->transaction_type ?? '') === 'charge';
        $hasSI = !empty($order->si_prepared_at) || !empty($order->si_number) || \App\Models\SalesInvoice::where('so_id', $order->id)->exists();
        
        $isTeamOrder = (!empty($order->preparedBy?->sales_team) && trim($order->preparedBy->sales_team) !== '')
            || (!empty($order->areaSalesStaff?->sales_team) && trim($order->areaSalesStaff->sales_team) !== '');

        if ($isTeamOrder) {
            $newStatus = 'completed';
        } elseif ($isCharge || $hasSI) {
            $newStatus = 'ready_for_packing';
        } elseif ($isAccountingContext && !in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']) && $order->transaction_type !== 'consignment') {
            $newStatus = 'pending_si_prep';
        } else {
            $newStatus = 'ready_for_packing';
        }

        $updateData = [
            'status' => $newStatus,
            'dr_approved_at' => now(),
            'dr_approved_by' => auth()->id(),
            'signed_at' => now()
        ];

        if (empty($order->dr_prepared_at)) {
            $updateData['dr_prepared_at'] = now();
            $updateData['dr_prepared_by'] = auth()->id();
        }

        $order->update($updateData);

        $dr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->first();
        if ($dr) {
            $dr->update(['status' => 'completed', 'prepared_by' => $dr->prepared_by ?: auth()->id()]);
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DR Approved & Signed',
            'description' => "Delivery Receipt for Sales Order {$order->so_number} approved and signed by " . auth()->user()->name . ".",
            'reference_type' => 'SalesOrder',
            'reference_id' => $order->id,
        ]);

        $redirectRoute = $isAccountingContext ? 'admin-finance.accounting.delivery-receipt-list' : 'production.logistic.delivery-receipt-list';

        return redirect()->route($redirectRoute)->with('success', 'DR approved and signed for Order #' . $order->so_number . '.');
    }

    public function viewDeliveryForm($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bookIndex', 'items.bundle', 'preparedBy', 'siPreparedBy', 'drPreparedBy'])->findOrFail($id);
        
        $requestedType = request('type');
        if ($requestedType === 'AR') {
            $documentType = 'ACKNOWLEDGEMENT RECEIPT';
        } elseif ($requestedType === 'CR') {
            $documentType = 'CONSIGNMENT DELIVERY RECEIPT';
        } elseif ($requestedType === 'DR') {
            $documentType = 'DELIVERY RECEIPT';
        } else {
            // Logic to determine form type and title
            $documentType = 'DELIVERY RECEIPT';
            if (str_contains($order->type, 'consignment')) {
                $documentType = 'CONSIGNMENT DELIVERY RECEIPT';
            } elseif ($order->si_prepared_at) {
                $documentType = 'SALES INVOICE';
            } elseif ($order->ar_prepared_at) {
                $documentType = 'ACKNOWLEDGEMENT RECEIPT';
            }
        }

        return view('production.logistic.view-delivery-form', [
            'order'        => $order,
            'title'        => $documentType,
            'documentType' => $documentType,
            'role'         => 'Driver',
            'sidebar'      => 'production'
        ]);
    }

    /**
     * Consignment Receipt Tab in Logistics.
     */
    public function areaConsignment(Request $request)
    {
        $search = $request->query('search');

        $query = \App\Models\SalesOrder::with(['areaSalesStaff', 'items.book', 'customer', 'preparedBy'])
            ->whereIn('type', ['area_consignment', 'area_sales_consignment'])
            ->where(function($q) {
                $q->whereNotNull('cr_prepared_at')
                  ->orWhere('status', 'cr_created');
            })
            ->whereNotIn('status', ['draft', 'pending_mkt_approval', 'picking', 'cancelled']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhereHas('areaSalesStaff', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('production.logistic.area-consignment', [
            'title'   => 'Consignment Receipt',
            'role'    => auth()->user()->position,
            'sidebar' => 'production',
            'orders'  => $orders,
            'search'  => $search,
        ]);
    }

    /**
     * Move SO to Acknowledgement Receipt (AR).
     */
    public function moveToAR($id, Request $request)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if ($order->status === 'cr_created' || $order->cr_prepared_at !== null) {
            return redirect()->back()->with('error', "Sales Order {$order->so_number} has already been moved to Consignment Receipt and cannot be moved to Acknowledgement Receipt.");
        }

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('sales_orders/proof_of_payments', 'public');
            $order->proof_of_payment = $path;
        }

        // Proof of Payment is required before moving to Acknowledgement Receipt
        // (except for COD orders where payment is collected on delivery)
        $isPopExempt = in_array($order->type, ['cod', 'ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary']);
        if (!$isPopExempt && empty($order->proof_of_payment)) {
            return redirect()->back()->with('error', "Proof of Payment is required to move Sales Order {$order->so_number} to Acknowledgement Receipt. Please upload Proof of Payment first.");
        }

        $order->ar_prepared_at = now();
        $order->ar_prepared_by = auth()->id();
        $order->status = 'ar_created';
        $order->save();

        return redirect()->route('production.logistic.acknowledgement-receipt')
            ->with('success', "Sales Order {$order->so_number} moved to Acknowledgement Receipt.");
    }

    /**
     * Move SO to Consignment Receipt (CR).
     */
    public function moveToCR($id, Request $request)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if ($order->status === 'ar_created' || $order->ar_prepared_at !== null) {
            return redirect()->back()->with('error', "Sales Order {$order->so_number} has already been moved to Acknowledgement Receipt and cannot be moved to Consignment Receipt.");
        }

        $order->cr_prepared_at = now();
        $order->cr_prepared_by = auth()->id();
        $order->status = 'cr_created';
        $order->save();

        return redirect()->route('production.logistic.area-consignment')
            ->with('success', "Sales Order {$order->so_number} moved to Consignment Receipt.");
    }

    /**
     * Upload Proof of Payment in DR (makes it visible in Acknowledgement Receipt).
     */
    public function uploadDRProofOfPayment($id, Request $request)
    {
        $request->validate([
            'proof_of_payment' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        $order = \App\Models\SalesOrder::findOrFail($id);
        $path = $request->file('proof_of_payment')->store('sales_orders/proof_of_payments', 'public');

        $order->update([
            'proof_of_payment' => $path,
            'ar_prepared_at' => $order->ar_prepared_at ?? now(),
            'ar_prepared_by' => $order->ar_prepared_by ?? auth()->id(),
        ]);

        return redirect()->back()->with('success', "Proof of payment uploaded for SO {$order->so_number}. It is now visible in Acknowledgement Receipt.");
    }

    /**
     * Update Pick Qty directly from Delivery Receipt page.
     */
    public function updateDrPickQty($id, Request $request)
    {
        $order = \App\Models\SalesOrder::with('items')->findOrFail($id);

        if ($request->has('remarks')) {
            $order->update(['remarks' => $request->remarks]);
        }

        if ($request->has('pick_qty') && is_array($request->pick_qty)) {
            foreach ($request->pick_qty as $itemId => $qty) {
                $item = $order->items->where('id', $itemId)->first();
                if ($item) {
                    $pickQty = max(0, min((int)$qty, (int)$item->quantity));
                    $item->update(['customer_selected_qty' => $pickQty]);
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Delivery receipt updated successfully.']);
        }

        return redirect()->back()->with('success', "Delivery receipt updated successfully for SO {$order->so_number}.");
    }

    /**
     * Import Pick Quantities + Customer Name from Excel in Delivery Receipt (DR).
     */
    public function importDeliveryReceiptFromExcel(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'order_id'   => 'required|exists:sales_orders,id',
        ]);

        try {
            $file        = $request->file('excel_file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();

            $soNumberFromFile = trim((string) $sheet->getCell('B2')->getValue());

            $order = \App\Models\SalesOrder::with('items')
                ->whereIn('type', ['area_consignment', 'area_sales_consignment'])
                ->findOrFail($request->order_id);

            if (!empty($soNumberFromFile) && $soNumberFromFile !== $order->so_number) {
                return back()->withErrors([
                    'excel_file' => "The uploaded Excel is for SO \"{$soNumberFromFile}\" but this import is for SO \"{$order->so_number}\". Please upload the correct file.",
                ]);
            }

            // Read Customer Name from B7
            $customerName = trim((string) $sheet->getCell('B7')->getValue());

            if (!empty($customerName)) {
                $customer = \App\Models\Customer::whereRaw('LOWER(customer_name) = ?', [strtolower($customerName)])->first();

                if (!$customer) {
                    $customer = \App\Models\Customer::create([
                        'customer_name'  => $customerName,
                        'company_name'   => 'Individual',
                        'account_number' => 'CUST-' . strtoupper(uniqid()),
                        'manual_status'  => 'good',
                    ]);
                }

                $order->update(['customer_id' => $customer->customer_id]);
            }

            // Read Pick Qty from column G, starting row 10
            $dataStartRow = 10;
            $pickQtyCol   = 'G';

            $items    = $order->items->values();
            $updated  = 0;
            $rowIndex = $dataStartRow;

            foreach ($items as $item) {
                $cellValue = $sheet->getCell("{$pickQtyCol}{$rowIndex}")->getValue();

                if (!is_null($cellValue) && $cellValue !== '') {
                    $pickQty = (int) $cellValue;
                    $pickQty = max(0, min($pickQty, (int) $item->quantity));
                    $item->update(['customer_selected_qty' => $pickQty]);
                    $updated++;
                }

                $rowIndex++;
            }

            $msg = "Excel imported for SO {$order->so_number}. {$updated} item(s) Pick Qty saved.";
            if (!empty($customerName)) {
                $msg .= " Customer linked: {$customerName}.";
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error importing Excel in DR: ' . $e->getMessage());
            return back()->withErrors(['excel_file' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Acknowledgement Receipt  lists Area Sales Consignment SOs for logistics import.
     */
    public function acknowledgementReceipt()
    {
        $orders = \App\Models\SalesOrder::with(['areaSalesStaff', 'items.book', 'customer', 'preparedBy'])
            ->whereIn('type', ['area_consignment', 'area_sales_consignment'])
            ->where(function($query) {
                $query->whereNotNull('ar_prepared_at')
                      ->orWhereNotNull('proof_of_payment')
                      ->orWhere('status', 'ar_created');
            })
            ->whereNotIn('status', ['draft', 'pending_mkt_approval', 'picking', 'cancelled'])
            ->latest()
            ->get();

        return view('production.logistic.acknowledgement-receipt', [
            'title'   => 'Acknowledgement Receipt',
            'role'    => auth()->user()->position,
            'sidebar' => 'production',
            'orders'  => $orders,
        ]);
    }

    /**
     * Import / record pick quantities from the acknowledgement receipt.
     */
    public function importAcknowledgementReceipt(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:sales_orders,id',
            'pick_items' => 'required|array',
            'pick_items.*.item_id' => 'required|exists:sales_order_items,id',
            'pick_items.*.pick_qty' => 'required|integer|min:0',
        ]);

        $order = \App\Models\SalesOrder::where('type', 'area_sales_consignment')
            ->findOrFail($request->order_id);

        foreach ($request->pick_items as $entry) {
            \App\Models\SalesOrderItem::where('id', $entry['item_id'])
                ->where('sales_order_id', $order->id)
                ->update(['customer_selected_qty' => $entry['pick_qty']]);
        }

        return redirect()
            ->route('production.logistic.acknowledgement-receipt')
            ->with('success', 'Pick quantities saved for SO ' . $order->so_number . '.');
    }

    /**
     * Import Pick Quantities + Customer Name from the exported Excel file (SO_*.xlsx).
     * Excel structure (from exportSingleSalesOrder):
     *   Row 1  : Banner "AREA SALES CONSIGNMENT — SO-XXXX"
     *   Row 2  : Sales Order #  | <so_number>           ← B2
     *   Row 3  : Order Date     | date
     *   Row 4  : Area Sales Staff | name
     *   Row 5  : Status         | status
     *   Row 6  : Total Amount   | amount
     *   Row 7  : Customer Name  | [staff fills this in] ← B7
     *   Row 8  : blank
     *   Row 9  : Column headers (#, Book Title, Unit, Order Qty, Unit Price, Subtotal, Pick Qty)
     *   Row 10+: Item rows — column G = Pick Qty
     */
    public function importAcknowledgementReceiptFromExcel(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'order_id'   => 'required|exists:sales_orders,id',
        ]);

        try {
            $file        = $request->file('excel_file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();

            // ── Verify SO number matches the row's order ───────────
            $soNumberFromFile = trim((string) $sheet->getCell('B2')->getValue());

            $order = \App\Models\SalesOrder::with('items')
                ->where('id', $request->order_id)
                ->where('type', 'area_sales_consignment')
                ->findOrFail($request->order_id);

            if (!empty($soNumberFromFile) && $soNumberFromFile !== $order->so_number) {
                return back()->withErrors([
                    'excel_file' => "The uploaded Excel is for SO \"{$soNumberFromFile}\" but this import button is for SO \"{$order->so_number}\". Please upload the correct file.",
                ]);
            }

            // ── Read Customer Name from B7 ───────────────────────
            $customerName = trim((string) $sheet->getCell('B7')->getValue());

            if (!empty($customerName)) {
                // Try to find customer by name (case-insensitive)
                $customer = \App\Models\Customer::whereRaw('LOWER(customer_name) = ?', [strtolower($customerName)])->first();

                if (!$customer) {
                    // Create new customer on the fly if not found
                    $customer = \App\Models\Customer::create([
                        'customer_name'  => $customerName,
                        'company_name'   => 'Individual',
                        'account_number' => 'CUST-' . strtoupper(uniqid()),
                        'manual_status'  => 'good',
                    ]);
                }

                $order->update(['customer_id' => $customer->customer_id]);
            }

            // ── Read Pick Qty from column G, starting row 10 ────────
            // (Meta = rows 2-7, blank = row 8, header = row 9, data = row 10+)
            $dataStartRow = 10;
            $pickQtyCol   = 'G';

            $items    = $order->items->values(); // re-index
            $updated  = 0;
            $rowIndex = $dataStartRow;

            foreach ($items as $item) {
                $cellValue = $sheet->getCell("{$pickQtyCol}{$rowIndex}")->getValue();

                if (!is_null($cellValue) && $cellValue !== '') {
                    $pickQty = (int) $cellValue;
                    $pickQty = max(0, min($pickQty, (int) $item->quantity));
                    $item->update(['customer_selected_qty' => $pickQty]);
                    $updated++;
                }

                $rowIndex++;
            }

            $msg = "Excel imported for SO {$order->so_number}. {$updated} item(s) Pick Qty saved.";
            if (!empty($customerName)) {
                $customer = \App\Models\Customer::whereRaw('LOWER(customer_name) = ?', [strtolower($customerName)])->first();
                if ($customer) {
                    $msg .= " Customer linked: {$customerName}.";
                }
            }

            return redirect()
                ->route('production.logistic.acknowledgement-receipt')
                ->with('success', $msg);

        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Failed to read Excel file: ' . $e->getMessage()]);
        }
    }

    /**
     * Save customer selected quantities for evaluation orders
     * API endpoint for recording what customer chose to keep from evaluation
     */
    public function saveEvaluationSelections(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $soNumber = $request->input('so_number');
            $selections = $request->input('selections', []);

            // Validate
            if (!$orderId || empty($selections)) {
                return response()->json(['success' => false, 'message' => 'Invalid order or selections']);
            }

            // Get order
            $order = \App\Models\SalesOrder::with('items.book')->findOrFail($orderId);

            // Verify it's an evaluation order
            if ($order->type !== 'evaluation') {
                return response()->json(['success' => false, 'message' => 'This order is not an evaluation order']);
            }

            // Process selections
            $totalSelectedQty = 0;
            foreach ($selections as $selection) {
                $productName = $selection['product'];
                $selectedQty = floatval($selection['quantity']);

                // Find the SalesOrderItem by product name
                $item = $order->items()
                    ->whereHas('book', function($query) use ($productName) {
                        $query->where('name', $productName);
                    })
                    ->first();

                if ($item) {
                    // Update customer_selected_qty
                    $item->update(['customer_selected_qty' => $selectedQty]);
                    $totalSelectedQty += $selectedQty;
                }
            }

            // Record activity log
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Evaluation selections recorded',
                'description' => 'Customer selections recorded for evaluation SO ' . $soNumber . ' - Total items: ' . $totalSelectedQty,
                'reference_type' => 'SalesOrder',
                'reference_id' => $orderId,
                'details' => json_encode($selections)
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Customer selections saved successfully - Total items selected: ' . $totalSelectedQty
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving evaluation selections: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function savePickedItems(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:sales_orders,id',
                'so_number' => 'required|string',
                'picked_items' => 'nullable|array'
            ]);

            $order = \App\Models\SalesOrder::findOrFail($request->order_id);
            
            // Check if a pick list already exists for this SO (that's not completed)
            $pickList = \App\Models\PickList::where('sales_order_id', $order->id)
                ->where('status', '!=', 'completed')
                ->first();
            
            $remarks = $request->input('remarks', $request->input('notes', null));
            if (!$pickList) {
                // Generate unique pick list number only if creating new
                $pickListNumber = 'PL-' . $request->so_number . '-' . now()->format('YmdHis');
                
                // Create the PickList record
                $pickList = \App\Models\PickList::create([
                    'sales_order_id' => $order->id,
                    'pick_list_number' => $pickListNumber,
                    'status' => 'in_progress',
                    'prepared_by' => auth()->id(),
                    'notes' => $remarks,
                ]);
            } else {
                // Update existing pick list
                $pickList->update([
                    'notes' => $remarks !== null ? $remarks : $pickList->notes,
                ]);
            }

            if ($request->has('remarks') || $request->has('notes')) {
                $order->update(['remarks' => $remarks]);
            }

            // Get the sales order items for matching
            $soItems = $order->items()->get();

            // Create or update PickListItem records for each picked item if provided
            if ($request->has('picked_items') && is_array($request->picked_items)) {
                foreach ($request->picked_items as $pickedItem) {
                    $matchedItem = null;
                    
                    if (!empty($pickedItem['so_item_id'])) {
                        $matchedItem = $soItems->firstWhere('id', $pickedItem['so_item_id']);
                    }
                    if (!$matchedItem && !empty($pickedItem['id'])) {
                        $existingItem = \App\Models\PickListItem::find($pickedItem['id']);
                        if ($existingItem) {
                            $matchedItem = $soItems->firstWhere('id', $existingItem->sales_order_item_id);
                        }
                    }
                    if (!$matchedItem && isset($pickedItem['item_index'])) {
                        $matchedItem = $soItems[$pickedItem['item_index']] ?? null;
                    }
                    if (!$matchedItem && !empty($pickedItem['product'])) {
                        $prodName = trim($pickedItem['product']);
                        $matchedItem = $soItems->first(function ($item) use ($prodName) {
                            return ($item->book && trim($item->book->name) === $prodName)
                                || ($item->bundle && trim($item->bundle->name) === $prodName)
                                || ($item->bookIndex && trim($item->bookIndex->display_name) === $prodName)
                                || trim($item->item_name ?? '') === $prodName;
                        });
                    }

                    if ($matchedItem) {
                        $existingPlItem = \App\Models\PickListItem::where('pick_list_id', $pickList->id)
                            ->where('sales_order_item_id', $matchedItem->id)
                            ->first();

                        $reqQty = $existingPlItem ? $existingPlItem->requested_qty : $matchedItem->quantity;
                        $pQty = floatval($pickedItem['picked_qty']);
                        $status = $pickedItem['status'] ?? ($pQty >= $reqQty && $reqQty > 0 ? 'picked' : ($pQty > 0 ? 'short' : 'pending'));

                        \App\Models\PickListItem::updateOrCreate(
                            [
                                'pick_list_id' => $pickList->id,
                                'sales_order_item_id' => $matchedItem->id,
                            ],
                            [
                                'requested_qty' => $reqQty,
                                'picked_qty' => $pQty,
                                'status' => $status,
                                'notes' => $pickedItem['notes'] ?? null,
                            ]
                        );
                    }
                }
            }

            // Update order status - keep it as picking for all types
            $newStatus = 'picking';
            $order->update([
                'status' => $newStatus,
                'picked_at' => now(),
                'picked_by' => auth()->id()
            ]);

            // Store activity log for audit trail
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Pick list updated with items',
                'description' => 'Pick list items updated for SO ' . $request->so_number,
                'reference_type' => 'PickList',
                'reference_id' => $pickList->id,
                'details' => json_encode([
                    'pick_list_number' => $pickList->pick_list_number,
                    'items_count' => is_array($request->picked_items) ? count($request->picked_items) : 0,
                    'total_picked' => is_array($request->picked_items) ? array_sum(array_column($request->picked_items, 'picked_qty')) : 0
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pick list saved successfully for SO ' . $request->so_number,
                'pick_list_id' => $pickList->id,
                'pick_list_number' => $pickList->pick_list_number,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving picked items: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function shippingLabel($id, Request $request)
    {
        $order = \App\Models\SalesOrder::with('customer', 'items.book')->find($id);
        if (!$order) {
            $pickList = \App\Models\PickList::with('salesOrder.customer', 'salesOrder.items.book')->find($id);
            if ($pickList && $pickList->salesOrder) {
                $order = $pickList->salesOrder;
            } else {
                abort(404, 'Order or Pick List not found');
            }
        }

        $address = $request->query('address')
            ? urldecode($request->query('address'))
            : ($order->shipping_address ?: ($order->customer->shipping_address ?? ($order->customer->billing_address ?? 'N/A')));

        return view('production.logistic.shipping-label', [
            'order' => $order,
            'address' => $address
        ]);
    }

    public function packingManagement(Request $request)
    {
        // Get orders ready for packing - EXCLUDING ecom_direct, complimentary, and Team A, B, C
        $packingOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.book')
            ->whereIn('status', ['ready_for_delivery', 'ready_for_packing', 'pending_ar_prep'])
            ->whereNotIn('type', ['ecom_direct', 'complimentary'])
            ->where(function($query) {
                $query->whereNull('packing_data')
                      ->orWhere(function($innerQ) {
                          $innerQ->where('packing_data->status', '<>', 'ready_for_pickup')
                                  ->where('packing_data->status', '<>', 'gathered');
                      });
            });
        // Do not exclude ready_for_packing Sales Orders from Packing Queue
        $this->excludeTeamSalesOrders($packingOrdersQuery);
        $packingOrders = $packingOrdersQuery->orderBy('id', 'desc')->get();

        // Get complimentary orders ready for packing - EXCLUDING Team A, B, C
        $complimentaryPackingOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.book')
            ->whereIn('status', ['ready_for_delivery', 'ready_for_packing'])
            ->where('type', 'complimentary')
            ->where(function($query) {
                $query->whereNull('packing_data')
                      ->orWhere(function($innerQ) {
                          $innerQ->where('packing_data->status', '<>', 'ready_for_pickup')
                                  ->where('packing_data->status', '<>', 'gathered');
                      });
            });
        $this->excludeTeamSalesOrders($complimentaryPackingOrdersQuery);
        $complimentaryPackingOrders = $complimentaryPackingOrdersQuery->orderBy('id', 'desc')->get();

        // Get e-commerce direct orders ready for packing - EXCLUDING Team A, B, C
        $ecomPackingOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.book')
            ->whereIn('status', ['ready_for_delivery', 'packing', 'picked'])
            ->where(function($q) {
                $q->where('type', 'ecom_direct')
                  ->orWhere(function($subQ) {
                      $subQ->whereNotNull('ecom_platform')->where('ecom_platform', '!=', '');
                  });
            })
            ->where(function($query) {
                $query->whereNull('packing_data')
                      ->orWhere(function($innerQ) {
                          $innerQ->where('packing_data->status', '<>', 'ready_for_pickup')
                                  ->where('packing_data->status', '<>', 'gathered');
                      });
            });
        $this->excludeTeamSalesOrders($ecomPackingOrdersQuery);
        $ecomPackingOrders = $ecomPackingOrdersQuery->orderBy('id', 'desc')->get();

        // Organize e-com packing orders by platform
        $ecomByPlatform = [
            'lazada' => $ecomPackingOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'lazada';
            })->values(),
            'shopee' => $ecomPackingOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'shopee';
            })->values(),
            'tiktok' => $ecomPackingOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'tiktok';
            })->values(),
            'cob' => $ecomPackingOrders->filter(function($item) {
                $p = strtolower($item->ecom_platform ?? '');
                return !in_array($p, ['lazada', 'shopee', 'tiktok']);
            })->values(),
        ];

        // Get orders ready for pickup - EXCLUDING Team A, B, C
        $readyForPickupOrdersQuery = \App\Models\SalesOrder::with('customer', 'items.book')
            ->where('status', 'ready_for_delivery')
            ->where('type', '=', 'ecom_direct')
            ->whereNotNull('packing_data')
            ->where('packing_data->status', '=', 'ready_for_pickup')
            ->where(function($query) {
                $query->whereNull('packing_data->gathered_at')
                      ->orWhere('packing_data->gathered_at', '=', null);
            });
        $this->excludeTeamSalesOrders($readyForPickupOrdersQuery);
        $readyForPickupOrders = $readyForPickupOrdersQuery->orderBy('id', 'desc')->get();

        // Organize ready for pickup orders by platform
        $readyByPlatform = [
            'shopee' => $readyForPickupOrders->filter(function($item) {
                $p = strtolower($item->ecom_platform ?? $item->platform ?? $item->customer->customer_name ?? '');
                return str_contains($p, 'shopee') || str_contains($p, 'shoppee');
            })->values(),
            'tiktok' => $readyForPickupOrders->filter(function($item) {
                $p = strtolower($item->ecom_platform ?? $item->platform ?? $item->customer->customer_name ?? '');
                return str_contains($p, 'tiktok') || str_contains($p, 'tik');
            })->values(),
            'lazada' => $readyForPickupOrders->filter(function($item) {
                $p = strtolower($item->ecom_platform ?? $item->platform ?? $item->customer->customer_name ?? '');
                return str_contains($p, 'lazada') || str_contains($p, 'laz');
            })->values(),
            'cob' => $readyForPickupOrders->filter(function($item) {
                $p = strtolower($item->ecom_platform ?? $item->platform ?? $item->customer->customer_name ?? '');
                return str_contains($p, 'cob');
            })->values(),
        ];

        // Get completed drop-off orders (e-com orders that have been gathered)
        $completedDropoffOrders = \App\Models\SalesOrder::with('customer', 'items.book')
            ->where('type', '=', 'ecom_direct')
            ->where('status', 'completed')
            ->whereNotNull('packing_data')
            ->where('packing_data->status', '=', 'gathered')
            ->orderBy('id', 'desc')
            ->get();

        // Organize completed drop-off orders by platform
        $completedByPlatform = [
            'shopee' => $completedDropoffOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'shopee';
            })->values(),
            'tiktok' => $completedDropoffOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'tiktok';
            })->values(),
            'lazada' => $completedDropoffOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'lazada';
            })->values(),
            'cob' => $completedDropoffOrders->filter(function($item) {
                return strtolower($item->ecom_platform ?? '') === 'cob';
            })->values(),
        ];

        // Check if an order_id was passed to preload a specific order
        $preloadOrderId = $request->input('order_id');
        $preloadOrder = null;
        if ($preloadOrderId) {
            try {
                $preloadOrder = \App\Models\SalesOrder::with('customer', 'items.book')->findOrFail($preloadOrderId);
                \Log::info('Preloading order for packing management', [
                    'order_id' => $preloadOrderId,
                    'so_number' => $preloadOrder->so_number
                ]);
            } catch (\Exception $e) {
                \Log::warning('Could not preload order', ['order_id' => $preloadOrderId, 'error' => $e->getMessage()]);
            }
        }

        $teamStockPackingTransfers = \App\Models\TeamStockTransfer::with(['transferredByUser', 'items.book', 'items.bookIndex.book', 'items.bookBundle'])
            ->whereIn('status', ['packing', 'in_progress', 'not_started', 'completed'])
            ->latest()
            ->get();

        $allUsers = \App\Models\User::orderBy('first_name')->get();
        $allBookIndexes = \App\Models\BookIndex::with('book')->get();

        return view('production.logistic.packing-management', [
            'packingOrders' => $packingOrders,
            'complimentaryPackingOrders' => $complimentaryPackingOrders,
            'ecomByPlatform' => $ecomByPlatform,
            'readyForPickupOrders' => $readyForPickupOrders,
            'readyByPlatform' => $readyByPlatform,
            'completedDropoffOrders' => $completedDropoffOrders,
            'completedByPlatform' => $completedByPlatform,
            'preloadOrder' => $preloadOrder,
            'preloadOrderId' => $preloadOrderId,
            'teamStockPackingTransfers' => $teamStockPackingTransfers,
            'allUsers' => $allUsers,
            'allBookIndexes' => $allBookIndexes,
            'title' => 'Packing Management',
            'role' => 'Warehouse Staff',
            'sidebar' => 'production'
        ]);
    }

    public function getPackingOrderData($id)
    {
        try {
            \Log::info('Fetching packing order data for ID: ' . $id);
            
            $order = \App\Models\SalesOrder::with([
                'customer',
                'items',
                'items.book',
                'items.bookIndex.book',
                'items.bundle'
            ])->findOrFail($id);

            // Fail-safe: ensure order items with 0 quantity are auto-corrected to valid ordered quantity
            foreach ($order->items as $item) {
                if ((float)$item->quantity <= 0) {
                    $fixQty = (float)$item->customer_selected_qty > 0 
                        ? (float)$item->customer_selected_qty 
                        : ((float)$item->selected_qty > 0 
                            ? (float)$item->selected_qty 
                            : 1);
                    $fixSub = $fixQty * (float)($item->price ?: 0);
                    $item->setRawAttributes(array_merge($item->getAttributes(), [
                        'quantity' => $fixQty,
                        'subtotal' => $fixSub,
                    ]));
                    try {
                        \App\Models\SalesOrderItem::where('id', $item->id)->update([
                            'quantity' => $fixQty,
                            'subtotal' => $fixSub,
                        ]);
                    } catch (\Exception $e) {}
                }
            }

            $bName = $order->customer_representative;
            if (!$bName && $order->remarks && str_contains($order->remarks, 'Branch:')) {
                preg_match('/Branch:\s*([^|\n\r]+)/', $order->remarks, $m);
                $bName = trim($m[1] ?? '');
            }
            $bCompany = null;
            if ($bName && \Illuminate\Support\Facades\Schema::hasTable('companies')) {
                try {
                    $bCompany = \App\Models\Company::where('company_name', $bName)
                        ->orWhere('company_name', str_replace('AB-', 'AB - ', $bName))
                        ->orWhere('company_name', str_replace('AB - ', 'AB-', $bName))
                        ->first();
                } catch (\Exception $e) {}
            }
            $accountNo = $bCompany?->account_number ?: ($order->customer?->account_number ?? null);
            $acctCompany = ($accountNo && \Illuminate\Support\Facades\Schema::hasTable('companies')) ? \App\Models\Company::where('account_number', $accountNo)->first() : null;

            $order->display_company_name = $bCompany?->parent?->company_name 
                ?: ($bCompany?->company_name 
                ?: ($acctCompany?->parent?->company_name 
                ?: ($acctCompany?->company_name 
                ?: ($order->customer?->company_name && !in_array(strtolower($order->customer->company_name), ['intracode', 'individual']) ? $order->customer->company_name : ($order->customer?->customer_name ?? 'N/A')))));
            $order->display_account_number = $bCompany?->account_number ?: ($acctCompany?->account_number ?: ($order->customer?->account_number ?? 'N/A'));
            $order->display_address = $order->shipping_address ?: ($order->customer?->address ?: ($order->customer?->business_address ?? 'N/A'));

            \Log::info('Order loaded successfully', [
                'id' => $order->id,
                'so_number' => $order->so_number,
                'customer' => $order->customer->customer_name ?? 'N/A',
                'items_count' => $order->items->count()
            ]);

            return response()->json([
                'success' => true,
                'order' => $order,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Order not found for ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Order not found with ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching packing order data', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error loading order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function savePackingData(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $packingStatus = $request->input('packing_status', 'in_progress');
            $boxesCount = $request->input('boxes_count');
            $packingItems = $request->input('items', []);
            $attachments = $request->input('attachments', []);

            $order = \App\Models\SalesOrder::findOrFail($orderId);
            $existingPackingData = json_decode($order->packing_data ?? '{}', true) ?: [];
            $attachmentData = $existingPackingData['attachments'] ?? [];

            $isCompleted = ($packingStatus === 'completed');

            // Build packing data structure
            $packingData = [
                'status' => $isCompleted ? ($order->type === 'ecom_direct' ? 'ready_for_pickup' : 'gathered') : $packingStatus,
                'boxes_count' => $boxesCount !== null && $boxesCount !== '' ? (int) $boxesCount : null,
                'packed_by' => auth()->user()->name,
                'packed_at' => now()->toDateTimeString(),
            ];

            // Add item-level packing data
            if (!empty($packingItems)) {
                foreach ($packingItems as $item) {
                    $itemKey = 'item_' . $item['index'];
                    $packingData[$itemKey] = [
                        'packed_qty' => $item['packed_qty'],
                        'status' => $item['status'],
                        'notes' => $item['notes'],
                        'packed_date' => $item['packed_date'],
                    ];
                }
            } else {
                $order->load('items');
                foreach ($order->items as $idx => $oItem) {
                    $itemKey = 'item_' . $idx;
                    $packingData[$itemKey] = [
                        'packed_qty' => $oItem->quantity,
                        'status' => 'Packed',
                        'notes' => 'Quick packed',
                        'packed_date' => now()->toDateString(),
                    ];
                }
            }

            // Handle photo attachments
            if (!empty($attachments)) {
                // Process Photo 1
                if (!empty($attachments['photo_1'])) {
                    $attachmentData['photo_1'] = $this->saveBase64Image($attachments['photo_1'], $order->so_number, 'photo_1');
                }

                // Process Photo 2
                if (!empty($attachments['photo_2'])) {
                    $attachmentData['photo_2'] = $this->saveBase64Image($attachments['photo_2'], $order->so_number, 'photo_2');
                }
            }

            if (!empty($attachmentData)) {
                $packingData['attachments'] = $attachmentData;
            }

            $remarks = $request->input('remarks');
            if ($remarks !== null) {
                $packingData['remarks'] = $remarks;
            }

            if ($isCompleted) {
                if ($order->type === 'ecom_direct') {
                    $packingData['ready_for_pickup_at'] = now()->toDateTimeString();
                    $packingData['ready_for_pickup_by'] = auth()->user()->name;
                } else {
                    $packingData['gathered_at'] = now()->toDateTimeString();
                    $packingData['gathered_by'] = auth()->user()->name;
                }
            }

            // Update order with packing data and optional status change
            $updateData = [
                'packing_data' => json_encode($packingData),
                'packing_prepared_by' => auth()->user()->name,
            ];

            if ($remarks !== null) {
                $updateData['remarks'] = $remarks;
            }

            if ($isCompleted && $order->type !== 'ecom_direct') {
                $isPickup = in_array($order->delivery_method, ['pickup']) || in_array($order->shipping_method, ['pickup']);
                $updateData['status'] = $isPickup ? 'ready_for_pickup' : 'ready_for_delivery';
            }

            $order->update($updateData);

            // Log the activity
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $isCompleted ? 'Order packing complete' : 'Packing items marked',
                'description' => $isCompleted 
                    ? ($order->type === 'ecom_direct' 
                        ? 'SO ' . $order->so_number . ' marked as packed and ready for dropoff' 
                        : 'SO ' . $order->so_number . ' marked as packed and sent directly to delivery scheduling')
                    : 'Items packed for SO ' . $order->so_number,
                'reference_type' => 'SalesOrder',
                'reference_id' => $order->id,
                'details' => json_encode([
                    'packing_status' => $packingStatus,
                    'boxes_count' => $boxesCount,
                    'items_count' => count($packingItems),
                    'has_attachments' => !empty($attachmentData),
                    'packed_by' => auth()->user()->name
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Packing data saved successfully for SO ' . $order->so_number,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving packing data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }



    private function saveBase64Image($base64String, $soNumber, $photoName)
    {
        try {
            // Remove data:image/jpeg;base64, prefix if present
            if (strpos($base64String, 'data:image') === 0) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            }

            // Decode base64 string
            $imageData = base64_decode($base64String);

            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data');
            }

            // Create storage directory if it doesn't exist
            $storagePath = storage_path('app/public/packing-photos');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Generate unique filename
            $filename = 'SO-' . $soNumber . '-' . $photoName . '-' . time() . '.jpg';
            $filePath = $storagePath . '/' . $filename;

            // Save image
            file_put_contents($filePath, $imageData);

            // Return storage path for database
            return 'packing-photos/' . $filename;
        } catch (\Exception $e) {
            \Log::error('Error saving base64 image: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deletePackingOrder($id)
    {
        try {
            $so = \App\Models\SalesOrder::findOrFail($id);

            // 1. Restore stock if already gathered/deducted
            self::restoreSalesOrderStock($so);

            // 2. Delete associated pick lists & pick list items
            $pickLists = \App\Models\PickList::where('sales_order_id', $so->id)->get();
            foreach ($pickLists as $pl) {
                \App\Models\PickListItem::where('pick_list_id', $pl->id)->delete();
                $pl->delete();
            }

            $soNumber = $so->so_number;

            // 3. Delete order items and sales order
            $so->items()->delete();
            $so->delete();

            // Store activity log for audit trail
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Order deleted from Packing Management',
                'description' => 'Sales Order ' . $soNumber . ' deleted from packing management queue.',
                'reference_type' => 'SalesOrder',
                'reference_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order ' . $soNumber . ' deleted successfully from Packing Management.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting packing order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setReadyForPickup(Request $request)
    {
        try {
            $orderIds = $request->input('order_ids', []);
            
            if (empty($orderIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders selected'
                ], 400);
            }

            $updatedCount = 0;
            foreach ($orderIds as $orderId) {
                $order = \App\Models\SalesOrder::find($orderId);
                if ($order) {
                    $packingData = json_decode($order->packing_data ?? '{}', true);
                    
                    if ($order->type === 'ecom_direct') {
                        $packingData['status'] = 'ready_for_pickup';
                        $packingData['ready_for_pickup_at'] = now()->toDateTimeString();
                        $packingData['ready_for_pickup_by'] = auth()->user()->name;
                        
                        $order->update([
                            'packing_data' => json_encode($packingData)
                        ]);
                        
                        $action = 'Order marked as ready for pickup';
                        $description = 'SO ' . $order->so_number . ' marked as ready for pickup/drop-off';
                    } else {
                        // Standard SO: mark as gathered & send directly to delivery scheduling
                        $packingData['status'] = 'gathered';
                        $packingData['gathered_at'] = now()->toDateTimeString();
                        $packingData['gathered_by'] = auth()->user()->name;
                        
                        $isPickup = in_array($order->delivery_method, ['pickup']) || in_array($order->shipping_method, ['pickup']);
                        $newStatus = $isPickup ? 'ready_for_pickup' : 'ready_for_delivery';
                        
                        $order->update([
                            'status' => $newStatus,
                            'packing_data' => json_encode($packingData)
                        ]);
                        
                        $action = 'Order packing completed';
                        $description = 'SO ' . $order->so_number . ' marked as packed and sent directly to delivery scheduling';
                    }

                    // Log activity
                    \App\Models\ActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => $action,
                        'description' => $description,
                        'reference_type' => 'SalesOrder',
                        'reference_id' => $order->id,
                        'details' => json_encode([
                            'so_number' => $order->so_number,
                            'marked_by' => auth()->user()->name
                        ])
                    ]);

                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $updatedCount . ' order(s) processed successfully',
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error setting ready for pickup: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }



    public function markPackedOrdersAsGathered(Request $request)
    {
        try {
            $orderIds = $request->input('order_ids');
            if (empty($orderIds) && $request->has('order_id')) {
                $orderIds = [$request->input('order_id')];
            }
            
            if (empty($orderIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID is required'
                ], 400);
            }

            $updatedCount = 0;
            $lastTargetQueue = '';

            foreach ($orderIds as $orderId) {
                $order = \App\Models\SalesOrder::find($orderId);
                if (!$order) continue;

                // Update packing data status to gathered
                $packingData = json_decode($order->packing_data ?? '{}', true);
                $packingData['status'] = 'gathered';
                $packingData['gathered_at'] = now()->toDateTimeString();
                $packingData['gathered_by'] = auth()->user()->name;

                // Packing complete: move order to Delivery Scheduling or complete if ecom direct
                if ($order->type === 'ecom_direct') {
                    $newStatus = 'completed';
                    $targetQueue = 'Completed (E-Commerce)';
                } else {
                    $isPickup = in_array($order->delivery_method, ['pickup']) || in_array($order->shipping_method, ['pickup']);
                    $newStatus = $isPickup ? 'ready_for_pickup' : 'ready_for_delivery';
                    $targetQueue = 'Delivery Scheduling';
                }
                $lastTargetQueue = $targetQueue;

                $order->update([
                    'status' => $newStatus,
                    'packing_data' => json_encode($packingData)
                ]);

                // Log activity
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Packed order marked as gathered',
                    'description' => 'SO ' . $order->so_number . ' marked as gathered and moved to ' . $targetQueue,
                    'reference_type' => 'SalesOrder',
                    'reference_id' => $order->id,
                    'details' => json_encode([
                        'so_number' => $order->so_number,
                        'marked_by' => auth()->user()->name,
                        'action' => 'moved to ' . $targetQueue
                    ])
                ]);
                $updatedCount++;
            }

            $message = $updatedCount > 1
                ? "✓ {$updatedCount} order(s) successfully marked as gathered and ready for delivery!"
                : 'Order marked as gathered and moved to ' . $lastTargetQueue;

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Error marking packed orders as gathered: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking order as gathered: ' . $e->getMessage()
            ], 500);
        }
    }

    public function freightQuotation()
    {
        $quotations = \App\Models\FreightQuotation::with(['createdBy', 'approvedBy'])
            ->latest()
            ->paginate(20);

        return view('production.logistic.freight-quotation', [
            'title' => 'Freight Quotation',
            'sidebar' => 'production',
            'quotations' => $quotations,
        ]);
    }

    public function storeFreightQuotation(\Illuminate\Http\Request $request)
    {
        try {
            $validated = $request->validate([
                'quote_number' => 'required|string|max:255|unique:freight_quotations,quote_number',
                'quote_date' => 'required|date_format:Y-m-d',
                'validity_days' => 'required|integer|min:1|max:365',
                'terms' => 'nullable|string|max:255',
                'origin_contact' => 'nullable|string|max:255',
                'origin_address' => 'nullable|string',
                'origin_province' => 'nullable|string|max:255',
                'destination_contact' => 'nullable|string|max:255',
                'destination_address' => 'nullable|string',
                'destination_province' => 'nullable|string|max:255',
                'service_mode' => 'nullable|string|max:255',
                'freight_option' => 'nullable|string|in:freight_collect,freight_billing,bill_client',
                'forwarder' => 'nullable|string|max:255',
                'service_carrier' => 'nullable|string|max:255',
                'service_remarks' => 'nullable|string',
                'estimated_freight' => 'required|numeric|min:0',
                'valuation_percentage' => 'nullable|numeric|min:0|max:100',
                'handling_percentage' => 'nullable|numeric|min:0|max:100',
                'total_amount' => 'required|numeric|min:0',
            ], [
                'quote_number.required' => 'Quote number is required',
                'quote_number.unique' => 'This quote number already exists',
                'quote_date.required' => 'Please select a quote date',
                'quote_date.date_format' => 'Quote date must be in YYYY-MM-DD format',
                'validity_days.required' => 'Validity days is required',
                'validity_days.min' => 'Validity days must be at least 1',
                'estimated_freight.required' => 'Estimated freight amount is required',
                'estimated_freight.min' => 'Estimated freight cannot be negative',
                'total_amount.required' => 'Total amount is required',
                'total_amount.min' => 'Total amount cannot be negative',
            ]);

            // Build cargo items array
            $cargoItems = [];
            if ($request->has('cargo_qty') && is_array($request->cargo_qty)) {
                foreach ($request->cargo_qty as $index => $qty) {
                    if (!empty($qty)) {
                        $cargoItems[] = [
                            'qty' => (int) $qty,
                            'package_type' => $request->cargo_package_type[$index] ?? null,
                            'dimensions' => $request->cargo_dimensions[$index] ?? null,
                            'gross_weight' => (float) ($request->cargo_gross_weight[$index] ?? 0),
                            'vol_weight' => (float) ($request->cargo_vol_weight[$index] ?? 0),
                        ];
                    }
                }
            }

            // Calculate charges
            $estimatedFreight = (float) $validated['estimated_freight'];
            $handlingPercent = 0;
            $valuationCharge = 0;
            $handlingFee = 0;
            $totalAmount = $estimatedFreight;

            // Create freight quotation record
            $quotation = \App\Models\FreightQuotation::create([
                'quote_number' => $validated['quote_number'],
                'quote_date' => $validated['quote_date'],
                'validity_days' => $validated['validity_days'],
                'terms' => $request->input('terms'),
                'origin_contact' => $validated['origin_contact'] ?? null,
                'origin_address' => $validated['origin_address'] ?? null,
                'origin_province' => $validated['origin_province'] ?? null,
                'destination_contact' => $validated['destination_contact'] ?? null,
                'destination_address' => $validated['destination_address'] ?? null,
                'destination_province' => $validated['destination_province'] ?? null,
                'service_mode' => $validated['service_mode'] ?? null,
                'freight_option' => $validated['freight_option'] ?? null,
                'forwarder' => $validated['forwarder'] ?? $validated['service_carrier'] ?? null,
                'service_carrier' => $validated['service_carrier'] ?? null,
                'service_remarks' => $validated['service_remarks'] ?? null,
                'cargo_items' => !empty($cargoItems) ? json_encode($cargoItems) : null,
                'estimated_freight' => $estimatedFreight,
                'valuation_percentage' => $valuationPercent,
                'valuation_charge' => $valuationCharge,
                'handling_percentage' => $handlingPercent,
                'handling_fee' => $handlingFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('production.logistic.freight-quotation')
                ->with('success', 'Freight quotation #' . $validated['quote_number'] . ' created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating freight quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating freight quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display pending freight quotations from marketing
     */
    public function pendingFreightQuotations(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = \App\Models\FreightQuotation::with(['createdBy', 'respondedBy']);

        if ($status === 'pending') {
            $query->whereIn('workflow_status', ['draft', 'pending_logistics'])
                ->whereNull('responded_by');
        } elseif ($status === 'responded') {
            $query->whereIn('workflow_status', ['approved', 'linked_to_so'])
                ->whereNotNull('responded_by');
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('quote_number', 'like', '%' . $search . '%')
                  ->orWhere('origin_province', 'like', '%' . $search . '%')
                  ->orWhere('destination_province', 'like', '%' . $search . '%')
                  ->orWhere('service_mode', 'like', '%' . $search . '%')
                  ->orWhere('customer_representative', 'like', '%' . $search . '%')
                  ->orWhereHas('createdBy', function($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $quotations = $query->latest()->paginate(10)->withQueryString();

        return view('production.logistic.pending-freight-quotations', [
            'title' => 'Pending Freight Quotations',
            'sidebar' => 'production',
            'quotations' => $quotations,
            'currentStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Show freight quotation details for logistics to review
     */
    public function showFreightQuotation(\App\Models\FreightQuotation $freightQuotation)
    {
        return view('production.logistic.freight-quotation-show', [
            'title' => 'Freight Quotation Review',
            'sidebar' => 'production',
            'quotation' => $freightQuotation->load(['createdBy', 'salesOrder' => function($query) {
                $query->with(['items.book', 'items.bookIndex', 'items.product', 'items.bundle', 'customer']);
            }]),
        ]);
    }

    /**
     * Approve and add pricing to freight quotation
     */
    public function approveFreightQuotation(Request $request, \App\Models\FreightQuotation $freightQuotation)
    {
        try {

            $validated = $request->validate([
                'estimated_freight' => 'nullable|numeric|min:0',
                'valuation_percentage' => 'nullable|numeric|min:0|max:100',
                'handling_percentage' => 'nullable|numeric|min:0|max:100',
                'boxes_count' => 'nullable|integer|min:0',
                'logistics_notes' => 'nullable|string',
            ], [
                'estimated_freight.min' => 'Estimated freight cannot be negative',
                'boxes_count.min' => 'Number of boxes cannot be negative',
            ]);

            // Calculate charges
            $estimatedFreight = (float) ($validated['estimated_freight'] ?? 0);
            $boxesCount = !empty($validated['boxes_count']) ? (int)$validated['boxes_count'] : 0;
            $handlingPercent = 0;
            $valuationCharge = 0;
            $handlingFee = 0;
            $totalAmount = $estimatedFreight;

            // Update quotation with logistics response
            $freightQuotation->update([
                'estimated_freight' => $estimatedFreight,
                'valuation_percentage' => $valuationPercent,
                'valuation_charge' => $valuationCharge,
                'handling_percentage' => $handlingPercent,
                'handling_fee' => $handlingFee,
                'total_amount' => $totalAmount,
                'boxes_count' => $boxesCount,
                'logistics_notes' => $validated['logistics_notes'] ?? null,
                'status' => 'approved',
                'workflow_status' => 'approved',
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ]);

            // Update linked Sales Order with freight charges
            if ($freightQuotation->sales_order_id) {
                $salesOrder = \App\Models\SalesOrder::find($freightQuotation->sales_order_id);
                if ($salesOrder) {
                    $salesOrder->update([
                        'freight_charges' => $totalAmount,
                        'freight_option' => $freightQuotation->freight_option,
                        'forwarder' => $freightQuotation->forwarder,
                        'freight_notes' => 'Freight approved: ' . number_format($estimatedFreight, 2) . 
                                         ' (Handling: ₱' . number_format($handlingFee, 2) . ')',
                    ]);
                    $itemsSubtotal = $salesOrder->items()->sum('subtotal');
                    $serviceFee = $freightQuotation->freight_option === 'freight_collect' ? 50.00 : 0;
                    $salesOrder->update([
                        'total_amount' => $itemsSubtotal + $totalAmount + $serviceFee,
                    ]);
                }
            }

            return redirect()->route('production.logistic.pending-freight-quotations')
                ->with('success', 'Freight quotation #' . $freightQuotation->quote_number . ' approved! Linked Sales Order updated with freight charges.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error approving freight quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject freight quotation
     */
    public function rejectFreightQuotation(Request $request, \App\Models\FreightQuotation $freightQuotation)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ], [
                'rejection_reason.required' => 'Reason for rejection is required',
                'rejection_reason.min' => 'Reason must be at least 10 characters',
            ]);

            $freightQuotation->update([
                'workflow_status' => 'draft',
                'logistics_notes' => 'Rejected: ' . $validated['rejection_reason'],
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ]);

            return redirect()->route('production.logistic.pending-freight-quotations')
                ->with('warning', 'Freight quotation #' . $freightQuotation->quote_number . ' has been rejected and returned to marketing');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error rejecting freight quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update cargo items for freight quotation
     */
    public function updateCargoItems(Request $request, \App\Models\FreightQuotation $freightQuotation)
    {
        try {
            // Get cargo item data from request
            $cargoQty = $request->input('cargo_qty', []);
            $cargoPackageType = $request->input('cargo_package_type', []);
            $cargoDimensions = $request->input('cargo_dimensions', []);
            $cargoGrossWeight = $request->input('cargo_gross_weight', []);
            $cargoVolWeight = $request->input('cargo_vol_weight', []);

            // Build cargo items array
            $cargoItems = [];
            $itemCount = count($cargoQty);

            for ($i = 0; $i < $itemCount; $i++) {
                // Skip empty rows
                if (empty($cargoQty[$i]) && empty($cargoPackageType[$i])) {
                    continue;
                }

                $cargoItems[] = [
                    'qty' => $cargoQty[$i] ?? 0,
                    'package_type' => $cargoPackageType[$i] ?? '',
                    'dimensions' => $cargoDimensions[$i] ?? '',
                    'gross_weight' => (float) ($cargoGrossWeight[$i] ?? 0),
                    'vol_weight' => (float) ($cargoVolWeight[$i] ?? 0),
                ];
            }

            $updateData = [
                'cargo_items' => json_encode($cargoItems),
            ];

            if ($request->has('boxes_count') && $request->input('boxes_count') !== null && $request->input('boxes_count') !== '') {
                $updateData['boxes_count'] = (int) $request->input('boxes_count');
            }

            if ($request->has('estimated_freight') && $request->input('estimated_freight') !== null && $request->input('estimated_freight') !== '') {
                $estimatedFreight = (float) $request->input('estimated_freight');
                $handlingPercent = 0;
                $valuationCharge = 0;
                $handlingFee = 0;
                $totalAmount = $estimatedFreight;

                $updateData['estimated_freight'] = $estimatedFreight;
                $updateData['valuation_charge'] = $valuationCharge;
                $updateData['handling_fee'] = $handlingFee;
                $updateData['total_amount'] = $totalAmount;

                if ($freightQuotation->sales_order_id) {
                    $salesOrder = \App\Models\SalesOrder::find($freightQuotation->sales_order_id);
                    if ($salesOrder) {
                        $salesOrder->update([
                            'freight_charges' => $totalAmount,
                        ]);
                        $itemsSubtotal = $salesOrder->items()->sum('subtotal');
                        $serviceFee = $freightQuotation->freight_option === 'freight_collect' ? 50.00 : 0;
                        $salesOrder->update([
                            'total_amount' => $itemsSubtotal + $totalAmount + $serviceFee,
                        ]);
                    }
                }
            }

            if ($request->has('logistics_notes') && filled($request->input('logistics_notes'))) {
                $updateData['logistics_notes'] = $request->input('logistics_notes');
            }

            // Update quotation with cargo items and freight charges
            $freightQuotation->update($updateData);

            return redirect()->back()
                ->with('success', 'Cargo items updated successfully! ' . count($cargoItems) . ' item(s) saved.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating cargo items: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating cargo items: ' . $e->getMessage());
        }
    }

    /**
     * Link Area Consignment to Sales Invoice
     * Creates SI from selected items in DR
     */
    public function linkConsignmentToSI(Request $request, $orderId)
    {
        try {
            // Get the Sales Order with all relationships
            $order = \App\Models\SalesOrder::with(['items.book', 'customer'])
                ->findOrFail($orderId);

            // Validate that this is an area consignment order
            if (!in_array($order->type, ['area_consignment', 'area_sales_consignment'])) {
                return redirect()->back()
                    ->with('error', 'This order is not an Area Consignment type.');
            }

            // Check for required proof of payment
            if (!$request->hasFile('proof_of_payment')) {
                return redirect()->back()
                    ->with('error', 'Proof of Payment file is required to link this consignment to a Sales Invoice.');
            }

            $proofOfPaymentPath = $request->file('proof_of_payment')->store('sales_orders/proof_of_payments', 'public');

            // Get selected items and quantities
            $selectedItems = $request->input('items', []);
            
            if (empty($selectedItems)) {
                return redirect()->back()
                    ->with('error', 'Please select at least one item.');
            }

            // Calculate totals for SI
            $totalAmount = 0;
            $siItems = [];
            
            foreach ($selectedItems as $itemId => $itemData) {
                $selectedQty = intval($itemData['selected_qty'] ?? 0);
                
                if ($selectedQty <= 0) {
                    continue;
                }

                $soItem = $order->items()->find($itemId);
                if (!$soItem) {
                    continue;
                }

                $itemAmount = $selectedQty * $soItem->price;
                $totalAmount += $itemAmount;

                $siItems[] = [
                    'so_item_id' => $itemId,
                    'book_id' => $soItem->book_id,
                    'quantity' => $selectedQty,
                    'unit_price' => $soItem->price,
                    'amount' => $itemAmount
                ];
            }

            if (empty($siItems)) {
                return redirect()->back()
                    ->with('error', 'No items selected. Please select at least one item.');
            }

            // Create Sales Invoice
            $si = new \App\Models\SalesInvoice();
            $si->so_id = $order->id;
            $si->so_number = $order->so_number;
            $si->si_number = 'SI-' . $order->so_number . '-' . time();
            $si->customer_id = $order->customer_id;
            $si->customer_name = $order->customer->customer_name ?? '';
            $si->transaction_type = 'area_consignment_si';
            $si->total_amount = $totalAmount;
            $si->status = 'draft';
            $si->created_by = auth()->id();
            $si->created_at = now();
            $si->save();

            // Create SI Items
            foreach ($siItems as $item) {
                \App\Models\SalesInvoiceItem::create([
                    'si_id' => $si->id,
                    'so_item_id' => $item['so_item_id'],
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['amount'],
                    'created_at' => now()
                ]);
            }

            // Update Sales Order status, store consignment data and proof of payment
            $order->update([
                'status' => 'si_created',
                'proof_of_payment' => $proofOfPaymentPath,
                'consignment_data' => json_encode([
                    'si_id' => $si->id,
                    'si_number' => $si->si_number,
                    'selected_items' => $selectedItems,
                    'selected_at' => now(),
                    'selected_by' => auth()->id()
                ])
            ]);

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Area Consignment SI Created',
                'description' => "Sales Invoice {$si->si_number} created from Area Consignment SO {$order->so_number}. Total: " . number_format($totalAmount, 2),
                'affected_model' => 'SalesOrder',
                'affected_model_id' => $order->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return redirect()
                ->route('admin-finance.accounting.sales-invoice')
                ->with('success', "Sales Invoice {$si->si_number} created successfully with " . count($siItems) . " item(s). Total: " . number_format($totalAmount, 2));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error linking consignment to SI: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating Sales Invoice: ' . $e->getMessage());
        }
    }

    /**
     * Returns remaining consignment items to warehouse stock and completes the sales order and delivery receipt.
     */
    public function returnConsignment(Request $request, $orderId)
    {
        try {
            $order = \App\Models\SalesOrder::with(['items.book', 'customer'])
                ->findOrFail($orderId);

            if (!in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment'])) {
                return redirect()->back()
                    ->with('error', 'This order is not an Area Consignment type.');
            }

            // Find all remaining quantities
            $returnedBooks = [];
            $returnedCount = 0;

            foreach ($order->items as $item) {
                $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($order) {
                    $query->where('so_id', $order->id)->where('status', '!=', 'cancelled');
                })->where('book_id', $item->book_id)->sum('quantity');

                $remainingQty = max(0, $item->quantity - $alreadyPurchasedQty);

                if ($remainingQty > 0) {
                    $book = $item->book;
                    if ($book) {
                        $book->stock += $remainingQty;
                        $book->save();

                        $returnedCount += $remainingQty;
                        $returnedBooks[] = [
                            'book_name' => $book->name,
                            'quantity' => $remainingQty
                        ];

                        // Record Inventory Transaction
                        \App\Models\InventoryTransaction::create([
                            'book_id' => $book->id,
                            'type' => 'in',
                            'quantity' => $remainingQty,
                            'location' => 'Main Warehouse',
                            'source' => 'Consignment Return',
                            'reference_number' => $order->so_number,
                            'unit_cost' => $book->cost ?? 0,
                            'total_cost' => $remainingQty * ($book->cost ?? 0),
                            'notes' => 'Returned from Area Consignment Sales Order #' . $order->so_number . ' via Direct Return button',
                            'status' => 'completed',
                            'transaction_date' => now(),
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }

            if ($returnedCount === 0) {
                return redirect()->back()
                    ->with('error', 'No remaining items found to return.');
            }

            // Start Database Transaction
            \DB::beginTransaction();

            // Find the previous Delivery Receipt and close it
            $previousDr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->first();
            if ($previousDr) {
                $previousDr->update(['status' => 'completed']);
            }

            // Close the Sales Order
            $order->update(['status' => 'completed']);

            \DB::commit();

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Area Consignment Return Completed',
                'description' => "Returned {$returnedCount} remaining book(s) from Area Consignment SO {$order->so_number} back to warehouse stock.",
                'affected_model' => 'SalesOrder',
                'affected_model_id' => $order->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return redirect()
                ->route('production.logistic.delivery-receipt-list')
                ->with('success', "Returned {$returnedCount} remaining book(s) to stock and closed Sales Order {$order->so_number}.");

        } catch (\Exception $e) {
            \DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error returning consignment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error returning consignment items: ' . $e->getMessage());
        }
    }

    /**
     * Fast-track Move Sales Order directly to Sales Invoice queue
     * For area consignment: uses customer_selected_qty as the invoiced qty
     * Sets status to si_created so the SO stays in DR list and can still be reconsigned
     */
    public function fastMoveToSI(Request $request, $id)
    {
        try {
            $order = \App\Models\SalesOrder::with(['items.book', 'customer'])->findOrFail($id);

            // Build SI items using picked qty (customer_selected_qty).
            // Skip items with zero pick qty.
            $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']);
            $siItems = [];
            $totalAmount = 0;

            foreach ($order->items as $item) {
                $selected = (int)($item->customer_selected_qty ?? 0);
                $qty = $selected > 0 ? $selected : (int)($item->quantity ?? 0);

                if ($qty <= 0) continue;

                $unitPrice = $item->price ?? 0;
                $amount = $qty * $unitPrice;
                $totalAmount += $amount;

                $siItems[] = [
                    'so_item_id' => $item->id,
                    'book_id'    => $item->book_id,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'amount'     => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($siItems)) {
                return redirect()->back()->with('error', 'No items with a pick quantity selected. Please save pick quantities first before moving to SI.');
            }

            // Use si_created status: keeps the SO visible in DR list and allows reconsignment
            $order->update([
                'status'               => 'si_created',
                'si_prepared_by'       => auth()->id(),
                'si_prepared_at'       => now(),
                'signed_by_af_manager' => null,
                'signed_at'            => null,
                'remarks'              => ($order->remarks ? $order->remarks . ' | ' : '') . 'Moved to SI by ' . auth()->user()->name
            ]);

            // Create or re-use Sales Invoice record
            $si = \App\Models\SalesInvoice::firstOrCreate(
                ['so_id' => $order->id],
                [
                    'si_number'        => 'SI-' . $order->so_number,
                    'customer_id'      => $order->customer_id,
                    'customer_name'    => $order->customer->customer_name ?? 'N/A',
                    'total_amount'     => $totalAmount,
                    'transaction_type' => $order->type . '_si',
                    'status'           => 'pending_approval',
                    'created_by'       => auth()->id()
                ]
            );

            // Update totals and status in case the record already existed
            $si->update([
                'total_amount'   => $totalAmount,
                'status'         => 'pending_approval',
                'payment_method' => $order->payment_method ?? 'cash'
            ]);

            // Remove any previously incorrect items and re-create from picked qty
            $si->items()->delete();
            foreach ($siItems as $row) {
                \App\Models\SalesInvoiceItem::create(array_merge(['si_id' => $si->id], $row));
            }

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id'            => auth()->id(),
                'action'             => 'Moved to Sales Invoice',
                'description'        => "Sales Order {$order->so_number} moved to SI. " . count($siItems) . " item(s), total ₱" . number_format($totalAmount, 2) . ".",
                'affected_model'     => 'SalesOrder',
                'affected_model_id'  => $order->id,
                'ip_address'         => $request->ip(),
                'user_agent'         => $request->header('User-Agent')
            ]);

            return redirect()
                ->back()
                ->with('success', 'Sales Order #' . $order->so_number . ' moved to Sales Invoice successfully! ' . count($siItems) . ' item(s) invoiced.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error moving order to SI: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error moving order to SI: ' . $e->getMessage());
        }
    }

    public function completeTeamStockPickList(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::with('items')->findOrFail($id);
        
        \DB::beginTransaction();
        try {
            if ($request->has('notes')) {
                $transfer->notes = $request->notes;
                $transfer->save();
            }

            // Sync any submitted item quantities directly from the complete button click
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    if (isset($itemData['id'])) {
                        $tItem = \App\Models\TeamStockTransferItem::where('team_stock_transfer_id', $transfer->id)
                            ->where('id', $itemData['id'])
                            ->first();
                        if ($tItem) {
                            if (isset($itemData['picked_qty'])) {
                                $tItem->picked_qty = floatval($itemData['picked_qty']);
                            }
                            if (isset($itemData['status'])) {
                                $tItem->status = $itemData['status'];
                            }
                            if (isset($itemData['notes'])) {
                                $tItem->notes = $itemData['notes'];
                            }
                            if (isset($itemData['picked_date'])) {
                                $tItem->picked_date = $itemData['picked_date'];
                            }
                            $tItem->save();
                        }
                    }
                }
            }

            // Reload transfer items to get updated quantities
            $transfer->load('items');

            // Find or resolve target Site for the team
            $teamName = trim($transfer->team_name);
            $targetSite = \App\Models\Site::where('name', $teamName)
                ->orWhere('name', 'Site ' . $teamName)
                ->orWhere('code', strtolower(str_replace([' ', '-'], '_', $teamName)))
                ->orWhereRaw('LOWER(name) = ?', [strtolower($teamName)])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($teamName) . '%'])
                ->first();

            if (!$targetSite) {
                $targetSite = \App\Models\Site::create([
                    'name' => $teamName,
                    'code' => strtolower(str_replace([' ', '-'], '_', $teamName)),
                    'location' => 'Area Sales',
                    'description' => 'Area Sales ' . $teamName . ' Inventory',
                    'is_active' => true
                ]);
            }

            foreach ($transfer->items as $tItem) {
                // If picked_qty was submitted or saved (> 0), use it; otherwise fallback to item quantity unless status is explicitly Unpicked
                //if ($tItem->picked_qty !== null && (float)$tItem->picked_qty > 0) {
                //    $qty = (float)$tItem->picked_qty;
                //} elseif ($tItem->status !== 'Unpicked' && (float)$tItem->quantity > 0) {
                //    $qty = (float)$tItem->quantity;
               // } else {
               //     $qty = 0;
               // }
              
              // Always use the actual saved picked quantity.
// 0 is a valid picked quantity and must NOT fall back to requested quantity.
$qty = $tItem->picked_qty !== null
    ? (float) $tItem->picked_qty
    : 0;

                // Update item picked_qty to actual picked_qty so only picked quantity proceeds to packing
                $tItem->picked_qty = $qty;
                if ($qty > 0) {
                    $tItem->status = 'Picked';
                } else {
                    $tItem->status = 'Unpicked';
                }
                $tItem->save();

                if ($qty <= 0) {
                    continue;
                }

                // 1. Deduct Main Warehouse Stock & Sync SiteInventory
                $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
                $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                if ($tItem->book_index_id) {
                    $index = \App\Models\BookIndex::find($tItem->book_index_id);
                    if ($index) {
                        $index->stock = max(0, ($index->stock ?? $index->quantity ?? 0) - $qty);
                        $index->save();

                        \App\Models\SiteInventory::updateOrCreate(
                            ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                            ['quantity' => $index->stock]
                        );
                    }
                } elseif ($tItem->book_id) {
                    $book = \App\Models\Book::find($tItem->book_id);
                    if ($book) {
                        $book->stock = max(0, ($book->stock ?? 0) - $qty);
                        $book->save(); // BookObserver automatically syncs Main Warehouse SiteInventory to $book->stock
                    }
                } elseif ($tItem->book_bundle_id) {
                    $bundle = \App\Models\BookBundle::find($tItem->book_bundle_id);
                    if ($bundle) {
                        $bundle->stock = max(0, ($bundle->stock ?? $bundle->quantity ?? 0) - $qty);
                        $bundle->save();

                        \App\Models\SiteInventory::updateOrCreate(
                            ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                            ['quantity' => $bundle->stock]
                        );
                    }
                }

                // 2. Record Inventory Transaction Audit Trail (Out from Main Warehouse)
                if ($tItem->book_id) {
                    $bookForCost = \App\Models\Book::find($tItem->book_id);
                    \App\Models\InventoryTransaction::create([
                        'book_id'          => $tItem->book_id,
                        'type'             => 'out',
                        'quantity'         => $qty,
                        'location'         => 'Main Warehouse',
                        'source'           => 'Team Stock Transfer',
                        'reference_number' => $transfer->transfer_number,
                        'unit_cost'        => $bookForCost->cost ?? 0,
                        'total_cost'       => $qty * ($bookForCost->cost ?? 0),
                        'notes'            => 'Team Stock Transfer to ' . $transfer->team_name,
                        'status'           => 'completed',
                        'transaction_date' => now(),
                        'user_id'          => auth()->id() ?? 1,
                    ]);
                }
            }

            $transfer->update(['status' => 'packing']);
            \DB::commit();

            return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' pick list completed! Items forwarded to Packing Management.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete team stock pick list: ' . $e->getMessage());
        }
    }

    public function completeTeamStockPacking(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::with('items')->findOrFail($id);

        \DB::beginTransaction();
        try {
            if ($request->has('notes')) {
                $transfer->notes = $request->notes;
                $transfer->save();
            }

            // Sync any submitted item quantities directly from the complete button click
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    if (isset($itemData['id'])) {
                        $tItem = \App\Models\TeamStockTransferItem::where('team_stock_transfer_id', $transfer->id)
                            ->where('id', $itemData['id'])
                            ->first();
                        if ($tItem) {
                            if (isset($itemData['packed_qty'])) {
                                $tItem->packed_qty = floatval($itemData['packed_qty']);
                            }
                            if (isset($itemData['status'])) {
                                $tItem->status = $itemData['status'];
                            }
                            if (isset($itemData['notes'])) {
                                $tItem->notes = $itemData['notes'];
                            }
                            if (isset($itemData['packed_date'])) {
                                $tItem->packed_date = $itemData['packed_date'];
                            }
                            $tItem->save();
                        }
                    }
                }
            }

            // Reload transfer items to get updated quantities
            $transfer->load('items');

            $this->creditTeamStockTransfer($transfer);

            $transfer->update(['status' => 'completed']);
            \DB::commit();

            return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' packing completed! Stock successfully credited to ' . $transfer->team_name . ' site inventory.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete team stock packing: ' . $e->getMessage());
        }
    }

    protected function creditTeamStockTransfer(\App\Models\TeamStockTransfer $transfer)
    {
        // Find or resolve target Site for the team
        $teamName = trim($transfer->team_name);
        $targetSite = \App\Models\Site::where('name', $teamName)
            ->orWhere('name', 'Site ' . $teamName)
            ->orWhere('code', strtolower(str_replace([' ', '-'], '_', $teamName)))
            ->orWhereRaw('LOWER(name) = ?', [strtolower($teamName)])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($teamName) . '%'])
            ->first();

        if (!$targetSite) {
            $targetSite = \App\Models\Site::create([
                'name' => $teamName,
                'code' => strtolower(str_replace([' ', '-'], '_', $teamName)),
                'location' => 'Area Sales',
                'description' => 'Area Sales ' . $teamName . ' Inventory',
                'is_active' => true
            ]);
        }

        $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
        $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

        // Check if this transfer was already credited by checking InventoryTransaction
        $alreadyCredited = \App\Models\InventoryTransaction::where('reference_number', $transfer->transfer_number)
            ->where('type', 'in')
            ->where('source', 'Team Stock Transfer')
            ->exists();

        foreach ($transfer->items as $tItem) {
               // Preserve actual picked quantity, including explicit 0.
    $previouslyPicked = $tItem->picked_qty !== null
        ? (float) $tItem->picked_qty
        : 0;

    // Preserve actual packed quantity, including explicit 0.
    // Never fall back to requested quantity.
    $qty = $tItem->packed_qty !== null
        ? (float) $tItem->packed_qty
        : $previouslyPicked;

    // Packing can never exceed what was actually picked.
    $qty = min($qty, $previouslyPicked);
    $qty = max(0, $qty);
          
          

            // If picked quantity was higher than actual packed quantity, restore the difference back to Main Warehouse
            if ($previouslyPicked > $qty) {
                $unpackedDiff = $previouslyPicked - $qty;

                if ($tItem->book_index_id) {
                    $index = \App\Models\BookIndex::find($tItem->book_index_id);
                    if ($index) {
                        $index->stock = ($index->stock ?? $index->quantity ?? 0) + $unpackedDiff;
                        $index->save();
                        \App\Models\SiteInventory::updateOrCreate(
                            ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                            ['quantity' => $index->stock]
                        );
                    }
                } elseif ($tItem->book_id) {
                    $book = \App\Models\Book::find($tItem->book_id);
                    if ($book) {
                        $book->stock = ($book->stock ?? 0) + $unpackedDiff;
                        $book->save();
                    }
                } elseif ($tItem->book_bundle_id) {
                    $bundle = \App\Models\BookBundle::find($tItem->book_bundle_id);
                    if ($bundle) {
                        $bundle->stock = ($bundle->stock ?? $bundle->quantity ?? 0) + $unpackedDiff;
                        $bundle->save();
                        \App\Models\SiteInventory::updateOrCreate(
                            ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                            ['quantity' => $bundle->stock]
                        );
                    }
                }

                if ($tItem->book_id) {
                    $bookForCost = \App\Models\Book::find($tItem->book_id);
                    \App\Models\InventoryTransaction::create([
                        'book_id'          => $tItem->book_id,
                        'type'             => 'in',
                        'quantity'         => $unpackedDiff,
                        'location'         => 'Main Warehouse',
                        'source'           => 'Unpacked Stock Return',
                        'reference_number' => $transfer->transfer_number,
                        'unit_cost'        => $bookForCost->cost ?? 0,
                        'total_cost'       => $unpackedDiff * ($bookForCost->cost ?? 0),
                        'notes'            => 'Restored ' . $unpackedDiff . ' unpacked pcs from transfer ' . $transfer->transfer_number . ' to Main Warehouse',
                        'status'           => 'completed',
                        'transaction_date' => now(),
                        'user_id'          => auth()->id() ?? 1,
                    ]);
                }
            }
            
           $tItem->packed_qty = $qty;

if ($qty > 0) {
    $tItem->status = 'Packed';
} else {
    $tItem->status = 'Not Packed';
}

$tItem->save();

            if ($qty <= 0 || $alreadyCredited) {
                continue;
            }

            // 1. Credit Team Stock balance
            $teamStock = \App\Models\TeamStock::firstOrNew([
                'team_name' => $transfer->team_name,
                'book_id' => $tItem->book_id,
                'book_index_id' => $tItem->book_index_id,
                'book_bundle_id' => $tItem->book_bundle_id,
            ]);
            $teamStock->quantity = ($teamStock->quantity ?? 0) + $qty;
            $teamStock->save();

            // 2. Sync Target Team SiteInventory
            $siteInv = \App\Models\SiteInventory::firstOrNew([
                'site_id' => $targetSite->id,
                'book_id' => $tItem->book_id,
                'book_index_id' => $tItem->book_index_id,
                'book_bundle_id' => $tItem->book_bundle_id,
            ]);
            $siteInv->quantity = ($siteInv->quantity ?? 0) + $qty;
            $siteInv->save();

            // 3. Record Inventory Transaction Audit Trail (In to Target Site)
            if ($tItem->book_id) {
                $bookForCost = \App\Models\Book::find($tItem->book_id);
                \App\Models\InventoryTransaction::create([
                    'book_id'          => $tItem->book_id,
                    'type'             => 'in',
                    'quantity'         => $qty,
                    'location'         => $targetSite->name,
                    'source'           => 'Team Stock Transfer',
                    'reference_number' => $transfer->transfer_number,
                    'unit_cost'        => $bookForCost->cost ?? 0,
                    'total_cost'       => $qty * ($bookForCost->cost ?? 0),
                    'notes'            => 'Stock received by ' . $transfer->team_name . ' from Main Warehouse',
                    'status'           => 'completed',
                    'transaction_date' => now(),
                    'user_id'          => auth()->id() ?? 1,
                ]);
            }
        }
    }

    public function saveTeamStockPickItems(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::findOrFail($id);

        \DB::beginTransaction();
        try {
            if ($request->has('notes')) {
                $transfer->notes = $request->notes;
            }
            // Only allow safe status transitions from the save-pick-items form.
            // Completion should only happen via the dedicated completeTeamStockPickList endpoint.
// Save Picked Items = draft/work-in-progress only.
// Never complete the transfer or move it to Packing from this button.
$transfer->status = 'picking';
$transfer->save();

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    if (isset($itemData['id'])) {
                        $tItem = \App\Models\TeamStockTransferItem::where('team_stock_transfer_id', $transfer->id)
                            ->where('id', $itemData['id'])
                            ->first();
                        if ($tItem) {
                            $pickedQty = isset($itemData['picked_qty']) ? floatval($itemData['picked_qty']) : $tItem->picked_qty;
                            $tItem->picked_qty = $pickedQty;
                            
                            // Do not modify $tItem->quantity; quantity represents original requested quantity to pick
                            if (isset($itemData['status']) && !empty($itemData['status'])) {
                                $tItem->status = $itemData['status'];
                            } elseif ($pickedQty >= $tItem->quantity && $tItem->quantity > 0) {
                                $tItem->status = 'Picked';
                            } elseif ($pickedQty > 0) {
                                $tItem->status = 'Picking';
                            } else {
                                $tItem->status = 'Pending';
                            }

                            $tItem->notes = $itemData['notes'] ?? $tItem->notes;
                            $tItem->picked_date = $itemData['picked_date'] ?? $tItem->picked_date;
                            $tItem->save();
                        }
                    }
                }
            }

            \DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Team Stock Transfer pick list saved successfully!']);
            }

            return redirect()->back()->with('success', 'Pick list items for Team Stock Transfer #' . $transfer->transfer_number . ' updated successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to save pick list items: ' . $e->getMessage());
        }
    }

    public function saveTeamStockPackItems(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::with('items')->findOrFail($id);

        \DB::beginTransaction();
        try {
            if ($request->has('notes')) {
                $transfer->notes = $request->notes;
            }

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    if (isset($itemData['id'])) {
                        $tItem = \App\Models\TeamStockTransferItem::where('team_stock_transfer_id', $transfer->id)
                            ->where('id', $itemData['id'])
                            ->first();
                        if ($tItem) {
                            $packedQty = isset($itemData['packed_qty']) ? floatval($itemData['packed_qty']) : $tItem->packed_qty;
                            $tItem->packed_qty = $packedQty;

                            // Do not modify $tItem->quantity; quantity represents original requested quantity
                           // $targetQty = (float)($tItem->picked_qty !== null && (float)$tItem->picked_qty > 0 ? $tItem->picked_qty : ($tItem->status !== 'Unpicked' ? $tItem->quantity : 0));
                          
                          $targetQty = (float)(
    $tItem->picked_qty !== null
        ? $tItem->picked_qty
        : $tItem->quantity
);
                          
                            if (isset($itemData['status']) && !empty($itemData['status'])) {
                                $tItem->status = $itemData['status'];
                            } elseif ($packedQty >= $targetQty && $targetQty > 0) {
                                $tItem->status = 'Packed';
                            } elseif ($packedQty > 0) {
                                $tItem->status = 'In Progress';
                            } else {
                                $tItem->status = 'Not Packed';
                            }

                            $tItem->notes = $itemData['notes'] ?? $tItem->notes;
                            $tItem->packed_date = $itemData['packed_date'] ?? $tItem->packed_date;
                            $tItem->save();
                        }
                    }
                }
            }

            // Reload transfer items to get updated quantities
// Reload transfer items to get updated quantities
$transfer->load('items');

// IMPORTANT:
// "Save Packing" is only a draft/save action.
// It must NEVER complete the Team Stock Transfer
// and must NEVER credit stock to the destination team.
//
// Only completeTeamStockPack() / complete-pack route
// is allowed to finalize the transfer.
if ($transfer->status !== 'completed') {
    $transfer->status = 'packing';
}

$transfer->save();

            \DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Team Stock Transfer packing details saved successfully!']);
            }

            return redirect()->back()->with('success', 'Packing items for Team Stock Transfer #' . $transfer->transfer_number . ' updated successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to save packing items: ' . $e->getMessage());
        }
    }

    public function deleteTeamStockTransfer($id)
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admin can delete team stock transfers.');
        }

        \DB::beginTransaction();
        try {
            $transfer = \App\Models\TeamStockTransfer::with('items')->findOrFail($id);

            // Restore stock if it was already picked or completed
            if (in_array($transfer->status, ['packing', 'completed'])) {
                foreach ($transfer->items as $tItem) {
                    $qty = $tItem->quantity;

                    // 1. Restore Main Warehouse Stock & Sync SiteInventory
                    $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
                    $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                    if ($tItem->book_index_id) {
                        $index = \App\Models\BookIndex::find($tItem->book_index_id);
                        if ($index) {
                            $index->stock = ($index->stock ?? 0) + $qty;
                            $index->save();

                            \App\Models\SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                                ['quantity' => $index->stock]
                            );
                        }
                    } elseif ($tItem->book_id) {
                        $book = \App\Models\Book::find($tItem->book_id);
                        if ($book) {
                            $book->stock = ($book->stock ?? 0) + $qty;
                            $book->save(); // BookObserver automatically syncs Main Warehouse SiteInventory to $book->stock
                        }
                    } elseif ($tItem->book_bundle_id) {
                        $bundle = \App\Models\BookBundle::find($tItem->book_bundle_id);
                        if ($bundle) {
                            $bundle->stock = ($bundle->stock ?? 0) + $qty;
                            $bundle->save();

                            \App\Models\SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                                ['quantity' => $bundle->stock]
                            );
                        }
                    }

                    // 3. If picked or completed, deduct credited Team Stock & Target SiteInventory
                    if (in_array($transfer->status, ['packing', 'completed'])) {
                        $teamStock = \App\Models\TeamStock::where([
                            'team_name' => $transfer->team_name,
                            'book_id' => $tItem->book_id,
                            'book_index_id' => $tItem->book_index_id,
                            'book_bundle_id' => $tItem->book_bundle_id,
                        ])->first();
                        if ($teamStock) {
                            $teamStock->quantity = max(0, $teamStock->quantity - $qty);
                            $teamStock->save();
                        }

                        $targetSite = \App\Models\Site::where('name', $transfer->team_name)->first();
                        if ($targetSite) {
                            $siteInv = \App\Models\SiteInventory::where([
                                'site_id' => $targetSite->id,
                                'book_id' => $tItem->book_id,
                                'book_index_id' => $tItem->book_index_id,
                                'book_bundle_id' => $tItem->book_bundle_id,
                            ])->first();
                            if ($siteInv) {
                                $siteInv->quantity = max(0, $siteInv->quantity - $qty);
                                $siteInv->save();
                            }
                        }
                    }

                    // Audit log stock restoration in InventoryTransaction
                    if ($tItem->book_id) {
                        $bookForCost = \App\Models\Book::find($tItem->book_id);
                        \App\Models\InventoryTransaction::create([
                            'book_id'          => $tItem->book_id,
                            'type'             => 'in',
                            'quantity'         => $qty,
                            'location'         => 'Main Warehouse',
                            'source'           => 'Team Stock Transfer Restoration',
                            'reference_number' => $transfer->transfer_number,
                            'unit_cost'        => $bookForCost->cost ?? 0,
                            'total_cost'       => $qty * ($bookForCost->cost ?? 0),
                            'notes'            => 'Team Stock Transfer ' . $transfer->transfer_number . ' deleted/restored',
                            'status'           => 'completed',
                            'transaction_date' => now(),
                            'user_id'          => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Team stock transfer deleted',
                'description' => 'Team stock transfer ' . $transfer->transfer_number . ' was deleted by Super Admin',
                'reference_type' => 'TeamStockTransfer',
                'reference_id' => $transfer->id,
                'details' => json_encode(['transfer_number' => $transfer->transfer_number])
            ]);

            // Delete items and transfer record
            \App\Models\TeamStockTransferItem::where('team_stock_transfer_id', $transfer->id)->delete();
            $transfer->delete();

            \DB::commit();
            return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting team stock transfer: ' . $e->getMessage());
        }
    }

    public function updateTeamStockTransfer(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admin can edit team stock transfers.');
        }

        $transfer = \App\Models\TeamStockTransfer::with('items')->findOrFail($id);

        \DB::beginTransaction();
        try {
            if ($request->filled('transfer_number')) {
                $transfer->transfer_number = trim($request->transfer_number);
            }
            if ($request->filled('team_name')) {
                $transfer->team_name = trim($request->team_name);
            }
            if ($request->filled('transferred_by')) {
                $transfer->transferred_by = $request->transferred_by;
            }
            if ($request->has('notes')) {
                $transfer->notes = $request->notes;
            }

            $oldStatus = $transfer->status;
            if ($request->filled('status')) {
                $newStatus = $request->status;
                $transfer->status = $newStatus;

                if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                    $this->creditTeamStockTransfer($transfer);
                }
            }

            // Update existing items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemId => $itemData) {
                    $tItem = \App\Models\TeamStockTransferItem::where('team_stock_transfer_id', $transfer->id)
                        ->where('id', $itemId)
                        ->first();

                    if ($tItem) {
                        if (isset($itemData['_delete']) && ($itemData['_delete'] == '1' || $itemData['_delete'] === 'true')) {
                            $tItem->delete();
                            continue;
                        }

                        if (isset($itemData['quantity'])) {
                            $tItem->quantity = floatval($itemData['quantity']);
                        }

                       if (array_key_exists('picked_qty', $itemData)) {
    $picked = ($itemData['picked_qty'] !== '' && $itemData['picked_qty'] !== null)
        ? max(0, floatval($itemData['picked_qty']))
        : null;

    $tItem->picked_qty = $picked;
}

if (array_key_exists('packed_qty', $itemData)) {
    $packed = ($itemData['packed_qty'] !== '' && $itemData['packed_qty'] !== null)
        ? max(0, floatval($itemData['packed_qty']))
        : null;

    $tItem->packed_qty = $packed;
}

                        if (isset($itemData['status']) && !empty($itemData['status'])) {
                            $tItem->status = $itemData['status'];
                        }

                        if (isset($itemData['notes'])) {
                            $tItem->notes = $itemData['notes'];
                        }

                        $tItem->save();
                    }
                }
            }

            // Add new item if requested
            if ($request->filled('new_item_book_index_id') && $request->filled('new_item_qty') && floatval($request->new_item_qty) > 0) {
                $bIndex = \App\Models\BookIndex::find($request->new_item_book_index_id);
                if ($bIndex) {
                    $addedQty = floatval($request->new_item_qty);
                    \App\Models\TeamStockTransferItem::create([
                        'team_stock_transfer_id' => $transfer->id,
                        'book_id' => $bIndex->book_id,
                        'book_index_id' => $bIndex->id,
                        'quantity' => $addedQty,
                        'picked_qty' => $addedQty,
                        'packed_qty' => $addedQty,
                        'status' => 'Packed',
                    ]);
                }
            }

            $transfer->save();
            \DB::commit();

            return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' updated successfully by Super Admin.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update Team Stock Transfer: ' . $e->getMessage());
        }
    }

    public function savePackingRemarks(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $remarks = $request->input('remarks');

            $order = \App\Models\SalesOrder::findOrFail($orderId);
            $order->update(['remarks' => $remarks]);

            return response()->json(['success' => true, 'message' => 'Remarks updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

