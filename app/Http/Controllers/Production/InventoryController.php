<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
use App\Models\SiteInventory;
use App\Models\InventoryTransaction;
use App\Models\ProductStock;
use App\Models\Site;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InventoryController extends Controller
{
    public function overview(Request $request)
    {
        $search = $request->input('search');

        // Get Main Warehouse specifically
        $mainWarehouse = Site::where('name', 'Main Warehouse')->first();

        // Auto-sync: ensure all indices/bundles with stock > 0 are in site_inventory for Main Warehouse
        if ($mainWarehouse) {
            // Sync BookIndex stocks
            BookIndex::where('stock', '>', 0)->each(function($idx) use ($mainWarehouse) {
                $existing = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_index_id', $idx->id)->first();
                if (!$existing) {
                    SiteInventory::create([
                        'site_id'        => $mainWarehouse->id,
                        'book_index_id'  => $idx->id,
                        'book_id'        => null,
                        'book_bundle_id' => null,
                        'quantity'       => $idx->stock,
                    ]);
                }
            });

            // Sync BookBundle stocks
            BookBundle::where('stock', '>', 0)->each(function($bundle) use ($mainWarehouse) {
                $existing = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_bundle_id', $bundle->id)->first();
                if (!$existing) {
                    SiteInventory::create([
                        'site_id'        => $mainWarehouse->id,
                        'book_bundle_id' => $bundle->id,
                        'book_id'        => null,
                        'book_index_id'  => null,
                        'quantity'       => $bundle->stock,
                    ]);
                }
            });
        }

        // Virtual category warehouses dedicated strictly to Master Inventory
        $masterCategoryWarehouseNames = [
            'Bookstore Warehouse',
            'Area Sales Warehouse',
            'Consignment Warehouse',
            'Reserved Warehouse',
            'Book Sale Warehouse',
            'E-commerce Warehouse',
            'Damaged Stock Warehouse',
            'Returned Stock Warehouse',
            'In Transit Warehouse',
        ];

        $siteSearch = $request->input('site_search');

        // Sync TeamStock into SiteInventory so team site columns in Master Registry are always 100% up-to-date
        \App\Services\StockDeductionService::syncTeamSitesInventory();

        // Sites query has been moved below pagination to filter inventory only for visible items.

        $allBooks = Book::select('id', 'name', 'stock', 'cost', 'reorder_point')->get();
        
        $query = Book::where('is_book', true)->with(['inventory.site'])->latest();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }
        $books = $query->paginate(10, ['*'], 'books_page')->withQueryString();

        $nonBooksQuery = Book::where('is_book', false)->latest();
        if (!empty($search)) {
            $nonBooksQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }
        $nonBooks = $nonBooksQuery->paginate(10, ['*'], 'nonbooks_page')->withQueryString();

        // Calculate statistics based on MAIN WAREHOUSE ONLY
        $totalBooks = 0;
        $lowStock = 0;
        $outOfStock = 0;
        $inventoryValue = 0;

        $mainInventoryMap = $mainWarehouse 
            ? $mainWarehouse->inventory()->pluck('quantity', 'book_id')->toArray() 
            : [];

        foreach ($allBooks as $book) {
            $mainWarehouseQuantity = $mainInventoryMap[$book->id] ?? 0;

            if ($mainWarehouseQuantity > 0) {
                $totalBooks++;
                $inventoryValue += $mainWarehouseQuantity * ($book->cost ?? 0);
                
                if ($mainWarehouseQuantity <= ($book->reorder_point ?? 0)) {
                    $lowStock++;
                }
            } else {
                $outOfStock++;
            }
        }

        $totalMovements = InventoryTransaction::count();

        $recentMovements = InventoryTransaction::with('book')
            ->latest()
            ->take(200)
            ->get();

        $user = Auth::user();
        $userApprovalDivision = StockTransfer::approvalDivisionForUser($user);
        $isTransferApprover = $user && (
            $user->isSuperAdmin()
            || str_contains(strtolower($user->position ?? ''), 'manager')
            || str_contains(strtolower($user->position ?? ''), 'supervisor')
        );

        $pendingTransfers = StockTransfer::whereIn('status', ['logistics_assignment', 'logistics_assigned', 'completed'])
            ->with(['fromSite', 'toSite', 'book', 'createdBy'])
            ->when(!$user?->isSuperAdmin(), function ($query) use ($user, $userApprovalDivision, $isTransferApprover) {
                $query->where(function ($scope) use ($user, $userApprovalDivision, $isTransferApprover) {
                    $scope->where('created_by', $user->id);

                    if ($isTransferApprover) {
                        $scope->orWhere('approval_division', $userApprovalDivision);
                    }
                });
            })
            ->latest()
            ->get();

        $batchData = [];
        $stockTransferWorkflow = StockTransfer::with([
                'fromSite',
                'toSite',
                'book',
                'bookIndex.book',
                'bookBundle',
                'createdBy',
                'approvedBy',
                'accountingReviewedBy',
                'logisticsAssignedTo',
                'logisticsAssignedBy',
                'completedBy',
            ])
            ->whereIn('status', [
                'pending',
                'accounting_review',
                'logistics_assignment',
                'logistics_assigned',
                'completed',
                'rejected',
            ])
            ->when(!$user?->isSuperAdmin(), function ($query) use ($user, $userApprovalDivision, $isTransferApprover) {
                $query->where(function ($scope) use ($user, $userApprovalDivision, $isTransferApprover) {
                    $scope->where('created_by', $user->id)
                        ->orWhere('logistics_assigned_to', $user->id);

                    if ($isTransferApprover) {
                        $scope->orWhere('approval_division', $userApprovalDivision);
                    }

                    if ($this->isAccountingReviewer($user)) {
                        $scope->orWhere('status', 'accounting_review');
                    }

                    if ($this->isLogisticsAssigner($user)) {
                        $scope->orWhereIn('status', ['logistics_assignment', 'logistics_assigned']);
                    }
                });
            })
            ->latest()
            ->get()
            ->groupBy(function ($item) {
                return $item->batch_id ?: ('single_' . $item->id);
            })
            ->map(function ($items) use (&$batchData) {
                $first = $items->first();
                $first->total_quantity = (int) $items->sum('quantity');
                $first->items_count = (int) $items->count();
                $batchItems = $items->map(function($i) {
                    $unitPrice = (float) (
                        $i->bookIndex ? ($i->bookIndex->price ?: ($i->bookIndex->book?->price ?? 0))
                        : ($i->book ? $i->book->price 
                        : ($i->bookBundle ? $i->bookBundle->price : 0))
                    );
                    $barcode = $i->bookIndex ? ($i->bookIndex->barcode ?: ($i->bookIndex->nbs_barcode ?: $i->bookIndex->article))
                        : ($i->book ? ($i->book->barcode ?: ($i->book->isbn ?: $i->book->item_code))
                        : ($i->bookBundle ? $i->bookBundle->sku : ''));
                    return [
                        'id'         => $i->id,
                        'name'       => (string) $i->item_name,
                        'type'       => (string) $i->item_type,
                        'quantity'   => (int)    $i->quantity,
                        'unit_price' => $unitPrice,
                        'barcode'    => (string) $barcode,
                    ];
                })->values()->toArray();

                $batchData[$first->id] = [
                    'items'          => $batchItems,
                    'total_quantity' => (int) $items->sum('quantity'),
                    'items_count'    => (int) $items->count(),
                ];

                return $first;
            })
            ->values();

        $logisticsUsers = User::where('status', true)
            ->where(function($q) {
                $q->where('position', 'like', '%Logistic%')
                  ->orWhere('position', 'like', '%Rider%')
                  ->orWhere('position', 'like', '%Driver%')
                  ->orWhere('position', 'like', '%Delivery%')
                  ->orWhere('position', 'like', '%Warehouse%')
                  ->orWhere('position', 'like', '%Staff%')
                  ->orWhere('position', 'like', '%Admin%')
                  ->orWhere('division', 'like', '%Production%')
                  ->orWhere('division', 'like', '%Logistic%');
            })
            ->orderBy('first_name')
            ->get();

        $isAccountingReviewer = $this->isAccountingReviewer($user);
        $isLogisticsAssigner = $this->isLogisticsAssigner($user);

        $allIndices = null;
        $allBundles = null;

        // Fetch book indices
        $indicesQuery = \App\Models\BookIndex::with(['book', 'inventory'])->latest();
        if (!empty($search)) {
            $indicesQuery->where(function($q) use ($search) {
                $q->where('index_value', 'like', '%' . $search . '%')
                  ->orWhereHas('book', function($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%');
                  });
            });
        }
        $indices = $indicesQuery->paginate(10, ['*'], 'indices_page')->withQueryString();

        // Fetch book bundles
        $bundlesQuery = \App\Models\BookBundle::with(['books', 'inventory'])->latest();
        if (!empty($search)) {
            $bundlesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }
        $bundles = $bundlesQuery->paginate(10, ['*'], 'bundles_page')->withQueryString();

        // Fetch physical sites with fresh inventory after any sync
        $visibleBookIds = collect($books->items())->pluck('id')
            ->merge(collect($nonBooks->items())->pluck('id'))
            ->unique()
            ->toArray();
        $visibleIndexIds = collect($indices->items())->pluck('id')->unique()->toArray();
        $visibleBundleIds = collect($bundles->items())->pluck('id')->unique()->toArray();

        $allSites = Site::where('is_active', true)
            ->whereNotIn('name', $masterCategoryWarehouseNames)
            ->with(['inventory' => function ($q) use ($visibleBookIds, $visibleIndexIds, $visibleBundleIds) {
                $q->where('quantity', '>', 0)
                  ->where(function($query) use ($visibleBookIds, $visibleIndexIds, $visibleBundleIds) {
                      $query->whereIn('book_id', $visibleBookIds)
                            ->orWhereIn('book_index_id', $visibleIndexIds)
                            ->orWhereIn('book_bundle_id', $visibleBundleIds);
                  });
            }])
            ->get();

        $sitesQuery = Site::where('is_active', true)
            ->whereNotIn('name', $masterCategoryWarehouseNames)
            ->with(['inventory' => function ($q) {
                $q->where('quantity', '>', 0)
                  ->with(['book', 'bookIndex.book', 'bookBundle']);
            }]);

        if (!empty($siteSearch)) {
            $sitesQuery->where(function($q) use ($siteSearch) {
                $q->where('name', 'like', '%' . $siteSearch . '%')
                  ->orWhere('code', 'like', '%' . $siteSearch . '%')
                  ->orWhere('location', 'like', '%' . $siteSearch . '%');
            });
        }
        $sites = $sitesQuery->paginate(10, ['*'], 'sites_page')->withQueryString();

        // Fetch consignment inventory: 1. Area Consignment (grouped BY CUSTOMER)
        $areaOrders = \App\Models\SalesOrder::with(['customer', 'areaSalesStaff', 'preparedBy', 'items.book', 'items.bookIndex', 'items.bookBundle'])
            ->whereIn('type', ['area_consignment', 'area_sales_consignment'])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $consignmentStaffCollection = $areaOrders->groupBy(function($order) {
            $rep = trim($order->customer_representative ?? '');
            if (!empty($rep)) {
                return 'rep_' . strtolower($rep);
            }
            return $order->customer_id ? 'cust_' . $order->customer_id : 'unassigned';
        })->map(function ($orders) {
            $first = $orders->first();
            $rep = trim($first->customer_representative ?? '');
            $cust = trim($first->customer->customer_name ?? ($first->customer->company_name ?? ''));

            if (!empty($rep) && !empty($cust) && strtolower($rep) !== strtolower($cust)) {
                $customerName = $rep;
                $companyName = $cust;
            } elseif (!empty($rep)) {
                $customerName = $rep;
                $companyName = null;
            } else {
                $customerName = $cust ?: 'Area Consignment Customer';
                $companyName = null;
            }
            $staffName = $first->areaSalesStaff ? $first->areaSalesStaff->name : ($first->preparedBy ? $first->preparedBy->name : 'Area Sales Team');

            $drNumbers = $orders->map(function($o) {
                return 'DR-' . $o->so_number;
            })->unique()->values()->toArray();

            $drBreakdown = [];
            $bookMap = [];
            foreach ($orders as $order) {
                $drNum = 'DR-' . $order->so_number;
                $soDate = $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('m/d/Y') : ($order->created_at ? $order->created_at->format('m/d/Y') : 'N/A');
                $rawDate = $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : ($order->created_at ? $order->created_at->format('Y-m-d') : '');
                $drItems = [];

                foreach ($order->items as $item) {
                    $name = $item->bookIndex ? $item->bookIndex->display_name : ($item->book ? $item->book->name : ($item->bookBundle ? $item->bookBundle->name : 'N/A'));
                    $sku = $item->bookIndex ? ($item->bookIndex->barcode ?: $item->bookIndex->article) : ($item->book ? ($item->book->sku ?: $item->book->item_code) : ($item->bookBundle ? $item->bookBundle->sku : ''));
                    $key = ($item->book_index_id ? 'idx_' . $item->book_index_id : ($item->book_id ? 'bk_' . $item->book_id : 'bdl_' . $item->book_bundle_id));
                    $qty = (int) $item->quantity;
                    $unitPrice = (float) ($item->price ?? ($item->book->price ?? ($item->bookIndex->price ?? ($item->bookBundle->price ?? 0))));
                    $subtotal = (float) ($item->subtotal ?? ($unitPrice * $qty));

                    $drItems[] = [
                        'sku' => $sku,
                        'name' => $name,
                        'qty' => $qty,
                        'price' => $unitPrice,
                        'amount' => $subtotal,
                        'order_date' => $soDate,
                        'raw_date' => $rawDate,
                        'dr_number' => $drNum,
                    ];

                    if (!isset($bookMap[$key])) {
                        $bookMap[$key] = [
                            'name' => $name,
                            'sku' => $sku,
                            'total_qty' => 0,
                            'order_count' => 0,
                            'drs' => [],
                        ];
                    }
                    $bookMap[$key]['total_qty'] += $qty;
                    $bookMap[$key]['order_count']++;
                    if (!in_array($drNum, $bookMap[$key]['drs'])) {
                        $bookMap[$key]['drs'][] = $drNum;
                    }
                }

                $drBreakdown[] = [
                    'dr_number' => $drNum,
                    'so_number' => $order->so_number,
                    'order_date' => $soDate,
                    'items' => $drItems,
                    'total_qty' => collect($drItems)->sum('qty'),
                ];
            }

            return (object) [
                'customer_name' => $customerName,
                'company_name' => $companyName,
                'staff_name' => $staffName,
                'dr_numbers' => $drNumbers,
                'dr_breakdown' => $drBreakdown,
                'orders_count' => $orders->count(),
                'books' => collect($bookMap)->sortBy(fn($b) => $b['name'])->values(),
                'total_items' => collect($bookMap)->sum('total_qty'),
            ];
        })->filter(fn($c) => count($c->books) > 0)->sortBy(fn($c) => $c->customer_name)->values();

        if (!empty($search)) {
            $searchLower = strtolower(trim($search));
            $consignmentStaffCollection = $consignmentStaffCollection->filter(function($cData) use ($searchLower) {
                if (str_contains(strtolower($cData->customer_name ?? ''), $searchLower)) return true;
                if (!empty($cData->company_name) && str_contains(strtolower($cData->company_name), $searchLower)) return true;
                if (!empty($cData->staff_name) && str_contains(strtolower($cData->staff_name), $searchLower)) return true;
                
                if (!empty($cData->dr_numbers)) {
                    foreach ($cData->dr_numbers as $dr) {
                        if (str_contains(strtolower($dr), $searchLower)) return true;
                    }
                }
                
                if (!empty($cData->books)) {
                    foreach ($cData->books as $b) {
                        if (str_contains(strtolower($b['name'] ?? ''), $searchLower)) return true;
                        if (str_contains(strtolower($b['sku'] ?? ''), $searchLower)) return true;
                    }
                }
                
                return false;
            })->values();
        }

        // Paginate Area Consignment
        $currentPageArea = \Illuminate\Pagination\Paginator::resolveCurrentPage('area_consignment_page');
        $perPageArea = 5;
        $currentAreaItems = $consignmentStaffCollection->slice(($currentPageArea - 1) * $perPageArea, $perPageArea)->values();
        $consignmentStaff = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentAreaItems,
            $consignmentStaffCollection->count(),
            $perPageArea,
            $currentPageArea,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'area_consignment_page']
        );
        $consignmentStaff->withQueryString();

        // Fetch consignment inventory: 2. Direct Consignment (grouped by customer / NBS)
        $directOrders = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bookIndex', 'items.bookBundle'])
            ->where(function($q) {
                $q->where('type', 'direct_consignment')
                  ->orWhere('so_number', 'like', 'SO-NBS-%');
            })
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $directConsignmentCollection = $directOrders->groupBy(function($order) {
            $rep = trim($order->customer_representative ?? '');
            if (!empty($rep)) {
                return 'rep_' . strtolower($rep);
            }
            return $order->customer_id ? 'cust_' . $order->customer_id : 'unassigned';
        })->map(function ($orders) {
            $first = $orders->first();
            $rep = trim($first->customer_representative ?? '');
            $cust = trim($first->customer->customer_name ?? ($first->customer->company_name ?? ''));

            if (!empty($rep) && !empty($cust) && strtolower($rep) !== strtolower($cust)) {
                $customerName = $rep;
                $companyName = $cust;
            } elseif (!empty($rep)) {
                $customerName = $rep;
                $companyName = null;
            } else {
                $customerName = $cust ?: 'Direct Consignment Customer';
                $companyName = null;
            }

            $drNumbers = $orders->map(function($o) {
                return 'DR-' . $o->so_number;
            })->unique()->values()->toArray();

            $drBreakdown = [];
            $bookMap = [];
            foreach ($orders as $order) {
                $drNum = 'DR-' . $order->so_number;
                $soDate = $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('m/d/Y') : ($order->created_at ? $order->created_at->format('m/d/Y') : 'N/A');
                $rawDate = $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : ($order->created_at ? $order->created_at->format('Y-m-d') : '');
                $drItems = [];

                foreach ($order->items as $item) {
                    $name = $item->bookIndex ? $item->bookIndex->display_name : ($item->book ? $item->book->name : ($item->bookBundle ? $item->bookBundle->name : 'N/A'));
                    $sku = $item->bookIndex ? ($item->bookIndex->barcode ?: $item->bookIndex->article) : ($item->book ? ($item->book->sku ?: $item->book->item_code) : ($item->bookBundle ? $item->bookBundle->sku : ''));
                    $key = ($item->book_index_id ? 'idx_' . $item->book_index_id : ($item->book_id ? 'bk_' . $item->book_id : 'bdl_' . $item->book_bundle_id));
                    $qty = (int) $item->quantity;
                    $unitPrice = (float) ($item->price ?? ($item->book->price ?? ($item->bookIndex->price ?? ($item->bookBundle->price ?? 0))));
                    $subtotal = (float) ($item->subtotal ?? ($unitPrice * $qty));

                    $drItems[] = [
                        'sku' => $sku,
                        'name' => $name,
                        'qty' => $qty,
                        'price' => $unitPrice,
                        'amount' => $subtotal,
                        'order_date' => $soDate,
                        'raw_date' => $rawDate,
                        'dr_number' => $drNum,
                    ];

                    if (!isset($bookMap[$key])) {
                        $bookMap[$key] = [
                            'name' => $name,
                            'sku' => $sku,
                            'total_qty' => 0,
                            'order_count' => 0,
                            'drs' => [],
                        ];
                    }
                    $bookMap[$key]['total_qty'] += $qty;
                    $bookMap[$key]['order_count']++;
                    if (!in_array($drNum, $bookMap[$key]['drs'])) {
                        $bookMap[$key]['drs'][] = $drNum;
                    }
                }

                $drBreakdown[] = [
                    'dr_number' => $drNum,
                    'so_number' => $order->so_number,
                    'order_date' => $soDate,
                    'items' => $drItems,
                    'total_qty' => collect($drItems)->sum('qty'),
                ];
            }
            return (object) [
                'customer_name' => $customerName,
                'company_name' => $companyName,
                'dr_numbers' => $drNumbers,
                'dr_breakdown' => $drBreakdown,
                'orders_count' => $orders->count(),
                'books' => collect($bookMap)->sortBy(fn($b) => $b['name'])->values(),
                'total_items' => collect($bookMap)->sum('total_qty'),
            ];
        })->filter(fn($c) => count($c->books) > 0)->sortBy(fn($c) => $c->customer_name)->values();

        if (!empty($search)) {
            $searchLower = strtolower(trim($search));
            $directConsignmentCollection = $directConsignmentCollection->filter(function($cData) use ($searchLower) {
                if (str_contains(strtolower($cData->customer_name ?? ''), $searchLower)) return true;
                if (!empty($cData->company_name) && str_contains(strtolower($cData->company_name), $searchLower)) return true;
                
                if (!empty($cData->dr_numbers)) {
                    foreach ($cData->dr_numbers as $dr) {
                        if (str_contains(strtolower($dr), $searchLower)) return true;
                    }
                }
                
                if (!empty($cData->books)) {
                    foreach ($cData->books as $b) {
                        if (str_contains(strtolower($b['name'] ?? ''), $searchLower)) return true;
                        if (str_contains(strtolower($b['sku'] ?? ''), $searchLower)) return true;
                    }
                }
                
                return false;
            })->values();
        }

        // Paginate Direct Consignment
        $currentPageDirect = \Illuminate\Pagination\Paginator::resolveCurrentPage('direct_consignment_page');
        $perPageDirect = 5;
        $currentDirectItems = $directConsignmentCollection->slice(($currentPageDirect - 1) * $perPageDirect, $perPageDirect)->values();
        $directConsignmentCustomers = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentDirectItems,
            $directConsignmentCollection->count(),
            $perPageDirect,
            $currentPageDirect,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'direct_consignment_page']
        );
        $directConsignmentCustomers->withQueryString();

        $sidebar = 'production';
        $role = 'Production Manager';
        if ($user) {
            $division = strtolower($user->division ?? '');
            $department = strtolower($user->department ?? '');
            if (str_contains($division, 'finance') || str_contains($division, 'accounting') || str_contains($division, 'admin') || str_contains($department, 'finance') || str_contains($department, 'accounting')) {
                $sidebar = 'admin-finance';
                $role = 'Finance Manager';
            }
        }

        // Lost Inventory Query
        $lostQuery = \App\Models\LostInventory::with(['book', 'bookIndex.book', 'bookBundle', 'site', 'user'])->latest();
        if (!empty($search)) {
            $lostQuery->where(function($q) use ($search) {
                $q->where('reason', 'like', '%' . $search . '%')
                  ->orWhere('team_name', 'like', '%' . $search . '%')
                  ->orWhereHas('book', fn($b) => $b->where('name', 'like', '%' . $search . '%')->orWhere('sku', 'like', '%' . $search . '%'))
                  ->orWhereHas('bookIndex', function($idx) use ($search) {
                      $idx->where('index_value', 'like', '%' . $search . '%')
                          ->orWhere('custom_name', 'like', '%' . $search . '%')
                          ->orWhere('article', 'like', '%' . $search . '%')
                          ->orWhere('barcode', 'like', '%' . $search . '%')
                          ->orWhereHas('book', fn($bq) => $bq->where('name', 'like', '%' . $search . '%')->orWhere('sku', 'like', '%' . $search . '%'));
                  })
                  ->orWhereHas('bookBundle', fn($bdl) => $bdl->where('name', 'like', '%' . $search . '%')->orWhere('sku', 'like', '%' . $search . '%'))
                  ->orWhereHas('site', fn($st) => $st->where('name', 'like', '%' . $search . '%'));
            });
        }
        $lostInventories = $lostQuery->paginate(15, ['*'], 'lost_page')->withQueryString();
        $totalLostQty = \App\Models\LostInventory::sum('quantity');

        return view('production.inventory.overview', compact(
            'totalBooks', 
            'lowStock', 
            'outOfStock', 
            'inventoryValue',
            'books',
            'nonBooks',
            'allBooks',
            'recentMovements',
            'totalMovements',
            'sites',
            'allSites',
            'pendingTransfers',
            'mainWarehouse',
            'stockTransferWorkflow',
            'logisticsUsers',
            'isAccountingReviewer',
            'isLogisticsAssigner',
            'indices',
            'bundles',
            'allIndices',
            'allBundles',
            'consignmentStaff',
            'directConsignmentCustomers',
            'batchData',
            'sidebar',
            'role',
            'lostInventories',
            'totalLostQty'
        ));
    }

    public function markAsLost(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_type'  => 'required|in:book,index,bundle,non_book',
            'product_id'    => 'required|integer',
            'quantity'      => 'required|integer|min:1',
            'location_type' => 'required|in:site,team',
            'site_id'       => 'nullable|required_if:location_type,site|integer|exists:sites,id',
            'team_name'     => 'nullable|required_if:location_type,team|string',
            'reason'        => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Failed to mark item as lost: ' . $validator->errors()->first());
        }

        $productType = $request->input('product_type');
        $productId = (int) $request->input('product_id');
        $lostQty = (int) $request->input('quantity');
        $locationType = $request->input('location_type');
        $siteId = $request->input('site_id');
        $teamName = $request->input('team_name');
        $reason = $request->input('reason');
        $userId = auth()->id();

        try {
            \DB::transaction(function() use ($productType, $productId, $lostQty, $locationType, $siteId, $teamName, $reason, $userId) {
                $bookId = null;
                $bookIndexId = null;
                $bookBundleId = null;
                $productName = '';

                if ($productType === 'book' || $productType === 'non_book') {
                    $book = \App\Models\Book::findOrFail($productId);
                    $bookId = $book->id;
                    $productName = $book->name;

                    if ($locationType === 'site') {
                        $siteInv = \App\Models\SiteInventory::where('site_id', $siteId)->where('book_id', $bookId)->first();
                        $availQty = $siteInv ? (int) $siteInv->quantity : 0;
                        if ($lostQty > $availQty) {
                            throw new \Exception("Cannot mark {$lostQty} pcs as lost. Available stock at selected site is only {$availQty} pcs.");
                        }
                        $siteInv->decrement('quantity', $lostQty);
                        if ($book->stock >= $lostQty) {
                            $book->decrement('stock', $lostQty);
                        }
                    } else {
                        $teamStock = \App\Models\TeamStock::where('team_name', $teamName)->where('book_id', $bookId)->first();
                        $availQty = $teamStock ? (int) $teamStock->quantity : 0;
                        if ($lostQty > $availQty) {
                            throw new \Exception("Cannot mark {$lostQty} pcs as lost. Available stock for {$teamName} is only {$availQty} pcs.");
                        }
                        $teamStock->decrement('quantity', $lostQty);
                        if ($book->stock >= $lostQty) {
                            $book->decrement('stock', $lostQty);
                        }
                    }
                } elseif ($productType === 'index') {
                    $index = \App\Models\BookIndex::findOrFail($productId);
                    $bookIndexId = $index->id;
                    $productName = $index->title;

                    if ($locationType === 'site') {
                        $siteInv = \App\Models\SiteInventory::where('site_id', $siteId)->where('book_index_id', $bookIndexId)->first();
                        $availQty = $siteInv ? (int) $siteInv->quantity : 0;
                        if ($lostQty > $availQty) {
                            throw new \Exception("Cannot mark {$lostQty} pcs as lost. Available stock at selected site is only {$availQty} pcs.");
                        }
                        $siteInv->decrement('quantity', $lostQty);
                        if ($index->stock >= $lostQty) {
                            $index->decrement('stock', $lostQty);
                        }
                    } else {
                        $teamStock = \App\Models\TeamStock::where('team_name', $teamName)->where('book_index_id', $bookIndexId)->first();
                        $availQty = $teamStock ? (int) $teamStock->quantity : 0;
                        if ($lostQty > $availQty) {
                            throw new \Exception("Cannot mark {$lostQty} pcs as lost. Available stock for {$teamName} is only {$availQty} pcs.");
                        }
                        $teamStock->decrement('quantity', $lostQty);
                        if ($index->stock >= $lostQty) {
                            $index->decrement('stock', $lostQty);
                        }
                    }
                } elseif ($productType === 'bundle') {
                    $bundle = \App\Models\BookBundle::findOrFail($productId);
                    $bookBundleId = $bundle->id;
                    $productName = $bundle->bundle_name;

                    if ($locationType === 'site') {
                        $siteInv = \App\Models\SiteInventory::where('site_id', $siteId)->where('book_bundle_id', $bookBundleId)->first();
                        $availQty = $siteInv ? (int) $siteInv->quantity : 0;
                        if ($lostQty > $availQty) {
                            throw new \Exception("Cannot mark {$lostQty} pcs as lost. Available stock at selected site is only {$availQty} pcs.");
                        }
                        $siteInv->decrement('quantity', $lostQty);
                        if ($bundle->stock >= $lostQty) {
                            $bundle->decrement('stock', $lostQty);
                        }
                    } else {
                        $teamStock = \App\Models\TeamStock::where('team_name', $teamName)->where('book_bundle_id', $bookBundleId)->first();
                        $availQty = $teamStock ? (int) $teamStock->quantity : 0;
                        if ($lostQty > $availQty) {
                            throw new \Exception("Cannot mark {$lostQty} pcs as lost. Available stock for {$teamName} is only {$availQty} pcs.");
                        }
                        $teamStock->decrement('quantity', $lostQty);
                        if ($bundle->stock >= $lostQty) {
                            $bundle->decrement('stock', $lostQty);
                        }
                    }
                }

                // Record entry in lost_inventories
                \App\Models\LostInventory::create([
                    'product_type'   => $productType,
                    'book_id'        => $bookId,
                    'book_index_id'  => $bookIndexId,
                    'book_bundle_id' => $bookBundleId,
                    'quantity'       => $lostQty,
                    'site_id'        => $locationType === 'site' ? $siteId : null,
                    'team_name'      => $locationType === 'team' ? $teamName : null,
                    'reason'         => $reason,
                    'user_id'        => $userId,
                    'lost_date'      => now(),
                ]);

                // Audit trail in InventoryTransaction if book_id is present
                if ($bookId) {
                    $siteObj = $siteId ? \App\Models\Site::find($siteId) : null;
                    \App\Models\InventoryTransaction::create([
                        'book_id'          => $bookId,
                        'type'             => 'LOST',
                        'quantity'         => -$lostQty,
                        'location'         => $locationType === 'site' ? ($siteObj->name ?? 'Main Warehouse') : $teamName,
                        'source'           => 'Lost Inventory Adjustment',
                        'reference_number' => 'LOST-' . date('YmdHis'),
                        'notes'            => $reason ?: 'Marked as lost in Inventory Overview',
                        'status'           => 'completed',
                        'transaction_date' => now(),
                        'user_id'          => $userId,
                    ]);
                }
            });

            return redirect()->back()->with('success', "Successfully recorded lost inventory and deducted stock.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error marking inventory as lost: ' . $e->getMessage());
        }
    }

    private function isAccountingReviewer($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $values = collect([$user->division, $user->department, $user->position])
            ->filter()
            ->map(fn ($value) => strtolower($value));

        return $values->contains(fn ($value) => str_contains($value, 'accounting')
            || str_contains($value, 'finance')
            || str_contains($value, 'admin & finance'));
    }

    private function isLogisticsAssigner($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $position   = strtolower($user->position ?? '');
        $division   = strtolower($user->division ?? '');
        $department = strtolower($user->department ?? '');

        return str_contains($position, 'logistic') || str_contains($position, 'production') || str_contains($position, 'warehouse') || str_contains($position, 'admin') || str_contains($position, 'manager') || str_contains($position, 'supervisor')
            || str_contains($division, 'logistic') || str_contains($division, 'production') || str_contains($division, 'warehouse')
            || str_contains($department, 'logistic') || str_contains($department, 'production') || str_contains($department, 'warehouse');
    }

    public function addStock()
    {
        $books = Book::all();
        
        // Fetch completed received items for dropdown selection
        $completedItems = InventoryTransaction::with('book')
            ->where('type', 'in')
            ->where('status', 'completed')
            ->latest()
            ->get();
        
        return view('production.inventory.add-stock', compact('books', 'completedItems'));
    }

    public function received()
    {
        $books = Book::all();

        $receivedItems = InventoryTransaction::with('book')
            ->where('type', 'in')
            ->latest()
            ->get();
            
        return view('production.inventory.received', compact('receivedItems', 'books'));
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'book_id' => 'nullable|exists:books,id',
            'new_book_name' => 'nullable|string|max:255',
            'new_book_sku' => 'nullable|string|max:100|unique:books,sku',
            'quantity' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'stockSource' => 'required|string',
            'status' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            if ($request->has('new_product_mode') && $request->new_product_mode == '1') {
                if (!$request->new_book_name || !$request->new_book_sku) {
                     return back()->with('error', 'Book Name and SKU are required.')->withInput();
                }

                $book = new Book();
                $book->name = $request->new_book_name;
                $book->sku = $request->new_book_sku;
                $book->stock = 0;
                $book->save();
            } else {
                 if (!$request->book_id) {
                    return back()->with('error', 'Please select a book.')->withInput();
                }
                $book = Book::findOrFail($request->book_id);
            }
            
            $transaction = new InventoryTransaction();
            $transaction->book_id = $book->id;
            $transaction->type = 'in';
            $transaction->quantity = $request->quantity;
            $transaction->location = 'Main Warehouse';
            $transaction->source = $request->stockSource;
            $transaction->supplier = $request->stockSource; 
            $transaction->reference_number = $request->reference_number;
            $transaction->notes = $request->notes ?? 'Received item - awaiting stock addition';
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = $request->transaction_date;
            $transaction->status = $request->status ?? 'pending';
            $transaction->total_cost = $request->total_cost;
            $transaction->unit_cost = $request->quantity > 0 ? ($request->total_cost / $request->quantity) : 0;
            $transaction->save();
            
            DB::commit();

            return redirect()->route('production.inventory.received')
                ->with('success', 'Received item recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add stock: ' . $e->getMessage())->withInput();
        }
    }

    public function updateTransaction(Request $request, $id)
    {
        $request->validate([
            'reference_number' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'stockSource' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'total_cost' => 'required|numeric|min:0',
            'status' => 'required|string|in:completed,pending,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $transaction = InventoryTransaction::findOrFail($id);
            
            // NOTE: Stock is NOT adjusted here anymore
            // Only update transaction details
            $transaction->reference_number = $request->reference_number;
            $transaction->transaction_date = $request->transaction_date;
            $transaction->source = $request->stockSource;
            $transaction->supplier = $request->stockSource;
            $transaction->quantity = $request->quantity;
            $transaction->total_cost = $request->total_cost;
            $transaction->unit_cost = $request->quantity > 0 ? ($request->total_cost / $request->quantity) : 0;
            $transaction->status = $request->status;
            
            $transaction->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaction updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = InventoryTransaction::findOrFail($id);
            $book = Book::findOrFail($transaction->book_id);

            // Revert stock if it was a completed transaction
            if ($transaction->status === 'completed') {
                if ($transaction->type == 'in') {
                    $book->stock -= $transaction->quantity;
                } else {
                    $book->stock += $transaction->quantity;
                }
                $book->save();
            }

            $transaction->delete();
            DB::commit();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Transaction deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
             if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to delete transaction.');
        }
    }

    public function getProductDetails($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        return response()->json([
            'sku' => $book->sku,
            'current_stock' => $book->stock,
            'cost' => number_format($book->cost, 2),
        ]);
    }

    /**
     * Process Add Stock - This is the ONLY method that updates product stock
     */
    public function processAddStock(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:inventory_transactions,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transaction = InventoryTransaction::findOrFail($request->transaction_id);
            $book = Book::findOrFail($transaction->book_id);

            // Update book stock
            $book->stock += $request->quantity;
            $book->save();

            // Optionally update ProductStock (location-wise) - renamed to Book link in migration
            $bookStock = ProductStock::firstOrNew([
                'book_id' => $book->id,
                'location' => 'Main Warehouse'
            ]);
            $bookStock->quantity += $request->quantity;
            $bookStock->save();

            DB::commit();

            return redirect()->route('production.inventory.overview')
                ->with('success', 'Stock added successfully! Master inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add stock: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update Stock Directly - API endpoint for inventory overview
     * Prevents stock from exceeding max_stock value
     */
    public function updateStockDirectly(Request $request, $bookId)
    {
        $request->validate([
            'action' => 'required|in:add,set',
            'site_id' => 'required|exists:sites,id',
            'quantity' => 'nullable|integer|min:1',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $book = Book::findOrFail($bookId);
            $site = Site::findOrFail($request->site_id);
            
            // Get site inventory record
            $siteInventory = \App\Models\SiteInventory::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'book_id' => $book->id
                ],
                [
                    'quantity' => 0
                ]
            );

            // Get max_stock - default to site's max stock, then book's max stock, then high value
            $maxStock = $siteInventory->max_stock ?? $book->max_stock ?? PHP_INT_MAX;
            
            $oldStock = $siteInventory->quantity;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;
            }

            // Update site inventory
            $siteInventory->quantity = $newStock;
            $siteInventory->save();

            // If it is Main Warehouse (site_id = 1), also update book stock
            if ($site->id == 1 || $site->name == 'Main Warehouse') {
                $book->stock = $newStock;
                $book->save();
            }

            // Sync TeamStock balance for Team sites (e.g. Team A, Team B, Team C, Book Sales, MIBF)
            $teamStock = \App\Models\TeamStock::where('team_name', $site->name)
                ->where('book_id', $book->id)
                ->first();
            if ($teamStock) {
                $teamStock->quantity = $newStock;
                $teamStock->save();
            } elseif (in_array($site->name, ['Team A', 'Team B', 'Team C', 'Book Sales', 'MIBF']) || str_contains(strtolower($site->name), 'team')) {
                \App\Models\TeamStock::create([
                    'team_name' => $site->name,
                    'book_id'   => $book->id,
                    'quantity'  => $newStock,
                ]);
            }

            // Update ProductStock for compatibility
            $bookStock = ProductStock::firstOrNew([
                'book_id' => $book->id,
                'location' => $site->name
            ]);
            $bookStock->quantity = $newStock;
            $bookStock->save();

            // Create inventory transaction record
            $transaction = new InventoryTransaction();
            $transaction->book_id = $book->id;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = $site->name;
            $transaction->source = 'Manual Adjustment';
            $transaction->notes = $request->action === 'add' 
                ? "Added {$request->quantity} units to {$site->name} via inventory overview"
                : "Manually set stock at {$site->name} from {$oldStock} to {$newStock}";
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'max_stock' => $maxStock
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reconcileStock(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && auth()->user()->position !== 'Super Admin' && auth()->user()->id != 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only Super Admin can perform stock recalculation.'
            ], 403);
        }

        try {
            // 1. Restore stock for any cancelled orders that still have stock_deducted = true
            $cancelledOrders = \App\Models\SalesOrder::where('status', 'cancelled')
                ->where('stock_deducted', true)
                ->get();
            foreach ($cancelledOrders as $order) {
                \App\Services\StockDeductionService::restoreForSalesOrder($order, 'Recalculation Cancellation');
                $order->update(['stock_deducted' => false]);
            }

            // 2. Process any active Sales Orders that were created without deducting stock
            $undeductedOrders = \App\Models\SalesOrder::where('stock_deducted', false)
                ->whereNotIn('status', ['cancelled'])
                ->get();
            foreach ($undeductedOrders as $order) {
                \App\Services\StockDeductionService::deductForSalesOrder($order);
            }

            // 3. Sync TeamStock to SiteInventory for all team sites
            \App\Services\StockDeductionService::syncTeamSitesInventory();

            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

            $syncedCount = 0;
            $books = Book::all();
            foreach ($books as $b) {
                $siteInv = SiteInventory::where('site_id', $mainSiteId)
                    ->where('book_id', $b->id)
                    ->first();

                if (!$siteInv || (int)$siteInv->quantity !== (int)$b->stock) {
                    SiteInventory::updateOrCreate(
                        ['site_id' => $mainSiteId, 'book_id' => $b->id],
                        ['quantity' => $b->stock]
                    );
                    $syncedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock successfully recalculated and synchronized across all items and sites!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to recalculate stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateIndexStockDirectly(Request $request, $indexId)
    {
        $request->validate([
            'action' => 'required|in:add,set',
            'quantity' => 'nullable|integer|min:0',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $index = \App\Models\BookIndex::findOrFail($indexId);
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            $mainWarehouseId = $mainWarehouse ? $mainWarehouse->id : 1;

            $mainSiteInv = SiteInventory::where('site_id', $mainWarehouseId)
                ->where('book_index_id', $index->id)
                ->first();

            $oldStock = $mainSiteInv ? (int)$mainSiteInv->quantity : (int)$index->stock;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;
            }

            $diff = $newStock - $oldStock;
            $index->stock = $newStock;
            $index->save();

            if ($diff != 0 && $index->book) {
                $book = $index->book;
                $book->stock = max(0, $book->stock - $diff);
                $book->save(); // Triggers BookObserver to update master book site_inventory
            }

            // Sync with Main Warehouse site inventory for index
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                $siteInv = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_index_id', $index->id)
                    ->first();
                if (!$siteInv) {
                    $siteInv = new SiteInventory();
                    $siteInv->site_id = $mainWarehouse->id;
                    $siteInv->book_index_id = $index->id;
                    $siteInv->book_id = null;
                    $siteInv->book_bundle_id = null;
                }
                $siteInv->quantity = $newStock;
                $siteInv->save();
            }

            // Create inventory transaction record for auditing
            $transaction = new InventoryTransaction();
            $transaction->book_id = $index->book_id;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = 'Main Warehouse';
            $transaction->source = 'Manual Index Adjustment';
            $transaction->notes = "Index: {$index->index_value}. " . ($request->action === 'add' 
                ? "Added {$request->quantity} units to Index Stock via inventory overview"
                : "Manually set Index Stock from {$oldStock} to {$newStock}");
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Index stock updated successfully',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update index stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBundleStockDirectly(Request $request, $bundleId)
    {
        $request->validate([
            'action' => 'required|in:add,set',
            'quantity' => 'nullable|integer|min:0',
            'new_stock' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $bundle = \App\Models\BookBundle::findOrFail($bundleId);
            $oldStock = $bundle->stock;
            $newStock = $oldStock;

            if ($request->action === 'add') {
                $quantity = $request->quantity ?? 0;
                $newStock = $oldStock + $quantity;
            } elseif ($request->action === 'set') {
                $newStock = $request->new_stock ?? $oldStock;
            }

            $bundle->stock = $newStock;
            $bundle->save();

            // Sync with Main Warehouse site inventory
            $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                $siteInv = SiteInventory::where('site_id', $mainWarehouse->id)
                    ->where('book_bundle_id', $bundle->id)
                    ->first();
                if (!$siteInv) {
                    $siteInv = new SiteInventory();
                    $siteInv->site_id = $mainWarehouse->id;
                    $siteInv->book_bundle_id = $bundle->id;
                    $siteInv->book_id = null;
                    $siteInv->book_index_id = null;
                }
                $siteInv->quantity = $newStock;
                $siteInv->save();
            }

            // Create inventory transaction record (book_id = null for bundle-level adjustment)
            $transaction = new InventoryTransaction();
            $transaction->book_id = null;
            $transaction->type = $newStock > $oldStock ? 'in' : ($newStock < $oldStock ? 'out' : 'adjustment');
            $transaction->quantity = abs($newStock - $oldStock);
            $transaction->location = 'Main Warehouse';
            $transaction->source = 'Manual Bundle Adjustment';
            $transaction->notes = "Bundle: {$bundle->name}. " . ($request->action === 'add' 
                ? "Added {$request->quantity} units to Bundle Stock via inventory overview"
                : "Manually set Bundle Stock from {$oldStock} to {$newStock}");
            $transaction->user_id = Auth::id();
            $transaction->transaction_date = now();
            $transaction->status = 'completed';
            $transaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bundle stock updated successfully',
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bundle stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function masterInventory(Request $request)
    {
        $categories = [
            'Raw Materials',
            'Finished Books',
            'Office Supplies',
            'Warehouse',
            'Bookstore',
            'Consignment',
            'Seasonals',
            'Imported Books',
            'Events',
            'Book Sales',
            'E-commerce',
        ];

        $rawMaterialSubcategories = [
            'Paper',
            'Ink',
            'Glue',
            'Packaging',
            'Other',
        ];

        $warehouseNames = [
            'Main Warehouse',
            'Bookstore Warehouse',
            'Area Sales Warehouse',
            'Consignment Warehouse',
            'Reserved Warehouse',
            'Book Sale Warehouse',
            'E-commerce Warehouse',
            'Damaged Stock Warehouse',
            'Returned Stock Warehouse',
            'In Transit Warehouse',
        ];

        // Ensure all 10 warehouses exist in Site table
        foreach ($warehouseNames as $name) {
            \App\Models\Site::firstOrCreate(
                ['name' => $name],
                [
                    'code' => 'WH-' . strtoupper(substr(str_replace(' ', '', $name), 0, 4)),
                    'description' => "{$name} Stock Storage",
                    'is_active' => true
                ]
            );
        }

        $warehouses = \App\Models\Site::whereIn('name', $warehouseNames)->get();

        $selectedCategory = $request->query('category', 'All');
        $selectedSubcategory = $request->query('subcategory', 'All');
        $search = $request->query('search');

        $query = \App\Models\InventoryCategoryItem::with(['warehouseStocks.site']);

        if ($selectedCategory && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }

        if ($selectedCategory === 'Raw Materials' && $selectedSubcategory && $selectedSubcategory !== 'All') {
            $query->where('subcategory', $selectedSubcategory);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('category')->orderBy('name')->get();

        // Calculate statistics
        $totalItems = $items->count();
        $totalStockUnits = 0;
        $totalValuation = 0;
        $lowStockCount = 0;
        $damagedStockCount = 0;

        $damagedWarehouse = $warehouses->where('name', 'Damaged Stock Warehouse')->first();
        $returnedWarehouse = $warehouses->where('name', 'Returned Stock Warehouse')->first();

        foreach ($items as $item) {
            $itemTotalStock = $item->warehouseStocks->sum('quantity');
            $totalStockUnits += $itemTotalStock;
            $totalValuation += ($itemTotalStock * (float)$item->unit_cost);

            if ($itemTotalStock <= $item->reorder_point) {
                $lowStockCount++;
            }

            if ($damagedWarehouse) {
                $damagedStockCount += $item->warehouseStocks->where('site_id', $damagedWarehouse->id)->sum('quantity');
            }
            if ($returnedWarehouse) {
                $damagedStockCount += $item->warehouseStocks->where('site_id', $returnedWarehouse->id)->sum('quantity');
            }
        }

        return view('production.inventory.master-inventory', [
            'title' => 'Production Master Inventory',
            'role' => auth()->user() ? auth()->user()->position : 'Staff',
            'sidebar' => 'production',
            'categories' => $categories,
            'rawMaterialSubcategories' => $rawMaterialSubcategories,
            'selectedCategory' => $selectedCategory,
            'selectedSubcategory' => $selectedSubcategory,
            'search' => $search,
            'warehouses' => $warehouses,
            'items' => $items,
            'metrics' => [
                'total_items' => $totalItems,
                'total_stock_units' => $totalStockUnits,
                'total_valuation' => $totalValuation,
                'low_stock_count' => $lowStockCount,
                'damaged_stock_count' => $damagedStockCount,
                'active_warehouses_count' => $warehouses->count(),
            ],
        ]);
    }

    public function storeInventoryCategoryItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'subcategory' => 'nullable|string',
            'unit_of_measure' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'reorder_point' => 'required|integer|min:0',
            'initial_warehouse_id' => 'required|exists:sites,id',
            'initial_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $sku = 'INV-' . strtoupper(substr($request->category, 0, 3)) . '-' . rand(10000, 99999);

        $item = \App\Models\InventoryCategoryItem::create([
            'sku' => $sku,
            'name' => $request->name,
            'category' => $request->category,
            'subcategory' => $request->category === 'Raw Materials' ? ($request->subcategory ?: 'Other') : null,
            'unit_of_measure' => $request->unit_of_measure,
            'unit_cost' => $request->unit_cost,
            'reorder_point' => $request->reorder_point,
            'description' => $request->description,
        ]);

        if ($request->initial_stock > 0) {
            \App\Models\WarehouseStockBalance::create([
                'site_id' => $request->initial_warehouse_id,
                'inventory_category_item_id' => $item->id,
                'quantity' => $request->initial_stock,
            ]);
        }

        return redirect()->back()->with('success', "Inventory Item '{$item->name}' created successfully!");
    }

    public function transferWarehouseStock(Request $request)
    {
        $request->validate([
            'inventory_category_item_id' => 'required|exists:inventory_category_items,id',
            'from_site_id' => 'required|exists:sites,id',
            'to_site_id' => 'required|exists:sites,id|different:from_site_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $fromStock = \App\Models\WarehouseStockBalance::where('site_id', $request->from_site_id)
            ->where('inventory_category_item_id', $request->inventory_category_item_id)
            ->first();

        if (!$fromStock || $fromStock->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Insufficient stock in source warehouse for transfer.');
        }

        $fromStock->quantity -= $request->quantity;
        $fromStock->save();

        $toStock = \App\Models\WarehouseStockBalance::firstOrCreate(
            [
                'site_id' => $request->to_site_id,
                'inventory_category_item_id' => $request->inventory_category_item_id,
            ],
            ['quantity' => 0]
        );

        $toStock->quantity += $request->quantity;
        $toStock->save();

        return redirect()->back()->with('success', 'Stock transferred between warehouses successfully!');
    }

    public function updateWarehouseStockDirectly(Request $request)
    {
        $request->validate([
            'inventory_category_item_id' => 'required|exists:inventory_category_items,id',
            'site_id' => 'required|exists:sites,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $stock = \App\Models\WarehouseStockBalance::firstOrCreate(
            [
                'site_id' => $request->site_id,
                'inventory_category_item_id' => $request->inventory_category_item_id,
            ],
            ['quantity' => 0]
        );

        $stock->quantity = $request->quantity;
        $stock->save();

        return redirect()->back()->with('success', 'Warehouse stock balance updated!');
    }
}

