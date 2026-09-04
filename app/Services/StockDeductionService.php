<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
use App\Models\SiteInventory;
use App\Models\TeamStock;
use App\Models\ConsignmentInventory;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockDeductionService
{
    /**
     * Deduct stock immediately upon Sales Order creation.
     */
    public static function deductForSalesOrder(SalesOrder $order)
    {
        if ($order->stock_deducted) {
            return;
        }

        DB::beginTransaction();
        try {
            $order->load(['items', 'preparedBy']);
            
            // Determine team/site name (if user/staff is assigned to a sales team or site tag in remarks)
            $userTeam = null;
            if (!empty($order->remarks) && preg_match('/\[SITE:\s*([^\]]+)\]/i', $order->remarks, $m)) {
                $siteTag = trim($m[1]);
                if ($siteTag === 'Book Sale') {
                    $userTeam = 'Book Sale';
                } elseif ($siteTag === 'Main Warehouse') {
                    $userTeam = null;
                }
            }
            if (empty($userTeam) && $order->area_sales_staff_id) {
                $staff = \App\Models\User::find($order->area_sales_staff_id);
                if ($staff && !empty($staff->sales_team)) {
                    $userTeam = trim($staff->sales_team);
                }
            }
            if (empty($userTeam) && $order->preparedBy && !empty($order->preparedBy->sales_team)) {
                $userTeam = trim($order->preparedBy->sales_team);
            }
            if (empty($userTeam) && auth()->check() && !empty(auth()->user()->sales_team)) {
                $userTeam = trim(auth()->user()->sales_team);
            }

            $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']) || str_starts_with($order->so_number, 'SO-NBS-');

            foreach ($order->items as $item) {
                $qty = (int) $item->quantity;
                if ($qty <= 0) continue;

                // 1. Deduct Stock (Team Stock vs Main Warehouse)
                if (!empty($userTeam)) {
                    $ts = TeamStock::firstOrNew([
                        'team_name'      => $userTeam,
                        'book_id'        => $item->book_id,
                        'book_index_id'  => $item->book_index_id,
                        'book_bundle_id' => $item->book_bundle_id,
                    ]);
                    $ts->quantity = max(0, ($ts->quantity ?? 0) - $qty);
                    $ts->save();

                    self::syncTeamSitesInventory();
                } else {
                    // Deduct from Main Warehouse & Sync SiteInventory
                    $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
                    $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                    if ($item->book_index_id) {
                        $index = BookIndex::find($item->book_index_id);
                        if ($index) {
                            $index->stock = max(0, ($index->stock ?? $index->quantity ?? 0) - $qty);
                            $index->save();

                            SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                                ['quantity' => $index->stock]
                            );
                        }
                    } elseif ($item->book_id) {
                        $book = Book::find($item->book_id);
                        if ($book) {
                            $book->stock = max(0, ($book->stock ?? 0) - $qty);
                            $book->save(); // BookObserver automatically syncs Main Warehouse SiteInventory to $book->stock
                        }
                    } elseif ($item->book_bundle_id) {
                        $bundle = BookBundle::find($item->book_bundle_id);
                        if ($bundle) {
                            $bundle->stock = max(0, ($bundle->stock ?? $bundle->quantity ?? 0) - $qty);
                            $bundle->save();

                            SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                                ['quantity' => $bundle->stock]
                            );
                        }
                    }
                }

                // 2. Track in Consignment Inventory if consignment order type
                if ($isConsignment) {
                    ConsignmentInventory::create([
                        'sales_order_id' => $order->id,
                        'customer_id'    => $order->customer_id,
                        'team_name'      => $userTeam ?: 'Main Warehouse',
                        'book_id'        => $item->book_id,
                        'book_index_id'  => $item->book_index_id,
                        'book_bundle_id' => $item->book_bundle_id,
                        'quantity'       => $qty,
                        'status'         => 'consigned',
                        'notes'          => 'Consigned via Sales Order #' . $order->so_number,
                    ]);
                }
            }

            $order->update(['stock_deducted' => true]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to deduct stock for Sales Order #' . $order->so_number . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restore stock when a Sales Order is rejected or cancelled.
     */
    public static function restoreForSalesOrder(SalesOrder $order, string $reason = '')
    {
        if (!$order->stock_deducted) {
            return;
        }

        DB::beginTransaction();
        try {
            $order->load(['items', 'preparedBy']);

            // Determine team name (if user/staff is assigned to a sales team)
            $userTeam = null;
            if ($order->area_sales_staff_id) {
                $staff = \App\Models\User::find($order->area_sales_staff_id);
                if ($staff && !empty($staff->sales_team)) {
                    $userTeam = trim($staff->sales_team);
                }
            }
            if (empty($userTeam) && $order->preparedBy && !empty($order->preparedBy->sales_team)) {
                $userTeam = trim($order->preparedBy->sales_team);
            }
            if (empty($userTeam) && auth()->check() && !empty(auth()->user()->sales_team)) {
                $userTeam = trim(auth()->user()->sales_team);
            }

            foreach ($order->items as $item) {
                $qty = (int) $item->quantity;
                if ($qty <= 0) continue;

                // 1. Restore Stock (Team Stock vs Main Warehouse)
                if (!empty($userTeam)) {
                    $ts = TeamStock::firstOrNew([
                        'team_name'      => $userTeam,
                        'book_id'        => $item->book_id,
                        'book_index_id'  => $item->book_index_id,
                        'book_bundle_id' => $item->book_bundle_id,
                    ]);
                    $ts->quantity = ($ts->quantity ?? 0) + $qty;
                    $ts->save();

                    self::syncTeamSitesInventory();
                } else {
                    // Restore to Main Warehouse & Sync SiteInventory
                    $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
                    $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                    if ($item->book_index_id) {
                        $index = BookIndex::find($item->book_index_id);
                        if ($index) {
                            $index->stock = ($index->stock ?? 0) + $qty;
                            $index->save();

                            SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                                ['quantity' => $index->stock]
                            );
                        }
                    } elseif ($item->book_id) {
                        $book = Book::find($item->book_id);
                        if ($book) {
                            $book->stock = ($book->stock ?? 0) + $qty;
                            $book->save(); // BookObserver automatically syncs Main Warehouse SiteInventory to $book->stock
                        }
                    } elseif ($item->book_bundle_id) {
                        $bundle = BookBundle::find($item->book_bundle_id);
                        if ($bundle) {
                            $bundle->stock = ($bundle->stock ?? 0) + $qty;
                            $bundle->save();

                            SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                                ['quantity' => $bundle->stock]
                            );
                        }
                    }
                }
            }

            // 2. Remove or cancel Consignment Inventory entries
            ConsignmentInventory::where('sales_order_id', $order->id)
                ->update(['status' => 'cancelled', 'notes' => 'Cancelled/Rejected: ' . $reason]);

            $order->update(['stock_deducted' => false]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore stock for Sales Order #' . $order->so_number . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Adjust stock and consignment for DR unselected/returned items.
     */
    public static function adjustForDRReturn(SalesOrder $order, array $unfulfilledItems)
    {
        DB::beginTransaction();
        try {
            $order->load('preparedBy');
            $userTeam = null;
            if ($order->type === 'area_sales_consignment' && $order->area_sales_staff_id) {
                $staff = \App\Models\User::find($order->area_sales_staff_id);
                if ($staff && !empty($staff->sales_team)) {
                    $userTeam = $staff->sales_team;
                }
            }
            if (empty($userTeam) && $order->preparedBy && !empty($order->preparedBy->sales_team)) {
                $userTeam = $order->preparedBy->sales_team;
            }

            foreach ($unfulfilledItems as $uItem) {
                $bookId      = $uItem['book_id'] ?? null;
                $bookIndexId = $uItem['book_index_id'] ?? null;
                $bundleId    = $uItem['book_bundle_id'] ?? null;
                $returnedQty = (int) ($uItem['quantity'] ?? 0);

                if ($returnedQty <= 0) continue;

                // Restore returned stock & Sync SiteInventory
                $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
                $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                if (!empty($userTeam)) {
                    $ts = TeamStock::firstOrNew([
                        'team_name'      => $userTeam,
                        'book_id'        => $bookId,
                        'book_index_id'  => $bookIndexId,
                        'book_bundle_id' => $bundleId,
                    ]);
                    $ts->quantity = ($ts->quantity ?? 0) + $returnedQty;
                    $ts->save();
                } else {
                    if ($bookIndexId) {
                        $index = BookIndex::find($bookIndexId);
                        if ($index) {
                            $index->stock = ($index->stock ?? 0) + $returnedQty;
                            $index->save();

                            SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                                ['quantity' => $index->stock]
                            );
                        }
                    } elseif ($bookId) {
                        $book = Book::find($bookId);
                        if ($book) {
                            $book->stock = ($book->stock ?? 0) + $returnedQty;
                            $book->save(); // BookObserver automatically syncs Main Warehouse SiteInventory to $book->stock
                        }
                    } elseif ($bundleId) {
                        $bundle = BookBundle::find($bundleId);
                        if ($bundle) {
                            $bundle->stock = ($bundle->stock ?? 0) + $returnedQty;
                            $bundle->save();

                            SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                                ['quantity' => $bundle->stock]
                            );
                        }
                    }
                }

                // Reduce Consignment Inventory
                $cInv = ConsignmentInventory::where('sales_order_id', $order->id)
                    ->where(function($q) use ($bookId, $bookIndexId, $bundleId) {
                        if ($bookIndexId) $q->where('book_index_id', $bookIndexId);
                        elseif ($bookId) $q->where('book_id', $bookId);
                        elseif ($bundleId) $q->where('book_bundle_id', $bundleId);
                    })->first();

                if ($cInv) {
                    $newQty = max(0, $cInv->quantity - $returnedQty);
                    if ($newQty === 0) {
                        $cInv->update(['status' => 'returned', 'quantity' => 0]);
                    } else {
                        $cInv->update(['quantity' => $newQty]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to adjust DR return stock for Sales Order #' . $order->so_number . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Synchronize TeamStock records into SiteInventory for Team sites (e.g. 'Team A', 'Team B', 'Team C')
     */
    public static function syncTeamSitesInventory()
    {
        $teamStocks = TeamStock::all();
        foreach ($teamStocks as $ts) {
            $teamName = trim($ts->team_name);
            if (empty($teamName)) continue;

            $teamSite = Site::where('name', $teamName)
                ->orWhere('name', 'Site ' . $teamName)
                ->orWhere('code', strtolower(str_replace(' ', '_', $teamName)))
                ->first();

            if ($teamSite) {
                if ($ts->book_id) {
                    SiteInventory::updateOrCreate(
                        ['site_id' => $teamSite->id, 'book_id' => $ts->book_id],
                        ['quantity' => max(0, (float)$ts->quantity)]
                    );
                } elseif ($ts->book_index_id) {
                    SiteInventory::updateOrCreate(
                        ['site_id' => $teamSite->id, 'book_index_id' => $ts->book_index_id],
                        ['quantity' => max(0, (float)$ts->quantity)]
                    );
                } elseif ($ts->book_bundle_id) {
                    SiteInventory::updateOrCreate(
                        ['site_id' => $teamSite->id, 'book_bundle_id' => $ts->book_bundle_id],
                        ['quantity' => max(0, (float)$ts->quantity)]
                    );
                }
            }
        }
    }
}
