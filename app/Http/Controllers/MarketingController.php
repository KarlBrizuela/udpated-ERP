<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\EmployeeCashAdvance;
use Illuminate\Support\Facades\Storage;

class MarketingController extends Controller
{
    public function dashboard()
    {
        $period = request()->query('period', 'monthly');
        
        switch ($period) {
            case 'daily':
                $start = \Carbon\Carbon::now()->startOfDay();
                $end = \Carbon\Carbon::now()->endOfDay();
                $periodLabel = 'Today';
                break;
            case 'weekly':
                $start = \Carbon\Carbon::now()->startOfWeek();
                $end = \Carbon\Carbon::now()->endOfWeek();
                $periodLabel = 'This Week';
                break;
            case 'yearly':
                $start = \Carbon\Carbon::now()->startOfYear();
                $end = \Carbon\Carbon::now()->endOfYear();
                $periodLabel = 'This Year';
                break;
            case 'monthly':
            default:
                $start = \Carbon\Carbon::now()->startOfMonth();
                $end = \Carbon\Carbon::now()->endOfMonth();
                $periodLabel = 'This Month';
                $period = 'monthly'; // normalize
                break;
        }

        // Filter helper query for valid sales orders (have proof of payment, or are ecom_direct, calculator_pos, or cash, excluding complimentary)
        $salesFilter = function($q) {
            $q->where('sales_orders.type', '!=', 'complimentary')
              ->where(function($sub) {
                $sub->whereNotNull('sales_orders.proof_of_payment')->where('sales_orders.proof_of_payment', '!=', '')
                   ->orWhere('sales_orders.type', 'ecom_direct')
                   ->orWhere('sales_orders.type', 'calculator_pos')
                   ->orWhere('sales_orders.payment_method', 'cash');
            });
        };

        // Orders in period (using effective invoice date if invoiced, otherwise order date)
        $baseOrdersQuery = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
            ->where($salesFilter)
            ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end]);

        $totalOrders = (int) $baseOrdersQuery->count();
        $totalSales = (float) $baseOrdersQuery->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)'));
        $avgOrder = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;

        // Sales by channel (platform) - Categorized into pos, so, nbs, e-com
        $subQuery = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
            ->select(
                \DB::raw("CASE 
                    WHEN sales_orders.type = 'calculator_pos' THEN 'pos'
                    WHEN sales_orders.type = 'ecom_direct' OR (sales_orders.ecom_platform IS NOT NULL AND sales_orders.ecom_platform != '') THEN 'e-com'
                    WHEN sales_orders.type = 'area_sales_consignment' THEN 'nbs'
                    ELSE 'so' 
                END as platform"),
                \DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount) as total_amount')
            )
            ->where($salesFilter)
            ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end]);

        $dbChannels = \DB::query()
            ->fromSub($subQuery, 'sub')
            ->select('platform', \DB::raw('COALESCE(SUM(total_amount),0) as total'))
            ->groupBy('platform')
            ->get()
            ->pluck('total', 'platform')
            ->toArray();

        $allCategories = [
            'pos' => 0.00,
            'so' => 0.00,
            'nbs' => 0.00,
            'e-com' => 0.00,
        ];

        foreach ($dbChannels as $plat => $total) {
            if (array_key_exists($plat, $allCategories)) {
                $allCategories[$plat] = (float) $total;
            } else {
                $allCategories['so'] += (float) $total;
            }
        }

        $channels = collect();
        foreach ($allCategories as $plat => $total) {
            $channels->push((object)[
                'platform' => $plat,
                'total' => $total
            ]);
        }
        $channels = $channels->sortByDesc('total')->values();
        $topChannel = $channels->first();

        // Chart categories and values
        $chartCategories = [];
        $chartRevenue = [];
        
        if ($period == 'daily') {
            // Hourly breakdown (0-23)
            for ($h = 0; $h < 24; $h++) {
                $chartCategories[] = $h == 0 ? '12 AM' : ($h < 12 ? $h . ' AM' : ($h == 12 ? '12 PM' : ($h - 12) . ' PM'));
                $chartRevenue[$h] = 0;
            }
            $rows = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->select(
                    \DB::raw('HOUR(COALESCE(sales_invoices.created_at, sales_orders.created_at)) as hour'),
                    \DB::raw('SUM(COALESCE(sales_invoices.total_amount, sales_orders.total_amount)) as total')
                )
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end])
                ->groupBy(\DB::raw('HOUR(COALESCE(sales_invoices.created_at, sales_orders.created_at))'))
                ->get();
            foreach ($rows as $r) {
                $chartRevenue[(int)$r->hour] = (float)$r->total;
            }
        } elseif ($period == 'weekly') {
            // Daily breakdown for week (Monday - Sunday)
            $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($weekDays as $day) {
                $chartCategories[] = $day;
                $chartRevenue[$day] = 0;
            }
            $rows = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->select(
                    \DB::raw('DAYNAME(COALESCE(sales_invoices.created_at, sales_orders.created_at)) as day'),
                    \DB::raw('SUM(COALESCE(sales_invoices.total_amount, sales_orders.total_amount)) as total')
                )
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end])
                ->groupBy(\DB::raw('DAYNAME(COALESCE(sales_invoices.created_at, sales_orders.created_at))'))
                ->get();
            foreach ($rows as $r) {
                $chartRevenue[$r->day] = (float)$r->total;
            }
            $chartRevenue = array_values($chartRevenue);
        } elseif ($period == 'yearly') {
            // Monthly breakdown for year (Jan - Dec)
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            foreach ($months as $month) {
                $chartCategories[] = substr($month, 0, 3); // Jan, Feb...
                $chartRevenue[$month] = 0;
            }
            $rows = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->select(
                    \DB::raw('MONTHNAME(COALESCE(sales_invoices.created_at, sales_orders.created_at)) as month'),
                    \DB::raw('SUM(COALESCE(sales_invoices.total_amount, sales_orders.total_amount)) as total')
                )
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end])
                ->groupBy(\DB::raw('MONTHNAME(COALESCE(sales_invoices.created_at, sales_orders.created_at))'))
                ->get();
            foreach ($rows as $r) {
                $chartRevenue[$r->month] = (float)$r->total;
            }
            $chartRevenue = array_values($chartRevenue);
        } else {
            // Monthly breakdown (daily 1-31)
            $daysInMonth = $start->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $chartCategories[] = (string)$d;
                $chartRevenue[$d] = 0;
            }
            $rows = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->select(
                    \DB::raw('DAY(COALESCE(sales_invoices.created_at, sales_orders.created_at)) as day'),
                    \DB::raw('SUM(COALESCE(sales_invoices.total_amount, sales_orders.total_amount)) as total')
                )
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end])
                ->groupBy(\DB::raw('DAY(COALESCE(sales_invoices.created_at, sales_orders.created_at))'))
                ->get();
            foreach ($rows as $r) {
                $chartRevenue[(int)$r->day] = (float)$r->total;
            }
            $chartRevenue = array_values($chartRevenue);
        }

        // Top products (filtered by same period and paid filter)
        $topProducts = \App\Models\SalesOrderItem::select('sales_order_items.book_id', \DB::raw('SUM(sales_order_items.quantity) as qty'))
            ->whereHas('order', function($query) use ($start, $end, $salesFilter) {
                $query->leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                    ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$start, $end])
                    ->where($salesFilter);
            })
            ->groupBy('sales_order_items.book_id')
            ->orderByDesc('qty')
            ->with('book')
            ->take(5)
            ->get();

        return view('marketing.dashboard', [
            'title' => 'Marketing Dashboard',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'period' => $period,
            'periodLabel' => $periodLabel,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'avgOrder' => $avgOrder,
            'topChannel' => $topChannel,
            'channels' => $channels,
            'chartCategories' => $chartCategories,
            'chartRevenue' => $chartRevenue,
            'topProducts' => $topProducts,
        ]);
    }

    public function approvalQueue()
    {
        // 1. Pending Department Approvals (Marketing Manager needs to approve these)
        $salesOrders = \App\Models\SalesOrder::with(['customer', 'preparedBy', 'items.book', 'items.bookIndex.book', 'items.bundle'])
            ->where('status', 'pending_mkt_approval')
            ->latest()
            ->get();

        // 2. Pending Cash Advances (Only Marketing Manager or Super Admin)
        $user = auth()->user();
        $isAuthorized = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';
        
        $pendingCashAdvances = $isAuthorized 
            ? EmployeeCashAdvance::where('status', 'pending_supervisor_approval')
                ->where('department_source', 'Marketing')
                ->latest()
                ->get()
            : collect();

        $pendingMaterials = $isAuthorized
            ? \App\Models\Admin\MIS\MaterialReq::with('user')
                ->where('status', 'pending_supervisor_approval')
                ->get()
                ->filter(function ($request) use ($user) {
                    return $request->canBeApprovedBy($user);
                })
            : collect();

        // 3. Pending Stock Transfers (Marketing Manager approves Marketing-origin requests)
        $batchData = [];  // keyed by first-transfer->id: ['items'=>[], 'total_quantity'=>N, 'items_count'=>N]

        $pendingTransfers = $isAuthorized
            ? \App\Models\StockTransfer::with('fromSite', 'toSite', 'book', 'bookIndex.book', 'bookBundle', 'createdBy')
                ->where('status', 'pending')
                ->where('approval_division', 'Marketing')
                ->latest()
                ->get()
                ->groupBy(function ($item) {
                    return $item->batch_id ?: ('single_' . $item->id);
                })
                ->map(function ($items) use (&$batchData) {
                    $first = $items->first();
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
                ->values()
            : collect();

        $pendingCctvRequests = $isAuthorized
            ? \App\Models\Admin\MIS\CCTVReq::with('user')
                ->where('status', 'pending approval')
                ->whereHas('user', function ($query) {
                    $query->where('division', 'like', '%Marketing%')
                        ->orWhereHas('divisions', function ($divisionQuery) {
                            $divisionQuery->where('division', 'like', '%Marketing%');
                        });
                })
                ->latest()
                ->get()
            : collect();

        $pendingTeamStockTransfers = \App\Models\TeamStockTransfer::with(['transferredByUser', 'items.book', 'items.bookIndex.book', 'items.bookBundle'])
            ->where('status', 'pending_mkt_approval')
            ->latest()
            ->get();
        


        // 2. My Activity - My Submissions
        $soSubmissions = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('prepared_by', auth()->id())
            ->latest()
            ->get();

        $caSubmissions = EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();

        $mySubmissions = collect();
        foreach($soSubmissions as $so) {
            $mySubmissions->push((object)[
                'type' => 'Sales Order',
                'id' => $so->id,
                'reference_no' => $so->so_number,
                'submitted_date' => $so->created_at,
                'amount' => $so->total_amount,
                'status' => $so->status,
                'url' => route('marketing.sales-orders.detail', $so->id),
                'original' => $so
            ]);
        }
        foreach($caSubmissions as $ca) {
            $mySubmissions->push((object)[
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 4, '0', STR_PAD_LEFT),
                'submitted_date' => $ca->created_at,
                'prep_name' => auth()->user()->name,
                'amount' => $ca->amount,
                'status' => $ca->status,
                'original' => $ca
            ]);
        }

        $materialSubmissions = \App\Models\Admin\MIS\MaterialReq::where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($materialSubmissions as $req) {
            $mySubmissions->push((object)[
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
                'submitted_date' => $req->created_at,
                'prep_name' => auth()->user()->name,
                'amount' => $req->amount,
                'status' => $req->status,
                'original' => $req
            ]);
        }
        $mySubmissions = $mySubmissions->sortByDesc('submitted_date');

        // 3. My Approved Requests (Requests this manager has already approved)
        $caApproved = EmployeeCashAdvance::where('approved_by_manager', auth()->id())
            ->latest()
            ->get();
        
        $materialApproved = \App\Models\Admin\MIS\MaterialReq::where('approved_by_manager', auth()->id())
            ->latest()
            ->get();
        
        $myApprovedRequests = collect();
        foreach($caApproved as $ca) {
            $myApprovedRequests->push((object)[
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 4, '0', STR_PAD_LEFT),
                'submitted_by' => $ca->employee_name,
                'submitted_date' => $ca->created_at,
                'amount' => $ca->amount,
                'status' => $ca->status,
                'original' => $ca
            ]);
        }

        foreach ($materialApproved as $req) {
            $myApprovedRequests->push((object)[
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
                'submitted_by' => $req->user->name ?? $req->requested_by,
                'submitted_date' => $req->created_at,
                'amount' => $req->amount,
                'status' => $req->status,
                'original' => $req
            ]);
        }

        return view('marketing.approval-queue', [
            'title' => 'Approval Queue',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'salesOrders' => $salesOrders,
            'pendingCashAdvances' => $pendingCashAdvances,
            'pendingTransfers' => $pendingTransfers,
            'batchData' => $batchData,
            'pendingCctvRequests' => $pendingCctvRequests,
            'pendingMaterials' => $pendingMaterials,
            'pendingTeamStockTransfers' => $pendingTeamStockTransfers,
            'mySubmissions' => $mySubmissions,
            'myApprovedRequests' => $myApprovedRequests->sortByDesc('submitted_date')
        ]);
    }

    public function myRequests()
    {
        $cashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();
        $materialRequests = \App\Models\Admin\MIS\MaterialReq::where('user_id', auth()->id())
            ->latest()
            ->get();
        $cctvRequests = \App\Models\Admin\MIS\CCTVReq::where('user_id', auth()->id())
            ->latest()
            ->get();

        $mergedRequests = $cashAdvances->concat($materialRequests)->sortByDesc('created_at');

        return view('marketing.my-requests.index', [
            'title' => '',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'cashAdvances' => $mergedRequests,
            'cctvRequests' => $cctvRequests,
        ]);
    }


    public function products(Request $request)
    {
        $search = $request->input('search');
        
        $query = Book::where('is_book', true)
            ->with(['product', 'bookCategory', 'bookSubCategory'])
            ->withSum('inventory as stock', 'quantity')
            ->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }

        $books = $query->paginate(15, ['*'], 'books_page')->withQueryString();
        $categories = BookCategory::whereNull('parent_id')->orderBy('name', 'asc')->get();

        return view('marketing.book-list', [
            'books' => $books,
            'categories' => $categories,
            'title' => 'Book List (Master Registry)',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'search' => $search
        ]);
    }

    public function nonBooks(Request $request)
    {
        $search = $request->input('search');
        
        $query = Book::where('is_book', false)
            ->with(['product', 'bookCategory', 'bookSubCategory'])
            ->withSum('inventory as stock', 'quantity')
            ->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }

        $books = $query->paginate(15, ['*'], 'books_page')->withQueryString();
        $categories = BookCategory::whereNull('parent_id')->orderBy('name', 'asc')->get();

        return view('marketing.non-books', [
            'books' => $books,
            'categories' => $categories,
            'title' => 'Non-Books (Master Registry)',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'search' => $search
        ]);
    }

    public function searchBooks(Request $request)
    {
        $q = trim($request->input('q', ''));
        $excludeIds = $request->input('exclude_ids', []);

        $query = Book::select('id', 'name', 'price', 'sku')
            ->orderBy('name', 'asc');

        if (!empty($q)) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        if (!empty($excludeIds) && is_array($excludeIds)) {
            $query->whereNotIn('id', array_filter($excludeIds));
        }

        $books = $query->limit(30)->get();

        $results = $books->map(function($b) {
            return [
                'id' => $b->id,
                'text' => $b->name . ' (₱' . number_format((float)$b->price, 2) . ')'
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function bundles(Request $request)
    {
        $bundleSearch = $request->input('bundle_search');

        $bundleQuery = \App\Models\BookBundle::with('books')->orderBy('created_at', 'desc');
        if (!empty($bundleSearch)) {
            $bundleQuery->where(function($q) use ($bundleSearch) {
                $q->where('name', 'like', '%' . $bundleSearch . '%')
                  ->orWhere('sku', 'like', '%' . $bundleSearch . '%');
            });
        }
        $bundles = $bundleQuery->paginate(15, ['*'], 'bundles_page')->withQueryString();

        return view('marketing.book-bundles', [
            'bundles' => $bundles,
            'allBooks' => [],
            'title' => 'Book Bundles Registry',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'bundleSearch' => $bundleSearch
        ]);
    }

    public function bookIndices(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\BookIndex::with('book')->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('index_value', 'like', '%' . $search . '%')
                  ->orWhere('article', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%')
                  ->orWhere('nbs_barcode', 'like', '%' . $search . '%')
                  ->orWhereHas('book', function($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%')
                         ->orWhere('article', 'like', '%' . $search . '%')
                         ->orWhere('barcode', 'like', '%' . $search . '%')
                         ->orWhere('nbs_barcode', 'like', '%' . $search . '%');
                  });
            });
        }

        $indices = $query->paginate(15, ['*'], 'indices_page')->withQueryString();
        $allBooks = Book::orderBy('name', 'asc')->get();

        return view('marketing.book-indices', [
            'indices' => $indices,
            'allBooks' => $allBooks,
            'title' => 'Book Indices Registry',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'search' => $search
        ]);
    }

    public function storeIndex(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'index_value' => 'required|string|max:255',
            'custom_name' => 'nullable|string|max:255',
            'article' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'nbs_barcode' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'mibf_price' => 'nullable|numeric|min:0',
        ]);

        \DB::transaction(function() use ($validated) {
            $index = \App\Models\BookIndex::create($validated);
            $indexStock = (int)$validated['stock'];

            if ($indexStock > 0) {
                $book = \App\Models\Book::find($validated['book_id']);
                if ($book) {
                    // Deduct stock from master Book. $book->save() triggers BookObserver which automatically updates master book site_inventory quantity.
                    $book->stock = max(0, $book->stock - $indexStock);
                    $book->save();
                }
            }

            // Create/update site inventory for the index
            $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                \App\Models\SiteInventory::updateOrCreate(
                    [
                        'site_id' => $mainWarehouse->id,
                        'book_index_id' => $index->id,
                    ],
                    [
                        'book_id' => null,
                        'book_bundle_id' => null,
                        'quantity' => $indexStock,
                    ]
                );
            }
        });

        return response()->json(['message' => 'Book Index mapping created successfully and stock transferred']);
    }

    public function editIndex($id)
    {
        $index = \App\Models\BookIndex::with('book')->findOrFail($id);
        return response()->json($index);
    }

    public function updateIndex(Request $request, $id)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'index_value' => 'required|string|max:255',
            'custom_name' => 'nullable|string|max:255',
            'article' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'nbs_barcode' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'mibf_price' => 'nullable|numeric|min:0',
        ]);

        \DB::transaction(function() use ($validated, $id) {
            $index = \App\Models\BookIndex::findOrFail($id);
            $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
            $mainWarehouseId = $mainWarehouse ? $mainWarehouse->id : 1;

            $mainSiteInv = \App\Models\SiteInventory::where('site_id', $mainWarehouseId)
                ->where('book_index_id', $index->id)
                ->first();

            $oldStock = $mainSiteInv ? (int)$mainSiteInv->quantity : (int)$index->stock;
            $newStock = (int)$validated['stock'];
            $diff = $newStock - $oldStock;

            $index->update($validated);

            if ($diff != 0 && $index->book) {
                $book = $index->book;
                $book->stock = max(0, $book->stock - $diff);
                $book->save(); // Triggers BookObserver to sync master book site_inventory quantity
            }

            $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
            if ($mainWarehouse) {
                \App\Models\SiteInventory::updateOrCreate(
                    [
                        'site_id' => $mainWarehouse->id,
                        'book_index_id' => $index->id,
                    ],
                    [
                        'book_id' => null,
                        'book_bundle_id' => null,
                        'quantity' => $newStock,
                    ]
                );
            }
        });

        return response()->json(['message' => 'Book Index mapping updated successfully']);
    }

    public function destroyIndex($id)
    {
        \DB::transaction(function() use ($id) {
            $index = \App\Models\BookIndex::findOrFail($id);
            $stockToReturn = (int)$index->stock;

            if ($stockToReturn > 0 && $index->book) {
                $book = $index->book;
                $book->stock += $stockToReturn;
                $book->save(); // Triggers BookObserver to sync master book site_inventory quantity
            }

            // Disassociate from stock transfers so foreign key constraint doesn't block deletion
            \App\Models\StockTransfer::where('book_index_id', $index->id)->update(['book_index_id' => null]);
            \App\Models\SiteInventory::where('book_index_id', $index->id)->delete();
            $index->delete();
        });

        return response()->json(['message' => 'Book Index mapping deleted successfully and stock returned to book']);
    }


    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'price' => 'nullable|numeric',
            'category' => 'nullable',
            'sales_description' => 'nullable',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'nullable',
        ]);

        // Check if already listed
        if (Product::where('book_id', $request->book_id)->exists()) {
            return response()->json(['error' => 'This book is already listed on the POS'], 422);
        }

        $book = Book::findOrFail($request->book_id);
        
        $productData = [
            'book_id' => $book->id,
            'name' => $book->name, // Keep a copy for easier searching or can be different
            'price' => $request->price ?? 0,
            'category' => $request->category,
            'sales_description' => $request->sales_description,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image_file')) {
        $path = $request->file('image_file')->store('products', 'public');
        $productData['image'] = $path;
    } elseif ($book->image) {
        // Use the book's existing image if no new one is provided
        $productData['image'] = $book->image;
    }

    Product::create($productData);

        return response()->json(['message' => 'Book successfully listed as a POS product']);
    }

    public function checkSku(Request $request)
    {
        $sku = trim((string)$request->query('sku'));
        $excludeId = $request->query('exclude_id');

        if ($sku === '') {
            return response()->json(['exists' => false]);
        }

        $query = Book::withTrashed()->where('sku', $sku);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:books,sku', // Validates against Books table
            'barcode' => 'nullable|string',
            'nbs_barcode' => 'nullable|string',
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'sub_category' => 'nullable|string',
            'size' => 'nullable|string',
            'pages' => 'nullable|integer',
            'cover_type' => 'nullable|string',
            'book_type' => 'nullable|string',
            'copyright' => 'nullable|string',
            'weight' => 'nullable|string',
            'stock' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'max_stock' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'mibf_price' => 'nullable|numeric|min:0',
            'shelf_number' => 'nullable|string|max:50',
            'rack_number' => 'nullable|string|max:50',
            'category' => 'nullable|string',
            'category_id' => 'nullable|exists:book_categories,id',
            'sub_category_id' => 'nullable|exists:book_categories,id',
            'purchase_description' => 'nullable',
            'item_code' => 'nullable|string|unique:books,item_code',
            'email' => 'nullable|email',
            'contact_number' => 'nullable|string',
            'royalty' => 'nullable|string',
            'article' => 'nullable|string',
            'cogs_account' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        // Explicitly handle empty strings for unique or nullable fields
        if (empty($validated['item_code'])) {
            $validated['item_code'] = null;
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = null;
        }

        // Set defaults for nullable fields that are not nullable in DB
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['reorder_point'] = $validated['reorder_point'] ?? 0;
        $validated['max_stock'] = $validated['max_stock'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['pages'] = $validated['pages'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        Book::create($validated);

        return response()->json(['message' => 'Book added to Master Registry']);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Books Template');

        $headers = [
            '', // Column A (empty)
            'BOOK TITLE',
            'SKU/CATAGLOG #',
            'ITEM CODE',
            'BARCODE/ISBN',
            'SELLING PRICE',
            'AUTHOR',
            'PUBLISHER',
            'SIZE(LXW)',
            'WEIGHT',
            'PAGES',
            'COVER TYPE',
            'CLASSIFICATION',
            'COPYRIGHT',
            'UNIT COST',
            'CATEGORY',
            'SUB-CATEGORY',
            'ARTILE',
            'ROYALTY',
            'EMAIL',
            'NBS BARCODE'
        ];

        // Write Headers
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Write Sample Data matching user's template Row 2
        $sampleData = [
            '', // Column A
            'Advent Arts and Christmas Crafts : With Prayers and Rituals for Family, School and Church',
            '978-8809125860',
            '', // ITEM CODE (Leave blank to generate automatically)
            '9788809125860',
            255.00,
            'Joanna Rotberg',
            'PAULIST PRESS',
            '11 x 8.500 x .250',
            '230',
            0,
            'Paper',
            'Foreign Book',
            '2020',
            0.00,
            'Pastoral',
            'Liturgy',
            '', // ARTILE
            '', // ROYALTY
            '', // EMAIL
            ''  // NBS BARCODE
        ];

        foreach ($sampleData as $colIndex => $value) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '2', $value);
        }

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9251C'], // Claretian Red color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:U1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Auto-fit column widths
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            if ($colIndex === 0) {
                $sheet->getColumnDimension('A')->setWidth(5);
            } else {
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="book_import_template.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function importBooks(Request $request)
    {
        // Increase time and memory limits for processing large files
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        try {
            $file = $request->file('excel_file');
            // Use setReadDataOnly(true) to significantly reduce memory usage of PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error reading spreadsheet file: ' . $e->getMessage()], 422);
        }

        if (empty($rows) || count($rows) < 2) {
            return response()->json(['error' => 'The uploaded file contains no data rows.'], 422);
        }

        // Headers cleaning & mapping
        $headers = array_map(function($h) {
            return strtolower(trim(str_replace("\ufeff", '', $h ?? '')));
        }, $rows[0]);

        $findHeader = function($keys, $headers) {
            foreach ($keys as $key) {
                $idx = array_search(strtolower(trim($key)), $headers);
                if ($idx !== false) {
                    return $idx;
                }
            }
            return false;
        };

        // Define column maps with fallbacks
        $colMap = [
            'name' => $findHeader(['book title', 'name', 'title'], $headers),
            'sku' => $findHeader(['sku/cataglog #', 'sku/cataglog', 'cataglog #', 'sku/catalog #', 'sku/catalog', 'sku', 'catalog #'], $headers),
            'item_code' => $findHeader(['item code', 'item_code'], $headers),
            'barcode' => $findHeader(['barcode/isbn', 'barcode', 'isbn'], $headers),
            'price' => $findHeader(['selling price', 'price'], $headers),
            'author' => $findHeader(['author'], $headers),
            'publisher' => $findHeader(['publisher'], $headers),
            'size' => $findHeader(['size(lxw)', 'size', 'size(l x w)'], $headers),
            'weight' => $findHeader(['weight'], $headers),
            'pages' => $findHeader(['pages'], $headers),
            'cover_type' => $findHeader(['cover type', 'cover_type'], $headers),
            'book_type' => $findHeader(['classification', 'book type', 'book_type', 'book-type'], $headers),
            'copyright' => $findHeader(['copyright'], $headers),
            'cost' => $findHeader(['unit cost', 'cost'], $headers),
            'category' => $findHeader(['category'], $headers),
            'sub_category' => $findHeader(['sub-category', 'sub category', 'sub_category'], $headers),
            'article' => $findHeader(['artile', 'article'], $headers),
            'royalty' => $findHeader(['royalty'], $headers),
            'email' => $findHeader(['email'], $headers),
            'nbs_barcode' => $findHeader(['nbs barcode', 'nbs_barcode'], $headers),
            'stock' => $findHeader(['stock'], $headers),
            'shelf_number' => $findHeader(['shelf number', 'shelf_number'], $headers),
            'rack_number' => $findHeader(['rack number', 'rack_number'], $headers),
            'reorder_point' => $findHeader(['reorder point', 'reorder_point'], $headers),
            'max_stock' => $findHeader(['max stock', 'max_stock'], $headers),
            'purchase_description' => $findHeader(['purchase description', 'purchase_description'], $headers),
            'contact_number' => $findHeader(['contact number', 'contact_number'], $headers),
            'cogs_account' => $findHeader(['cogs account', 'cogs_account'], $headers),
        ];

        // Book Title is required
        if ($colMap['name'] === false) {
            return response()->json(['error' => 'Critical column "Book Title" is missing in the Excel sheet.'], 422);
        }

        // Pre-scan file to collect all SKUs and Barcodes for bulk database queries
        $skusInSheet = [];
        $barcodesInSheet = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row, function($cell) { return !is_null($cell) && trim((string)$cell) !== ''; }))) {
                continue;
            }
            $sku = $colMap['sku'] !== false ? trim((string)($row[$colMap['sku']] ?? '')) : '';
            if (!empty($sku)) {
                $skusInSheet[] = $sku;
            }
            $barcode = $colMap['barcode'] !== false ? trim((string)($row[$colMap['barcode']] ?? '')) : '';
            if (!empty($barcode)) {
                $barcodesInSheet[] = $barcode;
            }
        }

        // Fetch existing books by SKU and Barcode in chunks
        $existingBooksBySku = [];
        if (!empty($skusInSheet)) {
            foreach (array_chunk(array_unique($skusInSheet), 1000) as $chunk) {
                $booksChunk = Book::withTrashed()->whereIn('sku', $chunk)->get();
                foreach ($booksChunk as $book) {
                    $existingBooksBySku[$book->sku] = $book;
                }
            }
        }

        $existingBooksByBarcode = [];
        if (!empty($barcodesInSheet)) {
            foreach (array_chunk(array_unique($barcodesInSheet), 1000) as $chunk) {
                $booksChunk = Book::whereIn('barcode', $chunk)->get();
                foreach ($booksChunk as $book) {
                    $existingBooksByBarcode[$book->barcode] = $book;
                }
            }
        }

        // Load all existing categories to memory mapping
        $categories = BookCategory::all();
        $categoryMap = [];
        $subCategoryMap = [];
        foreach ($categories as $cat) {
            if (is_null($cat->parent_id)) {
                $categoryMap[strtolower(trim($cat->name))] = $cat;
            } else {
                $subCategoryMap[$cat->parent_id][strtolower(trim($cat->name))] = $cat;
            }
        }

        // Pre-fetch existing SKU prefixes for query-free autoincrement generation
        $existingSkuPrefixes = Book::withTrashed()
            ->where('sku', 'like', 'SKU-%')
            ->pluck('sku')
            ->toArray();
        $existingSkuPrefixesMap = array_flip($existingSkuPrefixes);

        $skuAutoIncrement = (Book::withTrashed()->max('id') ?? 0) + 1;
        $createdCount = 0;
        $updatedCount = 0;
        $errors = [];
        $processedBarcodes = [];
        $processedSkus = [];

        \DB::beginTransaction();

        try {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                // Check if row is entirely empty
                if (empty(array_filter($row, function($cell) { return !is_null($cell) && trim((string)$cell) !== ''; }))) {
                    continue;
                }

                $rowNum = $i + 1;

                $sku = $colMap['sku'] !== false ? trim((string)($row[$colMap['sku']] ?? '')) : '';
                $name = trim((string)($row[$colMap['name']] ?? ''));

                $isAutoSku = false;
                if (empty($sku)) {
                    $isAutoSku = true;
                    do {
                        $sku = 'SKU-' . str_pad($skuAutoIncrement, 5, '0', STR_PAD_LEFT);
                        $skuAutoIncrement++;
                    } while (isset($existingSkuPrefixesMap[$sku]) || isset($existingBooksBySku[$sku]) || isset($processedSkus[$sku]));
                }

                $hasSkuError = false;
                if (!$isAutoSku) {
                    if (isset($existingBooksBySku[$sku])) {
                        $errors[] = "Row {$rowNum}: SKU \"{$sku}\" already exists in the database.";
                        $hasSkuError = true;
                    }
                    if (isset($processedSkus[$sku])) {
                        $errors[] = "Row {$rowNum}: SKU \"{$sku}\" is duplicated in the uploaded spreadsheet (previously seen on Row {$processedSkus[$sku]}).";
                        $hasSkuError = true;
                    }
                }

                $processedSkus[$sku] = $rowNum;

                if ($hasSkuError) {
                    continue;
                }

                if (empty($name)) {
                    $errors[] = "Row {$rowNum}: Book Title is required.";
                    continue;
                }

                $data = [
                    'sku' => $sku,
                    'name' => $name,
                    'item_code' => $colMap['item_code'] !== false ? trim((string)($row[$colMap['item_code']] ?? '')) : null,
                    'author' => $colMap['author'] !== false ? trim((string)($row[$colMap['author']] ?? '')) : null,
                    'publisher' => $colMap['publisher'] !== false ? trim((string)($row[$colMap['publisher']] ?? '')) : null,
                    'copyright' => $colMap['copyright'] !== false ? trim((string)($row[$colMap['copyright']] ?? '')) : null,
                    'book_type' => $colMap['book_type'] !== false ? trim((string)($row[$colMap['book_type']] ?? '')) : null,
                    'cover_type' => $colMap['cover_type'] !== false ? trim((string)($row[$colMap['cover_type']] ?? '')) : null,
                    'pages' => $colMap['pages'] !== false ? (int)($row[$colMap['pages']] ?? 0) : 0,
                    'size' => $colMap['size'] !== false ? trim((string)($row[$colMap['size']] ?? '')) : null,
                    'weight' => $colMap['weight'] !== false ? trim((string)($row[$colMap['weight']] ?? '')) : null,
                    'stock' => $colMap['stock'] !== false ? (int)($row[$colMap['stock']] ?? 0) : 0,
                    'cost' => $colMap['cost'] !== false ? (float)($row[$colMap['cost']] ?? 0) : 0.0,
                    'price' => $colMap['price'] !== false ? (float)($row[$colMap['price']] ?? 0) : 0.0,
                    'reorder_point' => $colMap['reorder_point'] !== false ? (int)($row[$colMap['reorder_point']] ?? 0) : 0,
                    'max_stock' => $colMap['max_stock'] !== false ? (int)($row[$colMap['max_stock']] ?? 0) : 0,
                    'shelf_number' => $colMap['shelf_number'] !== false ? trim((string)($row[$colMap['shelf_number']] ?? '')) : null,
                    'rack_number' => $colMap['rack_number'] !== false ? trim((string)($row[$colMap['rack_number']] ?? '')) : null,
                    'barcode' => $colMap['barcode'] !== false ? trim((string)($row[$colMap['barcode']] ?? '')) : null,
                    'nbs_barcode' => $colMap['nbs_barcode'] !== false ? trim((string)($row[$colMap['nbs_barcode']] ?? '')) : null,
                    'purchase_description' => $colMap['purchase_description'] !== false ? trim((string)($row[$colMap['purchase_description']] ?? '')) : null,
                    'article' => $colMap['article'] !== false ? trim((string)($row[$colMap['article']] ?? '')) : null,
                    'royalty' => $colMap['royalty'] !== false ? trim((string)($row[$colMap['royalty']] ?? '')) : null,
                    'email' => $colMap['email'] !== false ? trim((string)($row[$colMap['email']] ?? '')) : null,
                    'contact_number' => $colMap['contact_number'] !== false ? trim((string)($row[$colMap['contact_number']] ?? '')) : null,
                    'cogs_account' => $colMap['cogs_account'] !== false ? trim((string)($row[$colMap['cogs_account']] ?? '')) : null,
                    'unit' => 'pcs',
                    'is_active' => true,
                    'is_book' => $request->has('is_book') ? filter_var($request->input('is_book'), FILTER_VALIDATE_BOOLEAN) : true,
                ];

                if (empty($data['item_code'])) $data['item_code'] = null;
                if (empty($data['barcode'])) $data['barcode'] = null;
                if (empty($data['nbs_barcode'])) $data['nbs_barcode'] = null;

                // Handle categories on the fly
                $categoryName = $colMap['category'] !== false ? trim((string)($row[$colMap['category']] ?? '')) : '';
                $subCategoryName = $colMap['sub_category'] !== false ? trim((string)($row[$colMap['sub_category']] ?? '')) : '';

                if (!empty($categoryName)) {
                    $categoryKey = strtolower(trim($categoryName));
                    $category = $categoryMap[$categoryKey] ?? null;
                    if (!$category) {
                        $category = BookCategory::create([
                            'name' => $categoryName,
                            'parent_id' => null
                        ]);
                        $categoryMap[$categoryKey] = $category;
                    }
                    $data['category'] = $categoryName;
                    $data['category_id'] = $category->id;

                    if (!empty($subCategoryName)) {
                        $subCategoryKey = strtolower(trim($subCategoryName));
                        $subCategory = ($subCategoryMap[$category->id] ?? [])[$subCategoryKey] ?? null;
                        if (!$subCategory) {
                            $subCategory = BookCategory::create([
                                'name' => $subCategoryName,
                                'parent_id' => $category->id
                            ]);
                            $subCategoryMap[$category->id][$subCategoryKey] = $subCategory;
                        }
                        $data['sub_category'] = $subCategoryName;
                        $data['sub_category_id'] = $subCategory->id;
                    }
                }

                // Check uniqueness constraints other than SKU (barcode)
                if (!empty($data['barcode'])) {
                    $conflict = $existingBooksByBarcode[$data['barcode']] ?? null;
                    if ($conflict && $conflict->sku !== $sku) {
                        $errors[] = "Row {$rowNum}: Barcode \"{$data['barcode']}\" already exists for book with SKU \"{$conflict->sku}\".";
                        continue;
                    }
                    if (isset($processedBarcodes[$data['barcode']]) && $processedBarcodes[$data['barcode']] !== $sku) {
                        $errors[] = "Row {$rowNum}: Barcode \"{$data['barcode']}\" is duplicated in the uploaded spreadsheet.";
                        continue;
                    }
                    $processedBarcodes[$data['barcode']] = $sku;
                }

                $book = $existingBooksBySku[$sku] ?? null;
                if ($book) {
                    $book->update($data);
                    $updatedCount++;
                } else {
                    $newBook = Book::create($data);
                    $existingBooksBySku[$sku] = $newBook;
                    if (!empty($data['barcode'])) {
                        $existingBooksByBarcode[$data['barcode']] = $newBook;
                    }
                    $createdCount++;
                }
            }

            if (!empty($errors)) {
                \DB::rollBack();
                return response()->json([
                    'error' => 'Import failed due to row errors. No changes were saved.',
                    'details' => $errors
                ], 422);
            }

            \DB::commit();
        } catch (\Throwable $e) {
            if (\DB::transactionLevel() > 0) {
                \DB::rollBack();
            }
            return response()->json(['error' => 'An error occurred during import: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Import completed successfully.',
            'created' => $createdCount,
            'updated' => $updatedCount
        ]);
    }

    public function importNonBooks(Request $request)
    {
        $request->merge(['is_book' => false]);
        return $this->importBooks($request);
    }

    public function editProduct($id)
    {
        $product = Product::with('book')->findOrFail($id);
        return response()->json($product);
    }

    public function editBook($id)
    {
        $book = Book::findOrFail($id);
        return response()->json($book);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'price' => 'nullable|numeric',
            'category' => 'nullable',
            'sales_description' => 'nullable',
            'asset_account' => 'nullable',
            'is_active' => 'nullable',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image_file')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return response()->json(['message' => 'POS Listing updated successfully']);
    }

    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:books,sku,' . $id,
            'barcode' => 'nullable',
            'nbs_barcode' => 'nullable|string',
            'author' => 'nullable',
            'publisher' => 'nullable',
            'size' => 'nullable',
            'pages' => 'nullable|integer',
            'unit' => 'nullable',
            'copyright' => 'nullable',
            'book_type' => 'nullable',
            'weight' => 'nullable',
            'cover_type' => 'nullable',
            'royalty' => 'nullable',
            'article' => 'nullable',
            'sub_category' => 'nullable',
            'email' => 'nullable|email',
            'contact_number' => 'nullable',
            'stock' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'max_stock' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'cogs_account' => 'nullable',
            'purchase_description' => 'nullable',
            'price' => 'nullable|numeric',
            'mibf_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string',
            'category_id' => 'nullable|exists:book_categories,id',
            'sub_category_id' => 'nullable|exists:book_categories,id',
            'item_code' => 'nullable|string|unique:books,item_code,' . $id,
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Explicitly handle empty strings
        if (empty($validated['item_code'])) {
            $validated['item_code'] = null;
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = null;
        }

        // Set defaults for nullable fields
        $validated['stock'] = $validated['stock'] ?? $book->stock;
        $validated['reorder_point'] = $validated['reorder_point'] ?? 0;
        $validated['max_stock'] = $validated['max_stock'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['pages'] = $validated['pages'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated);

        return response()->json(['message' => 'Master Book entry updated successfully']);
    }

    // Area Sales
    public function salesOrdersList(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('type');
        $statusFilter = $request->input('status');

        $query = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', '!=', 'foreign')
            ->where('so_number', 'not like', 'FORD-SO-%');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('ecom_platform', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($typeFilter && $typeFilter !== 'all') {
            if ($typeFilter === 'area_sales_consignment') {
                $query->whereIn('type', ['area_sales_consignment', 'area_consignment']);
            } elseif ($typeFilter === 'mibf') {
                $query->where(function($q) {
                    $q->where('platform', 'MIBF')
                      ->orWhere('ecom_platform', 'MIBF')
                      ->orWhere('type', 'mibf')
                      ->orWhere('so_number', 'like', 'MIBF-%');
                });
            } else {
                $query->where('type', $typeFilter);
            }
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('marketing.sales-orders.list', [
            'title' => 'Sales Orders List',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'orders' => $orders
        ]);
    }
    public function exportSalesOrders()
    {
        $orders = \App\Models\SalesOrder::with(['customer', 'items', 'preparedBy'])
                    ->latest()
                    ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Orders');

        // Headers
        $headers = ['A1' => 'Order Number', 'B1' => 'Customer', 'C1' => 'Order Date',
                    'D1' => 'Platform / Source', 'E1' => 'Total Amount', 'F1' => 'Items Count',
                    'G1' => 'Status', 'H1' => 'Prepared By', 'I1' => 'Pick Qty'];
        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFCC0000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FF999999']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        $row = 2;
        foreach ($orders as $order) {
            $typeDisplay = str_replace('_', ' ', $order->type);
            if ($order->type === 'calculator_pos') $typeDisplay = 'Direct POS';
            if ($order->type === 'ecom_direct')    $typeDisplay = 'ECOM POS';
            if ($order->type === 'paid')           $typeDisplay = 'paid transac';
            $typeDisplay = strtoupper($typeDisplay);

            $displayStatus = str_replace('_', ' ', $order->status);
            if ($order->status === 'draft') {
                $isFreightApproved = $order->freight_charges !== null || ($order->freightQuotation && in_array($order->freightQuotation->workflow_status, ['approved', 'linked_to_so']));
                $displayStatus = $isFreightApproved ? 'Draft (Freight Approved)' : 'Draft (Pending Freight)';
            }
            if ($order->status === 'pending_si_prep')       $displayStatus = 'Gathered (In SI Prep)';
            if ($order->status === 'si_created')            $displayStatus = 'SI Created';
            if ($order->status === 'pending_dr_prep')       $displayStatus = 'SI Signed (In DR Prep)';
            if ($order->status === 'pending_mkt_approval')  $displayStatus = 'Pending Marketing Approval';
            if ($order->status === 'pending_prod_approval') $displayStatus = 'Pending Production Approval';
            $displayStatus = ucwords($displayStatus);

            $sheet->setCellValue("A{$row}", $order->so_number);
            $sheet->setCellValue("B{$row}", $order->customer->customer_name ?? 'Unknown Customer');
            $sheet->setCellValue("C{$row}", $order->created_at->format('Y-m-d'));
            $sheet->setCellValue("D{$row}", $typeDisplay);
            $sheet->setCellValue("E{$row}", (float) $order->total_amount);
            $sheet->setCellValue("F{$row}", $order->items->count());
            $sheet->setCellValue("G{$row}", $displayStatus);
            $sheet->setCellValue("H{$row}", optional($order->preparedBy)->name ?? '');
            $sheet->setCellValue("I{$row}", '');

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:I{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF5F5F5');
            }
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $filename = 'Sales_Orders_' . now()->format('Y-m-d_His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportSingleSalesOrder($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'areaSalesStaff'])
                    ->where('type', 'area_sales_consignment')
                    ->findOrFail($id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SO ' . $order->so_number);

        // Order header banner (row 1)
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'AREA SALES CONSIGNMENT  ' . $order->so_number);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFCC0000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Order meta info rows 2-7
        $meta = [
            ['Sales Order #',    $order->so_number],
            ['Order Date',       $order->created_at->format('Y-m-d')],
            ['Area Sales Staff', optional($order->areaSalesStaff)->name ?? '—'],
            ['Status',           ucwords(str_replace('_', ' ', $order->status))],
            ['Total Amount',     '₱' . number_format($order->total_amount, 2)],
            ['Customer Name',    $order->customer?->customer_name ?? ''],  // blank if no customer — staff fills this in
        ];
        $metaRow = 2;
        foreach ($meta as [$label, $value]) {
            $sheet->setCellValue("A{$metaRow}", $label);
            $sheet->setCellValue("B{$metaRow}", $value);
            $sheet->getStyle("A{$metaRow}")->getFont()->setBold(true);
            $metaRow++;
        }

        // Highlight the Customer Name row (B7) in light blue so staff knows to fill it
        $sheet->getStyle("A7:B7")->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                       'startColor' => ['argb' => 'FFD6EAF8']],
            'font' => ['color' => ['argb' => 'FF1A5276'], 'bold' => true],
        ]);
        // Add placeholder hint in B7 if empty
        if (empty($order->customer?->customer_name)) {
            $sheet->getComment('B7')->getText()->createTextRun('Fill in the customer name here before importing back.');
        }

        // Items table header
        $tableStart = $metaRow + 1;
        $colHeaders = ['#', 'Book Title / Product', 'Unit', 'Order Qty', 'Unit Price', 'Subtotal', 'Pick Qty'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($cols as $i => $col) {
            $sheet->setCellValue("{$col}{$tableStart}", $colHeaders[$i]);
        }
        // Style columns A-F (dark header)
        $sheet->getStyle("A{$tableStart}:F{$tableStart}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF333333']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FFAAAAAA']]],
        ]);
        // Style column G - Pick Qty header (orange bg, dark bold text)
        $sheet->getStyle("G{$tableStart}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FF7B3F00']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFA500']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FFAAAAAA']]],
        ]);
        $sheet->getRowDimension($tableStart)->setRowHeight(20);

        // Item data rows
        $dataRow = $tableStart + 1;
        $seq = 1;
        foreach ($order->items as $item) {
            $sheet->setCellValue("A{$dataRow}", $seq++);
            $sheet->setCellValue("B{$dataRow}", optional($item->book)->name ?? 'Unknown Product');
            $sheet->setCellValue("C{$dataRow}", $item->unit ?? 'pcs');
            $sheet->setCellValue("D{$dataRow}", (int) $item->quantity);
            $sheet->setCellValue("E{$dataRow}", (float) $item->price);
            $sheet->setCellValue("F{$dataRow}", (float) $item->subtotal);
            $sheet->setCellValue("G{$dataRow}", ''); // Pick Qty — blank for manual entry

            $sheet->getStyle("E{$dataRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("F{$dataRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            if ($dataRow % 2 === 0) {
                $sheet->getStyle("A{$dataRow}:G{$dataRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF9F9F9');
            }
            $sheet->getStyle("A{$dataRow}:G{$dataRow}")->getBorders()
                ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->getColor()->setARGB('FFDDDDDD');

            $dataRow++;
        }

        // Highlight Pick Qty data cells — light orange background, dark orange text
        $sheet->getStyle("G" . ($tableStart + 1) . ":G" . ($dataRow - 1))->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFF0D9']],
            'font'      => ['color' => ['argb' => 'FF7B3F00'], 'italic' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->freezePane("A" . ($tableStart + 1));

        // Download
        $filename = 'SO_' . $order->so_number . '_' . now()->format('Ymd') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    public function salesOrderDetail($id = null)
    {
        $order = null;
        if ($id) {
            $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bookIndex.book', 'items.bundle', 'items.pickListItems', 'preparedBy', 'areaSalesStaff'])->findOrFail($id);

            // Fail-safe auto-heal: restore any items with 0 quantity from pick list or historical qty
            foreach ($order->items as $item) {
                if ((float)($item->quantity ?? 0) <= 0) {
                    $item->loadMissing('pickListItems');
                    $plReq = $item->pickListItems->first()?->requested_qty ?? 0;
                    $restoredQty = (float)($item->sent_qty ?? 0) > 0 
                        ? (float)$item->sent_qty 
                        : ((float)($item->customer_selected_qty ?? 0) > 0 
                            ? (float)$item->customer_selected_qty 
                            : ((float)$plReq > 0 ? (float)$plReq : 0));
                    if ($restoredQty > 0) {
                        $itemGross = $restoredQty * (float)($item->price ?? $item->unit_price ?? 0);
                        $discVal = (float)($item->discount_value ?? 0);
                        $discType = $item->discount_type ?? 'percentage';
                        $discAmt = (float)($item->discount_amount ?? 0);
                        if ($discAmt <= 0 && $discVal > 0) {
                            $discAmt = $discType === 'percentage' ? $itemGross * ($discVal / 100) : $discVal;
                        }
                        $sub = max(0, $itemGross - $discAmt);
                        $item->update(['quantity' => $restoredQty, 'subtotal' => $sub]);
                        $item->quantity = $restoredQty;
                        $item->subtotal = $sub;
                    }
                }
            }

            // Recalculate total_amount to ensure database is in sync with line items and charges
            $itemsSubtotal = $order->items->filter(function($item) {
                return $item->book || $item->bookIndex || $item->bundle;
            })->sum(function($item) {
                return ($item->subtotal !== null) ? (float)$item->subtotal : ((float)$item->quantity * (float)$item->price);
            });
            $discountAmount = (float) ($order->discount_amount ?? 0);
            $freightCharges = (float) ($order->freight_charges ?? 0);
            $serviceFee = $order->freight_option === 'freight_collect' ? 50.00 : 0;

            $calculatedTotal = max(0, $itemsSubtotal - $discountAmount + $freightCharges + $serviceFee);

            if (abs((float)$order->total_amount - $calculatedTotal) > 0.01) {
                $order->update(['total_amount' => $calculatedTotal]);
                $order->total_amount = $calculatedTotal;
            }
        }

        return view('marketing.sales-orders.detail', [
            'title' => 'Sales Order',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'order' => $order
        ]);
    }

    public function shippingLabel($id, Request $request)
    {
        $order = \App\Models\SalesOrder::with('customer')->findOrFail($id);
        
        // Get address from query parameter if provided (edited address), otherwise use original
        $address = $request->query('address') ? urldecode($request->query('address')) : ($order->shipping_address ?: ($order->customer->shipping_address ?: $order->customer->billing_address));

        return view('marketing.sales-orders.shipping-label', [
            'order' => $order,
            'address' => $address
        ]);
    }

    public function printSalesInvoiceForm($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bundle', 'preparedBy', 'mktApprovedBy', 'prodApprovedBy'])->findOrFail($id);

        return view('marketing.sales-orders.print-invoice', [
            'order' => $order
        ]);
    }

    public function getUnifiedProducts($teamName = null)
    {
        $teamStocksMap = [];
        if (!empty($teamName)) {
            $rawTeam = trim($teamName);
            $cleanName = trim(preg_replace('/^(site\s+|team\s+)+/i', '', $rawTeam));
            $variations = array_unique([
                $rawTeam,
                'Team ' . $cleanName,
                'SITE TEAM ' . strtoupper($cleanName),
                'SITE TEAM ' . $cleanName,
                'SITE ' . strtoupper($cleanName),
                'SITE ' . $cleanName,
                $cleanName,
                strtoupper($rawTeam),
                strtolower($rawTeam),
            ]);

            $tsList = \App\Models\TeamStock::where(function($q) use ($variations) {
                foreach ($variations as $var) {
                    $q->orWhere('team_name', $var)
                      ->orWhereRaw('LOWER(team_name) = ?', [strtolower($var)]);
                }
            })->get();

            foreach ($tsList as $ts) {
                if ($ts->book_index_id) {
                    $teamStocksMap['index_' . $ts->book_index_id] = (int)$ts->quantity;
                } elseif ($ts->book_bundle_id) {
                    $teamStocksMap['bundle_' . $ts->book_bundle_id] = (int)$ts->quantity;
                } elseif ($ts->book_id) {
                    $teamStocksMap['book_' . $ts->book_id] = (int)$ts->quantity;
                }
            }
        }

        $booksQuery = \App\Models\Book::where('is_active', true);
        if (!empty($teamName)) {
            $allowedBookIds = array_map(fn($k) => (int)str_replace('book_', '', $k), array_keys(array_filter($teamStocksMap, fn($v, $k) => str_starts_with($k, 'book_') && $v > 0, ARRAY_FILTER_USE_BOTH)));
            $booksQuery->whereIn('id', $allowedBookIds ?? [0]);
        }
        $books = $booksQuery->orderBy('name')
            ->get()
            ->map(function($b) use ($teamName, $teamStocksMap) {
                $isNonBook = (isset($b->is_book) && $b->is_book === false) || 
                             (isset($b->category) && strtolower($b->category) === 'non-book') ||
                             (isset($b->book_type) && strtolower($b->book_type) === 'non-book');
                $typeSuffix = $isNonBook ? ' (non-book)' : ' (book)';
                $fullName = $b->name . $typeSuffix;
                $prefix = $isNonBook ? '[Non-Book] ' : '[Book] ';

                $stock = !empty($teamName) 
                    ? (int)($teamStocksMap['book_' . $b->id] ?? 0)
                    : (int)($b->main_stock ?? $b->stock ?? 0);

                return (object)[
                    'id' => 'book_' . $b->id,
                    'type' => 'book',
                    'real_id' => $b->id,
                    'book_id' => $b->id,
                    'name' => $fullName,
                    'category' => $isNonBook ? 'Non-Books' : 'Books',
                    'display_name' => $prefix . $b->name . ' (Stock: ' . $stock . ')',
                    'price' => (float) $b->price,
                    'isbn' => $b->isbn ?? $b->barcode ?? $b->sku ?? '',
                    'stock' => $stock,
                    'main_stock' => $stock,
                    'image' => $b->image ? asset('storage/' . $b->image) : asset('images/no-book-cover.svg'),
                ];
            });

        $indicesQuery = \App\Models\BookIndex::with('book');
        if (!empty($teamName)) {
            $allowedIndexIds = array_map(fn($k) => (int)str_replace('index_', '', $k), array_keys(array_filter($teamStocksMap, fn($v, $k) => str_starts_with($k, 'index_') && $v > 0, ARRAY_FILTER_USE_BOTH)));
            $indicesQuery->whereIn('id', $allowedIndexIds ?? [0]);
        }
        $indices = $indicesQuery->get()
            ->map(function($idx) use ($teamName, $teamStocksMap) {
                $fullName = $idx->display_name;
                $price = (float) (($idx->price && $idx->price > 0) ? $idx->price : ($idx->book?->price ?? 0));
                $img = $idx->book?->image ? asset('storage/' . $idx->book->image) : asset('images/no-book-cover.svg');
                $stock = !empty($teamName) 
                    ? (int)($teamStocksMap['index_' . $idx->id] ?? 0)
                    : (int)($idx->main_stock ?? $idx->stock ?? 0);

                return (object)[
                    'id' => 'index_' . $idx->id,
                    'type' => 'index',
                    'real_id' => $idx->id,
                    'book_id' => $idx->book_id,
                    'name' => $fullName,
                    'category' => 'Book Indices',
                    'display_name' => '[Index] ' . $fullName . ' (Stock: ' . $stock . ')',
                    'price' => $price,
                    'isbn' => $idx->book?->isbn ?? '',
                    'stock' => $stock,
                    'main_stock' => $stock,
                    'image' => $img,
                ];
            });

        $bundlesQuery = \App\Models\BookBundle::where('is_active', true);
        if (!empty($teamName)) {
            $allowedBundleIds = array_map(fn($k) => (int)str_replace('bundle_', '', $k), array_keys(array_filter($teamStocksMap, fn($v, $k) => str_starts_with($k, 'bundle_') && $v > 0, ARRAY_FILTER_USE_BOTH)));
            $bundlesQuery->whereIn('id', $allowedBundleIds ?? [0]);
        }
        $bundles = $bundlesQuery->orderBy('name')
            ->get()
            ->map(function($bun) use ($teamName, $teamStocksMap) {
                $fullName = $bun->name . ' (bundle)';
                $stock = !empty($teamName) 
                    ? (int)($teamStocksMap['bundle_' . $bun->id] ?? 0)
                    : (int)($bun->main_stock ?? $bun->stock ?? 0);

                return (object)[
                    'id' => 'bundle_' . $bun->id,
                    'type' => 'bundle',
                    'real_id' => $bun->id,
                    'book_id' => null,
                    'name' => $fullName,
                    'category' => 'Book Bundles',
                    'display_name' => '[Bundle] ' . $bun->name . ' (Stock: ' . $stock . ')',
                    'price' => (float) $bun->price,
                    'isbn' => $bun->sku ?? '',
                    'stock' => $stock,
                    'main_stock' => $stock,
                    'image' => asset('images/no-book-cover.svg'),
                ];
            });

        $unified = $books->concat($indices)->concat($bundles);
        if (empty($teamName)) {
            $unified = $unified->filter(fn($p) => $p->stock > 0)->values();
        }
        return $unified;
    }

    public function resolveItemTarget($pid)
    {
        $pidStr = (string) $pid;
        if (str_starts_with($pidStr, 'bundle_')) {
            $id = (int) str_replace('bundle_', '', $pidStr);
            $bundle = \App\Models\BookBundle::find($id);
            return [
                'type' => 'bundle',
                'name' => $bundle?->name ?? "Bundle #{$id}",
                'book_id' => null,
                'bundle_id' => $id,
                'book_index_id' => null,
                'stock' => (int) ($bundle?->main_stock ?? 0),
                'source_price' => (float) ($bundle?->price ?? 0),
                'exists' => (bool) $bundle,
            ];
        } elseif (str_starts_with($pidStr, 'index_')) {
            $id = (int) str_replace('index_', '', $pidStr);
            $index = \App\Models\BookIndex::with('book')->find($id);
            $name = $index ? $index->display_name : "Index #{$id}";
            return [
                'type' => 'index',
                'name' => $name,
                'book_id' => $index?->book_id,
                'bundle_id' => null,
                'book_index_id' => $id,
                'stock' => (int) ($index?->main_stock ?? 0),
                'source_price' => (float) ($index?->price ?: ($index?->book?->source_price ?? 0)),
                'exists' => (bool) $index,
            ];
        } else {
            $id = (int) str_replace('book_', '', $pidStr);
            $book = \App\Models\Book::find($id);
            return [
                'type' => 'book',
                'name' => $book?->name ?? "Book #{$id}",
                'book_id' => $id,
                'bundle_id' => null,
                'book_index_id' => null,
                'stock' => (int) ($book?->main_stock ?? 0),
                'source_price' => (float) ($book?->source_price ?? 0),
                'exists' => (bool) $book,
            ];
        }
    }

    public function getProductsByTeam(\Illuminate\Http\Request $request)
    {
        $teamName = $request->input('team_name');
        if (empty($teamName) && auth()->check()) {
            $teamName = auth()->user()->sales_team ?? null;
        }

        $products = $this->getUnifiedProducts($teamName);
        return response()->json([
            'team_name' => $teamName ?: 'Main Warehouse',
            'products' => $products
        ]);
    }

    public function consignmentInventoryIndex()
    {
        $items = \App\Models\ConsignmentInventory::with(['salesOrder', 'customer', 'book', 'bookIndex.book', 'bookBundle'])
            ->latest()
            ->get();

        return view('marketing.consignment-inventory', [
            'title' => 'Consignment Inventory',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'items' => $items,
        ]);
    }

    public function createSalesOrder()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $userTeam = auth()->user()->sales_team ?? null;
        $products = $this->getUnifiedProducts($userTeam);
        $areaSalesStaff = \App\Models\User::where('department', 'Area Sales')->get();

        return view('marketing.sales-orders.create', [
            'title' => 'Create Sales Order',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'areaSalesStaff' => $areaSalesStaff,
            'userTeam' => $userTeam
        ]);
    }

    public function storeSalesOrder(\Illuminate\Http\Request $request)
    {
        $action = $request->input('action', 'submit'); // 'draft' or 'submit'
        
        $validated = $request->validate([
            'customer_id' => $request->input('type') === 'area_sales_consignment' ? 'nullable|exists:customers,customer_id' : 'required|exists:customers,customer_id',
            'area_sales_staff_id' => $request->input('type') === 'area_sales_consignment' ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'type' => 'required',
            'so_number' => 'required|unique:sales_orders,so_number',
            'items' => $action === 'draft' ? 'nullable|array|max:24' : 'required|array|min:1|max:24', // Items optional for draft, max 24
            'remarks' => 'nullable',
            'terms' => 'nullable',
            'ref_number' => 'nullable',
            'billing_address' => 'nullable',
            'attachment' => 'nullable|file|max:5120', // 5MB Limit
            'proof_of_payment' => 'nullable|file|max:5120', // 5MB Limit
            'freight_option' => 'nullable|string|in:freight_collect,freight_billing,bill_client',
            'forwarder' => 'nullable|string|max:255',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:amount,percentage',
        ]);

        // Validate stock & quantity for all items
        $itemErrors = [];
        $userTeam = auth()->user()->sales_team ?? null;

        if ($request->filled('site_name')) {
            if ($request->site_name === 'Book Sale') {
                $userTeam = 'Book Sale';
            } elseif ($request->site_name === 'Main Warehouse') {
                $userTeam = null;
            }
        }

        if (empty($userTeam) && $request->type === 'area_sales_consignment' && $request->filled('area_sales_staff_id')) {
            $staff = \App\Models\User::find($request->area_sales_staff_id);
            if ($staff && $staff->sales_team) {
                $userTeam = $staff->sales_team;
            }
        }

        $validProductCount = count(array_filter($request->items ?? [], fn($i) => !empty($i['product_id'])));
        if ($validProductCount > 24) {
            $itemErrors[] = "Maximum of 24 products allowed per order.";
        }

        foreach ($request->items ?? [] as $item) {
            if (empty($item['product_id'])) continue;
            $qty = (int) ($item['quantity'] ?? 0);
            $target = $this->resolveItemTarget($item['product_id']);
            $productName = $target['name'];

            if ($qty <= 0) {
                $itemErrors[] = "<strong>{$productName}</strong>: Quantity must be at least 1.";
                continue;
            }

            if (!empty($userTeam)) {
                $rawTeam = trim($userTeam);
                $cleanName = trim(preg_replace('/^(site\s+|team\s+)+/i', '', $rawTeam));
                $variations = array_unique([
                    $rawTeam,
                    'Team ' . $cleanName,
                    'SITE TEAM ' . strtoupper($cleanName),
                    'SITE TEAM ' . $cleanName,
                    'SITE ' . strtoupper($cleanName),
                    'SITE ' . $cleanName,
                    $cleanName,
                    strtoupper($rawTeam),
                    strtolower($rawTeam),
                ]);

                $ts = \App\Models\TeamStock::where(function($q) use ($variations) {
                        foreach ($variations as $var) {
                            $q->orWhere('team_name', $var)
                              ->orWhereRaw('LOWER(team_name) = ?', [strtolower($var)]);
                        }
                    })
                    ->where(function($q) use ($target) {
                        if (!empty($target['book_index_id'])) $q->where('book_index_id', $target['book_index_id']);
                        elseif (!empty($target['book_id'])) $q->where('book_id', $target['book_id']);
                        elseif (!empty($target['bundle_id'])) $q->where('book_bundle_id', $target['bundle_id']);
                    })->first();

                $availableStock = $ts ? (int) $ts->quantity : 0;
                if ($qty > $availableStock) {
                    $itemErrors[] = "<strong>{$productName}</strong>: Requested {$qty} pcs, but <strong>{$userTeam}</strong> stock only has <strong>{$availableStock} pcs</strong> available.";
                }
            } else {
                $availableStock = (int) $target['stock'];
                if ($qty > $availableStock) {
                    $itemErrors[] = "<strong>{$productName}</strong>: Requested {$qty} pcs, but <strong>Main Warehouse</strong> stock only has <strong>{$availableStock} pcs</strong> available.";
                }
            }
        }

        if (!empty($itemErrors)) {
            return redirect()->back()->with('error', 'Cannot proceed with Sales Order:<br>• ' . implode('<br>• ', $itemErrors))->withInput();
        }

        $isFromSI = $request->input('source') === 'si' || $request->query('source') === 'si' || $request->input('from_si') == 1 || str_contains(url()->previous(), 'sales-invoice') || str_contains(request()->header('referer', ''), 'sales-invoice');

        // 1. Determine Initial Status
        if ($action === 'draft') {
            // Draft mode: wait for freight quotation
            $initialStatus = 'draft';
        } elseif ($isFromSI) {
            $initialStatus = 'pending_si_prep';
        } else {
            // Submit mode: proceed with approval flow
            $initialStatus = 'pending_mkt_approval';
            
            // Check if user is already a Manager/Supervisor to auto-approve to next stage
            $isMktManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor');
            
            if ($isMktManager) {
                $initialStatus = 'pending_acct_approval';
            }
        }

        // 2. Handle Attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('sales_orders', 'public');
        }

        $proofOfPaymentPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofOfPaymentPath = $request->file('proof_of_payment')->store('sales_orders', 'public');
        }

        // 3. Create Header
        $remarks = $request->remarks;
        if ($request->filled('site_name')) {
            $siteTag = "[SITE: " . trim($request->site_name) . "]";
            if (!str_contains($remarks ?? '', '[SITE:')) {
                $remarks = trim(($remarks ? $remarks . ' | ' : '') . $siteTag);
            }
        }

        $soData = [
            'customer_id' => $request->customer_id,
            'customer_representative' => $request->customer_representative,
            'area_sales_staff_id' => $request->type === 'area_sales_consignment' ? $request->area_sales_staff_id : null,
            'so_number' => $request->so_number,
            'type' => $request->type,
            'status' => $initialStatus,
            'prepared_by' => auth()->id(),
            'approved_by_mkt' => $action === 'submit' ? auth()->id() : null, // Only set for submissions
            'remarks' => $remarks,
            'terms' => $request->terms,
            'ref_number' => $request->ref_number,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'attachment' => $attachmentPath,
            'proof_of_payment' => $proofOfPaymentPath,
            'freight_option' => $validated['freight_option'] ?? null,
            'forwarder' => $request->forwarder ?? null,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('sales_orders', 'customer_contact')) {
            $soData['customer_contact'] = $request->customer_contact;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('sales_orders', 'cancellation_date') && $request->filled('cancellation_date')) {
            $soData['cancellation_date'] = $request->cancellation_date;
        }

        $so = \App\Models\SalesOrder::create($soData);

        // Clean up any orphaned items that might reuse this order ID (in case of auto-increment reuse)
        $so->items()->delete();

        // 4. Create Items (only if provided)
        $totalAmount = 0;
        if (!empty($request->items)) {
            $aggregatedItems = [];
            foreach ($request->items as $item) {
                if (empty($item['product_id'])) continue;
                $pid = $item['product_id'];
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                $key = $pid . '_' . $discVal . '_' . $discType;

                if (isset($aggregatedItems[$key])) {
                    $aggregatedItems[$key]['quantity'] += (int) $item['quantity'];
                } else {
                    $aggregatedItems[$key] = [
                        'product_id' => $pid,
                        'quantity' => (int) $item['quantity'],
                        'price' => (float) $item['price'],
                        'unit' => $item['unit'] ?? 'pcs',
                        'area' => $item['area'] ?? null,
                        'discount_value' => $discVal,
                        'discount_type' => $discType,
                    ];
                }
            }

            foreach (array_values($aggregatedItems) as $item) {
                $target = $this->resolveItemTarget($item['product_id']);
                if (!$target['exists']) {
                    \Log::warning('storeSalesOrder: skipping item with non-existent product_id=' . $item['product_id']);
                    continue;
                }

                $gross = $item['quantity'] * $item['price'];
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                if ($discType === 'percentage') {
                    $discAmount = $gross * ($discVal / 100);
                } else {
                    $discAmount = $discVal;
                }
                $subtotal = max(0, $gross - $discAmount);
                $totalAmount += $subtotal;

                \App\Models\SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'book_id' => $target['book_id'],
                    'bundle_id' => $target['bundle_id'],
                    'book_index_id' => $target['book_index_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount_value' => $discVal,
                    'discount_type' => $discType,
                    'discount_amount' => $discAmount,
                    'subtotal' => $subtotal,
                    'unit' => $item['unit'] ?? 'pcs',
                    'area' => $item['area'] ?? null,
                    'source_price_at_sale' => $target['source_price'],
                ]);
            }
        }

        $discountAmount = 0;
        $discountPercentage = 0;
        if ($request->filled('discount_value') && $request->discount_value > 0) {
            $discountValue = (float) $request->discount_value;
            if ($request->discount_type === 'percentage') {
                $discountPercentage = $discountValue;
                $discountAmount = $totalAmount * ($discountPercentage / 100);
            } else {
                $discountAmount = $discountValue;
                $discountPercentage = 0;
            }
        }

        $finalTotal = $totalAmount - $discountAmount;

        if (($validated['freight_option'] ?? null) === 'freight_collect') {
            $finalTotal += 50.00;
        }

        // 5. Update Total and Discount fields
        $so->update([
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage ?? 0,
            'total_amount' => max(0, $finalTotal)
        ]);

        // 6. Set transaction type to COD if SO type is 'cod'
        if ($validated['type'] === 'cod') {
            $so->update([
                'transaction_type' => 'COD',
            ]);
        }

        // Deduct stock immediately upon Sales Order creation
        \App\Services\StockDeductionService::deductForSalesOrder($so);

        $isFromSI = $request->input('source') === 'si' || $request->query('source') === 'si' || $request->input('from_si') == 1 || str_contains(url()->previous(), 'sales-invoice') || str_contains(request()->header('referer', ''), 'sales-invoice');

        if ($isFromSI && $action !== 'draft') {
            $so->update([
                'status' => 'pending_si_prep',
                'approved_by_mkt' => auth()->id(),
            ]);

            return redirect()->route('admin-finance.accounting.sales-invoice')
                ->with('success', "Sales Order #{$so->so_number} created successfully and added to Normal Invoices!");
        }

        $message = $action === 'draft' 
            ? 'Sales Order saved as draft. Please request freight quotation from Logistics.'
            : 'Sales Order created and routed successfully!';
        
        return redirect()->route('marketing.sales-orders.list')->with('success', $message);
    }

    public function approveSalesOrder(Request $request, $id)
    {
        // Role Enforcement: Only Marketing Manager or Supervisor
        if (!str_contains(auth()->user()->position, 'Manager') && !str_contains(auth()->user()->position, 'Supervisor')) {
            return redirect()->back()->with('error', 'Only Marketing Managers or Supervisors can approve Sales Orders.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        
        // NBS PO import / E-Com direct route to picking upon Marketing Approval
        // All other SO types (Paid, Charge, Consignments) proceed to Admin and Finance approval after Marketing approval
        if (str_starts_with($order->so_number, 'SO-NBS-') || $order->type === 'ecom_direct') {
            $nextStatus = 'picking';
        } else {
            $nextStatus = 'pending_acct_approval';
        }
        
        $updateData = [
            'status' => $nextStatus,
            'approved_by_mkt' => auth()->id(),
            'mkt_approved_at' => now()
        ];

        if ($request->filled('remarks')) {
            $userTitle = auth()->user()->name . ' (Marketing)';
            $updateData['remarks'] = trim(($order->remarks ? $order->remarks . "\n" : '') . '[' . $userTitle . ']: ' . $request->remarks);
        }
        
        $order->update($updateData);

        // If transitioning to picking (e.g. NBS PO / Area Consignment), automatically generate Pick List
        if ($nextStatus === 'picking') {
            $order->load('items');
            if ($order->items && $order->items->count() > 0) {
                $existingPickList = \App\Models\PickList::where('sales_order_id', $order->id)->first();
                if (!$existingPickList) {
                    $pickList = \App\Models\PickList::create([
                        'sales_order_id'   => $order->id,
                        'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
                        'status'           => 'in_progress',
                        'prepared_by'      => auth()->id(),
                    ]);

                    foreach ($order->items as $item) {
                        \App\Models\PickListItem::create([
                            'pick_list_id'        => $pickList->id,
                            'sales_order_item_id' => $item->id,
                            'requested_qty'       => $item->quantity,
                            'picked_qty'          => 0,
                            'status'              => 'pending',
                        ]);
                    }
                }
            }
        }

        $successMsg = 'Sales Order #' . $order->so_number . ' has been approved by Marketing.';
        if ($nextStatus === 'pending_prod_approval') {
            $successMsg .= ' It has been routed to Logistics / Production Approval Queue.';
        } elseif ($nextStatus === 'picking') {
            $successMsg .= ' It has been routed to E-Commerce / Logistics Pick Lists.';
        } else {
            $successMsg .= ' Awaiting Accounting approval.';
        }

        return redirect()->route('marketing.approval-queue')->with('success', $successMsg);
    }

    public function rejectSalesOrder(Request $request, $id)
    {
        if (!str_contains(auth()->user()->position, 'Manager') && !str_contains(auth()->user()->position, 'Supervisor')) {
            return redirect()->back()->with('error', 'Only Marketing Managers or Supervisors can reject Sales Orders.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        $userTitle = auth()->user()->name . ' (Marketing Rejection)';
        $remarksText = $request->remarks ? $request->remarks : 'Rejected by Marketing';
        $newRemarks = trim(($order->remarks ? $order->remarks . "\n" : '') . '[' . $userTitle . ']: ' . $remarksText);
        $order->update([
            'status' => 'cancelled',
            'remarks' => $newRemarks
        ]);

        // Restore stock when SO is rejected
        \App\Services\StockDeductionService::restoreForSalesOrder($order, 'Marketing Rejection');

        return redirect()->route('marketing.approval-queue')->with('warning', 'Sales Order #' . $order->so_number . ' has been rejected.');
    }

    public function proceedToFinalSalesOrder(Request $request, $id)
    {
        /**
         * This method finalizes a draft SO after freight charges have been approved
         * Transitions: draft (with freight_charges) → pending_mkt_approval
         */
        $so = \App\Models\SalesOrder::findOrFail($id);
        
        // Validate: only draft SOs with freight charges can proceed
        if ($so->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft sales orders can be finalized.');
        }

        $isFreightApproved = $so->freight_charges !== null || ($so->freightQuotation && in_array($so->freightQuotation->workflow_status, ['approved', 'linked_to_so']));
        if (!$isFreightApproved) {
            return redirect()->back()->with('error', 'Freight charges must be approved before proceeding.');
        }

        // Transition to pending approval
        $isMktManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor');
        $nextStatus = $isMktManager ? 'pending_acct_approval' : 'pending_mkt_approval';
        
        $so->update([
            'status' => $nextStatus,
            'approved_by_mkt' => $isMktManager ? auth()->id() : null,
            'mkt_approved_at' => $isMktManager ? now() : null,
        ]);


        if ($so->freightQuotation) {
            $so->freightQuotation->update([
                'workflow_status' => 'linked_to_so',
                'status' => 'approved',
            ]);
        }

        $message = 'Sales Order #' . $so->so_number . ' has been finalized with freight charges and routed for approval.';

        $isFord = $request->input('source') === 'ford' || $so->type === 'foreign' || str_contains($so->so_number, 'FORD') || $so->source === 'ford' || ($so->freightQuotation && $so->freightQuotation->source === 'ford');
        if ($isFord) {
            return redirect()->route('production.ford.sales-order')->with('success', $message);
        }

        return redirect()->route('marketing.sales-orders.list')->with('success', $message);
    }

    public function editSalesOrder($id)
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admin can edit sales orders.');
        }

        $order = \App\Models\SalesOrder::with(['items.book', 'items.bookIndex.book', 'items.bundle'])->findOrFail($id);
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $products = $this->getUnifiedProducts();
        $areaSalesStaff = \App\Models\User::where('department', 'Area Sales')->get();

        return view('marketing.sales-orders.create', [
            'title' => 'Edit Sales Order',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'order' => $order,
            'areaSalesStaff' => $areaSalesStaff
        ]);
    }

    // Handle AJAX/JSON updates for freight option only
    public function updateSalesOrderQuick(Request $request, $id)
    {
        try {
            $so = \App\Models\SalesOrder::findOrFail($id);
            
            // Only allow updates on non-completed/cancelled orders
            if (in_array($so->status, ['completed', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit completed or cancelled orders.'
                ], 422);
            }

            $validated = $request->validate([
                'freight_option' => 'nullable|string|in:,freight_collect,freight_billing,bill_client',
                'forwarder' => 'nullable|string|max:255'
            ]);

            // Calculate items subtotal (sum of all line items)
            $itemsSubtotal = $so->items()->sum('subtotal');
            
            // Add service fee (₱50.00) if freight_option is 'freight_collect'
            $serviceFee = ($validated['freight_option'] === 'freight_collect') ? 50.00 : 0;
            $newTotal = $itemsSubtotal + ($so->freight_charges ?? 0) + $serviceFee;

            $so->update([
                'freight_option' => $validated['freight_option'],
                'forwarder' => $request->forwarder ?? $so->forwarder,
                'total_amount' => $newTotal
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sales Order updated successfully!',
                'data' => $so
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSalesOrder(Request $request, $id)
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admin can edit sales orders.');
        }
        $so = \App\Models\SalesOrder::with('items')->findOrFail($id);
        
        $validated = $request->validate([
            'customer_id' => $request->input('type') === 'area_sales_consignment' ? 'nullable|exists:customers,customer_id' : 'required|exists:customers,customer_id',
            'area_sales_staff_id' => $request->input('type') === 'area_sales_consignment' ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'type' => 'required',
            'items' => 'required|array|min:1|max:24',
            'remarks' => 'nullable',
            'terms' => 'nullable',
            'ref_number' => 'nullable',
            'billing_address' => 'nullable',
            'attachment' => 'nullable|file|max:5120',
            'proof_of_payment' => 'nullable|file|max:5120',
            'freight_option' => 'nullable|string|in:freight_collect,freight_billing,bill_client',
            'forwarder' => 'nullable|string|max:255',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:amount,percentage',
        ]);

        // Validate stock & quantity for all items
        $itemErrors = [];
        $validProductCount = count(array_filter($request->items ?? [], fn($i) => !empty($i['product_id'])));
        if ($validProductCount > 24) {
            $itemErrors[] = "Maximum of 24 products allowed per order.";
        }

        foreach ($request->items ?? [] as $item) {
            if (empty($item['product_id'])) continue;
            $qty = (int) ($item['quantity'] ?? 0);
            $target = $this->resolveItemTarget($item['product_id']);
            $productName = $target['name'];
            $availableStock = $target['stock'];

            if ($qty <= 0) {
                $itemErrors[] = "<strong>{$productName}</strong>: Quantity must be at least 1.";
            } elseif ($availableStock <= 0) {
                $itemErrors[] = "<strong>{$productName}</strong>: Item is out of stock (Stock: 0).";
            } elseif ($availableStock < $qty) {
                $itemErrors[] = "<strong>{$productName}</strong>: Insufficient stock (Available: {$availableStock} pcs, Requested: {$qty} pcs).";
            }
        }

        if (!empty($itemErrors)) {
            return redirect()->back()->with('error', 'Cannot update Sales Order:<br>• ' . implode('<br>• ', $itemErrors))->withInput();
        }

        if ($request->hasFile('attachment')) {
            // optional: delete old file
            $path = $request->file('attachment')->store('sales_orders', 'public');
            $so->attachment = $path;
        }

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('sales_orders', 'public');
            $so->proof_of_payment = $path;
        }

        $so->update([
            'customer_id' => $request->customer_id,
            'customer_representative' => $request->customer_representative,
            'customer_contact' => $request->customer_contact,
            'area_sales_staff_id' => $request->type === 'area_sales_consignment' ? $request->area_sales_staff_id : null,
            'type' => $request->type,
            'remarks' => $request->remarks,
            'terms' => $request->terms,
            'ref_number' => $request->ref_number,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'freight_option' => $validated['freight_option'] ?? null,
            'forwarder' => $request->forwarder ?? null,
        ]);


        // Re-create items
        $so->items()->delete();
        
        $totalAmount = 0;
        if (!empty($request->items)) {
            $aggregatedItems = [];
            foreach ($request->items as $item) {
                if (empty($item['product_id'])) continue;
                $pid = $item['product_id'];
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                $key = $pid . '_' . $discVal . '_' . $discType;

                if (isset($aggregatedItems[$key])) {
                    $aggregatedItems[$key]['quantity'] += (int) $item['quantity'];
                } else {
                    $aggregatedItems[$key] = [
                        'product_id' => $pid,
                        'quantity' => (int) $item['quantity'],
                        'price' => (float) $item['price'],
                        'unit' => $item['unit'] ?? 'pcs',
                        'area' => $item['area'] ?? null,
                        'discount_value' => $discVal,
                        'discount_type' => $discType,
                    ];
                }
            }

            foreach (array_values($aggregatedItems) as $item) {
                $target = $this->resolveItemTarget($item['product_id']);
                if (!$target['exists']) {
                    \Log::warning('updateSalesOrder: skipping item with non-existent product_id=' . $item['product_id']);
                    continue;
                }

                $gross = $item['quantity'] * $item['price'];
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                if ($discType === 'percentage') {
                    $discAmount = $gross * ($discVal / 100);
                } else {
                    $discAmount = $discVal;
                }
                $subtotal = max(0, $gross - $discAmount);
                $totalAmount += $subtotal;

                \App\Models\SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'book_id' => $target['book_id'],
                    'bundle_id' => $target['bundle_id'],
                    'book_index_id' => $target['book_index_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount_value' => $discVal,
                    'discount_type' => $discType,
                    'discount_amount' => $discAmount,
                    'subtotal' => $subtotal,
                    'unit' => $item['unit'] ?? 'pcs',
                    'area' => $item['area'] ?? null,
                    'source_price_at_sale' => $target['source_price'],
                ]);
            }
        }

        $discountAmount = 0;
        $discountPercentage = 0;
        if ($request->filled('discount_value') && $request->discount_value > 0) {
            $discountValue = (float) $request->discount_value;
            if ($request->discount_type === 'percentage') {
                $discountPercentage = $discountValue;
                $discountAmount = $totalAmount * ($discountPercentage / 100);
            } else {
                $discountAmount = $discountValue;
                $discountPercentage = 0;
            }
        }

        $finalTotal = $totalAmount - $discountAmount;

        if (($validated['freight_option'] ?? null) === 'freight_collect') {
            $finalTotal += 50.00;
        }

        $finalTotal += $so->freight_charges ?? 0;

        $so->update([
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage ?? 0,
            'total_amount' => max(0, $finalTotal)
        ]);

        return redirect()->route('marketing.sales-orders.list')->with('success', 'Sales Order updated successfully!');
    }

    public function directInvoiceWebsite()
    {
        $customers = \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get();
        $products = $this->getUnifiedProducts();
        $invoices = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'website_direct')
            ->latest()
            ->get();

        return view('marketing.direct-invoice-website', [
            'title' => 'Direct Invoice (Website)',
            'role' => auth()->user()->position ?? 'Marketing Staff',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'invoices' => $invoices,
        ]);
    }

    public function storeDirectInvoice(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'transaction_subtype' => 'required|in:foreign,local',
            'items' => 'required|array|min:1|max:24',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'terms' => 'nullable|string',
            'remarks' => 'nullable|string',
            'proof_of_payment' => 'required|file|max:10240',
            'order_list' => 'required|file|max:10240',
        ]);

        if (count(array_filter($request->items ?? [], fn($i) => !empty($i['product_id']))) > 24) {
            return redirect()->back()->with('error', 'Cannot proceed with Direct Invoice: Maximum of 24 products allowed per order.')->withInput();
        }

        // STOCK VALIDATION: Check if all items have sufficient stock
        $insufficientItems = [];
        foreach ($request->items as $item) {
            if (empty($item['product_id'])) continue;
            $target = $this->resolveItemTarget($item['product_id']);
            if (!$target['exists'] || $target['stock'] < $item['quantity']) {
                $bookName = $target['name'];
                $availableStock = $target['stock'];
                $insufficientItems[] = "$bookName (Available: $availableStock pcs, Requested: {$item['quantity']} pcs)";
            }
        }

        if (!empty($insufficientItems)) {
            return redirect()->back()->with('error', 'Insufficient stock for the following items: ' . implode('<br>• ', $insufficientItems));
        }

        // Generate unique invoice number
        $lastInvoice = \App\Models\SalesOrder::where('type', 'website_direct')
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = $lastInvoice ? (intval(substr($lastInvoice->so_number, -4)) + 1) : 1;
        $invoiceNumber = 'DI-WEB-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Store attachments
        $popPath = $request->file('proof_of_payment')->store('direct_invoices/pop', 'public');
        $olPath = $request->file('order_list')->store('direct_invoices/order_lists', 'public');

        // Determine initial status based on transaction subtype and role
        $user = auth()->user();
        $isManagerOrSupervisor = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';

        if ($request->transaction_subtype === 'foreign') {
            // Foreign → Production Manager/Supervisor
            $initialStatus = $isManagerOrSupervisor ? 'picking' : 'pending_prod_approval';
        } else {
            // Local → Marketing Manager/Supervisor
            $initialStatus = $isManagerOrSupervisor ? 'picking' : 'pending_mkt_approval';
        }

        // Create the Sales Order (Invoice)
        $so = \App\Models\SalesOrder::create([
            'customer_id' => $request->customer_id,
            'so_number' => $invoiceNumber,
            'type' => 'website_direct',
            'transaction_subtype' => $request->transaction_subtype,
            'status' => $initialStatus,
            'prepared_by' => auth()->id(),
            'approved_by_mkt' => ($isManagerOrSupervisor && $request->transaction_subtype === 'local') ? auth()->id() : null,
            'approved_by_prod' => ($isManagerOrSupervisor && $request->transaction_subtype === 'foreign') ? auth()->id() : null,
            'mkt_approved_at' => ($isManagerOrSupervisor && $request->transaction_subtype === 'local') ? now() : null,
            'prod_approved_at' => ($isManagerOrSupervisor && $request->transaction_subtype === 'foreign') ? now() : null,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'terms' => $request->terms,
            'remarks' => $request->remarks,
            'proof_of_payment' => $popPath,
            'order_list_attachment' => $olPath,
        ]);

        // Create items
        $totalAmount = 0;
        $aggregatedWebItems = [];
        foreach ($request->items as $item) {
            if (empty($item['product_id'])) continue;
            $pid = $item['product_id'];
            $discVal = (float)($item['discount_value'] ?? 0);
            $discType = $item['discount_type'] ?? 'percentage';
            $key = $pid . '_' . $discVal . '_' . $discType;

            if (isset($aggregatedWebItems[$key])) {
                $aggregatedWebItems[$key]['quantity'] += (int) $item['quantity'];
            } else {
                $aggregatedWebItems[$key] = [
                    'product_id' => $pid,
                    'quantity' => (int) $item['quantity'],
                    'price' => (float) $item['price'],
                    'discount_value' => $discVal,
                    'discount_type' => $discType,
                    'unit' => $item['unit'] ?? 'pcs',
                ];
            }
        }

        foreach (array_values($aggregatedWebItems) as $item) {
            $gross = $item['quantity'] * $item['price'];
            $discVal = $item['discount_value'];
            $discType = $item['discount_type'];

            $discAmount = 0;
            if ($discType === 'percentage') {
                $discAmount = $gross * ($discVal / 100);
            } else {
                $discAmount = $discVal;
            }

            $subtotal = max(0, $gross - $discAmount);
            $totalAmount += $subtotal;

            $target = $this->resolveItemTarget($item['product_id']);
            \App\Models\SalesOrderItem::create([
                'sales_order_id' => $so->id,
                'book_id' => $target['book_id'],
                'bundle_id' => $target['bundle_id'],
                'book_index_id' => $target['book_index_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount_value' => $discVal,
                'discount_type' => $discType,
                'discount_amount' => $discAmount,
                'subtotal' => $subtotal,
                'unit' => $item['unit'] ?? 'pcs',
                'source_price_at_sale' => $target['source_price'],
            ]);
        }

        $so->update(['total_amount' => $totalAmount]);

        // Deduct stock immediately upon Sales Order creation
        \App\Services\StockDeductionService::deductForSalesOrder($so);

        $statusMsg = $initialStatus === 'picking'
            ? 'Invoice created and auto-approved! Routed to Logistics for picking.'
            : 'Invoice created and submitted for approval.';

        return redirect()->route('marketing.direct-invoice.website')->with('success', $statusMsg . ' Invoice #' . $invoiceNumber);
    }

    public function directInvoiceList()
    {
        $invoices = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'website_direct')
            ->latest()
            ->paginate(15);

        return view('marketing.direct-invoice-list', [
            'title' => 'Website Invoices',
            'role' => auth()->user()->position ?? 'Marketing Staff',
            'sidebar' => 'marketing',
            'invoices' => $invoices,
        ]);
    }

    public function approveDirectInvoice(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if ($order->type !== 'website_direct') {
            return redirect()->back()->with('error', 'This is not a website direct invoice.');
        }

        $user = auth()->user();
        $isManager = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';

        if (!$isManager) {
            return redirect()->back()->with('error', 'Only Managers or Supervisors can approve invoices.');
        }

        // Foreign: Production Manager approves
        // Local: Marketing Manager approves
        if ($order->transaction_subtype === 'foreign') {
            $order->update([
                'status' => 'picking',
                'approved_by_prod' => auth()->id(),
                'prod_approved_at' => now(),
            ]);
        } else {
            $order->update([
                'status' => 'picking',
                'approved_by_mkt' => auth()->id(),
                'mkt_approved_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Invoice #' . $order->so_number . ' approved! Routed to Logistics for picking.');
    }

    private function resolvePlatformSite($platformStr)
    {
        $platformStr = strtolower(trim($platformStr));
        if (str_contains($platformStr, 'lazada')) {
            return \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%lazada%'])->orWhereRaw('LOWER(code) LIKE ?', ['%lzd%'])->first();
        }
        if (str_contains($platformStr, 'shope') || str_contains($platformStr, 'shoppe')) {
            return \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%shop%'])->orWhereRaw('LOWER(code) LIKE ?', ['%shp%'])->first();
        }
        if (str_contains($platformStr, 'tiktok') || str_contains($platformStr, 'tik')) {
            return \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%tik%'])->orWhereRaw('LOWER(code) LIKE ?', ['%tik%'])->first();
        }
        if (str_contains($platformStr, 'cob')) {
            return \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%cob%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%consignment%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%bookstore%'])
                ->orWhereRaw('LOWER(code) LIKE ?', ['%cob%'])
                ->first();
        }
        return \App\Models\Site::whereRaw('LOWER(name) LIKE ?', ['%main%'])->first();
    }

    public function directInvoiceEcom(\Illuminate\Http\Request $request)
    {
        $customers = \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get();
        $products = $this->getUnifiedProducts();

        $lazadaSite = $this->resolvePlatformSite('lazada');
        $shopeeSite = $this->resolvePlatformSite('shopee');
        $tiktokSite = $this->resolvePlatformSite('tiktok');
        $cobSite    = $this->resolvePlatformSite('cob');
        $mainSite   = $this->resolvePlatformSite('main');

        $lazadaSiteId = $lazadaSite?->id ?? 3;
        $shopeeSiteId = $shopeeSite?->id ?? 4;
        $tiktokSiteId = $tiktokSite?->id ?? 5;
        $cobSiteId    = $cobSite?->id ?? 6;
        $mainSiteId   = $mainSite?->id ?? 1;

        // Load stocks for specific platform sites: Lazada, Shopee, TikTok, COB, Main (for Books, Indices, and Bundles)
        foreach ($products as $product) {
            $realId = $product->real_id ?? $product->id;
            $pType  = strtolower($product->type ?? 'book');

            $colName = 'book_id';
            if ($pType === 'book_index' || $pType === 'index') {
                $colName = 'book_index_id';
            } elseif ($pType === 'bundle' || $pType === 'book_bundle') {
                $colName = 'book_bundle_id';
            }

            $product->lazada_stock = \DB::table('site_inventory')->where($colName, $realId)->where('site_id', $lazadaSiteId)->value('quantity') ?? 0;
            $product->shopee_stock = \DB::table('site_inventory')->where($colName, $realId)->where('site_id', $shopeeSiteId)->value('quantity') ?? 0;
            $product->tiktok_stock = \DB::table('site_inventory')->where($colName, $realId)->where('site_id', $tiktokSiteId)->value('quantity') ?? 0;
            $product->cob_stock    = \DB::table('site_inventory')->where($colName, $realId)->where('site_id', $cobSiteId)->value('quantity') ?? 0;
            $product->main_stock   = \DB::table('site_inventory')->where($colName, $realId)->where('site_id', $mainSiteId)->value('quantity') ?? 0;
        }

        $query = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'ecom_direct');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'LIKE', "%{$search}%")
                  ->orWhere('platform_order_id', 'LIKE', "%{$search}%")
                  ->orWhere('remarks', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        if ($request->filled('platform')) {
            $query->where('ecom_platform', $request->input('platform'));
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        return view('marketing.direct-invoice-ecom', [
            'title' => 'Direct Invoice (E-com)',
            'role' => auth()->user()->position ?? 'Marketing Staff',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'invoices' => $invoices,
        ]);
    }

    public function storeDirectInvoiceEcom(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable',
            'ecom_platform' => 'required|in:lazada,shopee,tiktok,cob',
            'platform_order_id' => 'nullable|string|max:255',
            'items' => 'required|array|min:1|max:24',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'terms' => 'nullable|string',
            'pick_list' => 'nullable|file|max:10240',
            'proof_of_payment' => 'nullable|file|max:10240',
            'shipping_label' => 'nullable|file|max:10240',
            'remarks' => 'nullable|string',
        ]);

        // Determine platform site (Lazada, Shopee, Tiktok, COB)
        $targetSite = $this->resolvePlatformSite($request->ecom_platform);
        if (!$targetSite) {
            $targetSite = \App\Models\Site::where('name', 'Main Warehouse')->first();
        }

        if (count(array_filter($request->items ?? [], fn($i) => !empty($i['product_id']))) > 24) {
            return redirect()->back()->with('error', 'Cannot proceed with Direct Invoice: Maximum of 24 products allowed per order.')->withInput();
        }

        // STOCK VALIDATION: Check if items have sufficient stock at the chosen platform site
        $insufficientItems = [];
        foreach ($request->items as $item) {
            if (empty($item['product_id'])) continue;
            $target = $this->resolveItemTarget($item['product_id']);

            $siteInv = null;
            if ($targetSite) {
                $query = \App\Models\SiteInventory::where('site_id', $targetSite->id);
                if ($target['book_id']) {
                    $query->where('book_id', $target['book_id']);
                } elseif ($target['book_index_id']) {
                    $query->where('book_index_id', $target['book_index_id']);
                } elseif ($target['bundle_id']) {
                    $query->where('book_bundle_id', $target['bundle_id']);
                } else {
                    $query = null;
                }
                $siteInv = $query ? $query->first() : null;
            }

            $siteStock = $siteInv ? (int)$siteInv->quantity : 0;

            if (!$target['exists'] || $siteStock < $item['quantity']) {
                $bookName = $target['name'];
                $siteNameLabel = $targetSite ? $targetSite->name : ucfirst($request->ecom_platform);
                $insufficientItems[] = "$bookName (Available at $siteNameLabel: $siteStock pcs, Requested: {$item['quantity']} pcs)";
            }
        }

        if (!empty($insufficientItems)) {
            return redirect()->back()->with('error', 'Insufficient stock for the following items:' . implode('<br>• ', $insufficientItems));
        }

        // Generate unique invoice number
       // $lastInvoice = \App\Models\SalesOrder::where('type', 'ecom_direct')
        //    ->orderBy('id', 'desc')
        //    ->first();
      //  $nextNum = $lastInvoice ? (intval(substr($lastInvoice->so_number, -4)) + 1) : 1;
      //  $invoiceNumber = 'DI-ECOM-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
      
      	$invoicePrefix = 'DI-ECOM-' . date('Y') . '-';

		$lastInvoiceNumber = \App\Models\SalesOrder::where(
        'so_number',
        'like',
        $invoicePrefix . '%'
    		)
    	->orderByRaw('CAST(RIGHT(so_number, 4) AS UNSIGNED) DESC')
    	->value('so_number');

		$nextNum = $lastInvoiceNumber
    	? (intval(substr($lastInvoiceNumber, -4)) + 1)
    	: 1;

		$invoiceNumber = $invoicePrefix .
    	str_pad($nextNum, 4, '0', STR_PAD_LEFT);
      
      
      
      

        // Store attachments (optional)
        $pickListPath = $request->hasFile('pick_list') ? $request->file('pick_list')->store('direct_invoices/pick_lists', 'public') : null;
        $proofOfPaymentPath = $request->hasFile('proof_of_payment') ? $request->file('proof_of_payment')->store('direct_invoices/proof_of_payments', 'public') : null;
        // $shippingLabelPath = $request->file('shipping_label')->store('direct_invoices/shipping_labels', 'public');

        // Direct E-Com Invoices appear on Sales Invoice and Pick List at the same time
        $initialStatus = 'picking';

        // Resolve platform merchant customer
        $platformName = ucfirst(strtolower($request->ecom_platform));
        if (strtolower($platformName) === 'tiktok') {
            $platformName = 'TikTok';
        }
        
        $customer = \App\Models\Customer::withTrashed()
            ->where('customer_name', $platformName)
            ->first();
            
        if ($customer) {
            if ($customer->trashed()) {
                $customer->restore();
            }
        } else {
            $customer = \App\Models\Customer::create([
                'customer_name' => $platformName,
                'company_name' => $platformName,
                'account_number' => 'CUST-ECOM-' . strtoupper($request->ecom_platform),
            ]);
        }
        $customerId = $customer->customer_id;

        $so = \App\Models\SalesOrder::create([
            'customer_id' => $customerId,
            'so_number' => $invoiceNumber,
            'type' => 'ecom_direct',
            'ecom_platform' => $request->ecom_platform,
            'platform_order_id' => $request->platform_order_id,
            'status' => $initialStatus,
            'prepared_by' => auth()->id(),
            'si_prepared_by' => auth()->id(),
            'si_prepared_at' => now(),
            'approved_by_mkt' => auth()->id(),
            'mkt_approved_at' => now(),
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'terms' => $request->terms,
            'pick_list_attachment' => $pickListPath,
            'proof_of_payment' => $proofOfPaymentPath,
            'remarks' => $request->remarks,
            // 'shipping_label_attachment' => $shippingLabelPath,
        ]);

        // Create items and deduct stock from platform site
        $totalAmount = 0;
        $aggregatedEcomItems = [];
        foreach ($request->items as $item) {
            if (empty($item['product_id'])) continue;
            $pid = $item['product_id'];
            $discVal = (float)($item['discount_value'] ?? 0);
            $discType = $item['discount_type'] ?? 'percentage';
            $key = $pid . '_' . $discVal . '_' . $discType;

            if (isset($aggregatedEcomItems[$key])) {
                $aggregatedEcomItems[$key]['quantity'] += (int) $item['quantity'];
            } else {
                $aggregatedEcomItems[$key] = [
                    'product_id' => $pid,
                    'quantity' => (int) $item['quantity'],
                    'price' => (float) $item['price'],
                    'discount_value' => $discVal,
                    'discount_type' => $discType,
                    'unit' => $item['unit'] ?? 'pcs',
                ];
            }
        }

        foreach (array_values($aggregatedEcomItems) as $item) {
            $gross = $item['quantity'] * $item['price'];
            $discVal = $item['discount_value'];
            $discType = $item['discount_type'];

            $discAmount = 0;
            if ($discType === 'percentage') {
                $discAmount = $gross * ($discVal / 100);
            } else {
                $discAmount = $discVal;
            }

            $subtotal = max(0, $gross - $discAmount);
            $totalAmount += $subtotal;

            $target = $this->resolveItemTarget($item['product_id']);
            $book = $target['book_id'] ? Book::find($target['book_id']) : null;
            \App\Models\SalesOrderItem::create([
                'sales_order_id' => $so->id,
                'book_id' => $target['book_id'],
                'bundle_id' => $target['bundle_id'],
                'book_index_id' => $target['book_index_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount_value' => $discVal,
                'discount_type' => $discType,
                'discount_amount' => $discAmount,
                'subtotal' => $subtotal,
                'unit' => $item['unit'] ?? 'pcs',
                'source_price_at_sale' => $target['source_price'],
            ]);

            // Deduct from specific platform site inventory (Lazada, Shopee, Tiktok, COB) for Books, Indices, and Bundles
            if ($targetSite) {
                $siteInvQuery = \App\Models\SiteInventory::where('site_id', $targetSite->id);
                $keyAttrs = ['site_id' => $targetSite->id];

                if ($target['book_id']) {
                    $siteInvQuery->where('book_id', $target['book_id']);
                    $keyAttrs['book_id'] = $target['book_id'];
                } elseif ($target['book_index_id']) {
                    $siteInvQuery->where('book_index_id', $target['book_index_id']);
                    $keyAttrs['book_index_id'] = $target['book_index_id'];
                } elseif ($target['bundle_id']) {
                    $siteInvQuery->where('book_bundle_id', $target['bundle_id']);
                    $keyAttrs['book_bundle_id'] = $target['bundle_id'];
                }

                $siteInv = $siteInvQuery->first();
                if (!$siteInv) {
                    $siteInv = new \App\Models\SiteInventory($keyAttrs);
                }
                $siteInv->quantity = max(0, ($siteInv->quantity ?? 0) - $item['quantity']);
                $siteInv->save();
            }

            // Deduct master stock & log transaction
            if ($target['book_id'] && $book) {
                $book->stock = max(0, ($book->stock ?? 0) - $item['quantity']);
                $book->save();
            } elseif ($target['book_index_id']) {
                $bookIndex = \App\Models\BookIndex::find($target['book_index_id']);
                if ($bookIndex && isset($bookIndex->stock)) {
                    $bookIndex->stock = max(0, ($bookIndex->stock ?? 0) - $item['quantity']);
                    $bookIndex->save();
                }
            } elseif ($target['bundle_id']) {
                $bundle = \App\Models\BookBundle::find($target['bundle_id']);
                if ($bundle && isset($bundle->stock)) {
                    $bundle->stock = max(0, ($bundle->stock ?? 0) - $item['quantity']);
                    $bundle->save();
                }
            }

            // Record inventory transaction
            \App\Models\InventoryTransaction::create([
                'book_id' => $target['book_id'],
                'book_index_id' => $target['book_index_id'],
                'book_bundle_id' => $target['bundle_id'],
                'type' => 'out',
                'quantity' => $item['quantity'],
                'location' => $targetSite ? $targetSite->name : 'Main Warehouse',
                'source' => 'Direct E-Com (' . ucfirst($request->ecom_platform) . ')',
                'reference_number' => $invoiceNumber,
                'unit_cost' => $target['source_price'] ?? 0,
                'total_cost' => $item['quantity'] * ($target['source_price'] ?? 0),
                'notes' => 'Direct E-Com Invoice #' . $invoiceNumber . ' - Platform: ' . ucfirst($request->ecom_platform),
                'status' => 'completed',
                'transaction_date' => now(),
                'user_id' => auth()->id()
            ]);
        }

        $so->update([
            'total_amount' => $totalAmount,
            'status' => 'picking'
        ]);

        // Automatically create a pick list for Direct E-Com Invoice so it appears in E-Commerce Pick Lists
        try {
            $so->load('items');
            if ($so->items && $so->items->count() > 0) {
                $existingPickList = \App\Models\PickList::where('sales_order_id', $so->id)->first();
                if (!$existingPickList) {
                    $pickList = \App\Models\PickList::create([
                        'sales_order_id'   => $so->id,
                        'pick_list_number' => 'PL-' . $so->so_number . '-' . date('YmdHis'),
                        'status'           => 'in_progress',
                        'prepared_by'      => auth()->id(),
                    ]);

                    foreach ($so->items as $item) {
                        \App\Models\PickListItem::create([
                            'pick_list_id'        => $item->sales_order_id ? $pickList->id : $pickList->id,
                            'sales_order_item_id' => $item->id,
                            'requested_qty'       => $item->quantity,
                            'picked_qty'          => 0,
                            'status'              => 'pending'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to automatically create pick list for Direct E-Com Invoice: ' . $e->getMessage());
        }

        return redirect()->route('marketing.direct-invoice.ecom')->with('success', 'Direct Invoice #' . $invoiceNumber . ' created successfully and routed to Sales Invoice & Pick List.');
    }

    public function approveDirectInvoiceEcom(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if ($order->type !== 'ecom_direct') {
            return redirect()->back()->with('error', 'This is not an E-com direct invoice.');
        }

        $user = auth()->user();
        $isManager = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';

        if (!$isManager) {
            return redirect()->back()->with('error', 'Only Managers or Supervisors can approve invoices.');
        }

        // Direct Invoice Ecom workflow: After approval, route to Sales Invoice (Accounting)
        $order->update([
            'status' => 'pending_si_prep',
            'approved_by_mkt' => auth()->id(),
            'mkt_approved_at' => now(),
        ]);

        return redirect()->route('admin-finance.accounting.sales-invoice')
            ->with('success', 'Invoice #' . $order->so_number . ' approved! It now appears in the Sales Invoice list for preparation.');
    }

    public function acknowledgementReceipt()
    {
        return view('marketing.acknowledgement-receipt', [
            'title' => 'Acknowledgement Receipt',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function creditMemo()
    {
        return view('marketing.credit-memo', [
            'title' => 'Credit Memo Form',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function proofOfPayment()
    {
        return view('marketing.proof-of-payment', [
            'title' => 'Proof of Payment',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function salesInvoice()
    {
        return view('marketing.sales-invoice', [
            'title' => 'Sales Invoice',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function pickListManagement()
    {
        return view('marketing.pick-list-management', [
            'title' => 'Pick List Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function pickLists()
    {
        return view('marketing.pick-lists', [
            'title' => 'Pick Lists',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function deliveryReceipt()
    {
        // Get sales orders that need delivery receipt
        $salesOrders = \App\Models\SalesOrder::with('customer', 'items.product')
            ->whereIn('status', ['gathered', 'pending_si_prep', 'pending_si_approval', 'ready_for_delivery'])
            ->latest()
            ->get();
        
        // Get all customers
        $customers = \App\Models\Customer::orderBy('customer_name')->get();

        // Get existing delivery receipts with related data
        $deliveryReceipts = \App\Models\DeliveryReceipt::with('salesOrder', 'salesInvoice', 'customer', 'items', 'preparedByUser')
            ->latest()
            ->get();

        return view('marketing.delivery-receipt', [
            'title' => 'Delivery Receipt',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'salesOrders' => $salesOrders,
            'customers' => $customers,
            'deliveryReceipts' => $deliveryReceipts
        ]);
    }

    public function deliveryReceiptList()
    {
        return view('marketing.delivery-receipt-list', [
            'title' => 'Delivery Receipts',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function orderFulfillment()
    {
        return view('marketing.order-fulfillment', [
            'title' => 'Order Fulfillment',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function packingScheduling()
    {
        return view('marketing.packing-scheduling', [
            'title' => 'Packing & Scheduling',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function deliveryScheduling()
    {
        return view('marketing.delivery-scheduling', [
            'title' => 'Delivery Scheduling',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function deliveryTracking()
    {
        return view('marketing.delivery-tracking', [
            'title' => 'Delivery Tracking',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function salesReports()
    {
        return view('marketing.sales-reports', [
            'title' => 'Sales Reports',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function territoryManagement()
    {
        return view('marketing.territory-management', [
            'title' => 'Territory Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    // Direct Sales
    public function posSale()
    {
        $products = Book::where('is_active', true)
            ->withSum('inventory as stock', 'quantity')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'type' => $p->is_book ? 'book' : 'non-book',
                    'category' => $p->is_book ? 'books' : 'non-books',
                    'name' => $p->name,
                    'price' => (float)$p->price,
                    'barcode' => $p->barcode,
                    'sku' => $p->sku,
                    'stock' => $p->stock,
                    'image' => $p->image ? asset('storage/' . $p->image) : asset('images/no-book-cover.svg')
                ];
            });

        $indices = \App\Models\BookIndex::with('book')
            ->get()
            ->map(function($idx) {
                $price = (float) (($idx->price && $idx->price > 0) ? $idx->price : ($idx->book?->price ?? 0));
                $img = $idx->book?->image ? asset('storage/' . $idx->book->image) : asset('images/no-book-cover.svg');
                $mainStock = (int) ($idx->main_stock ?? $idx->stock ?? 0);
                return [
                    'id' => $idx->id,
                    'type' => 'index',
                    'category' => 'indices',
                    'name' => $idx->display_name,
                    'price' => $price,
                    'barcode' => $idx->barcode ?: ($idx->article ?: ($idx->nbs_barcode ?: '')),
                    'sku' => $idx->article ?: '',
                    'stock' => $mainStock,
                    'image' => $img
                ];
            });

        $bundles = \App\Models\BookBundle::where('is_active', true)
            ->with(['books' => function ($q) {
                $q->withPivot('quantity')
                  ->withSum('inventory as stock', 'quantity');
            }])
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($b) {
                return [
                    'id' => $b->id,
                    'type' => 'bundle',
                    'category' => 'bundle',
                    'name' => $b->name,
                    'sku' => $b->sku,
                    'price' => (float)$b->price,
                    'stock' => $b->stock,
                    'image' => asset('images/no-book-cover.svg'),
                    'books' => $b->books->map(fn($book) => [
                        'id' => $book->id,
                        'name' => $book->name,
                        'stock' => $book->stock,
                        'quantity' => $book->pivot->quantity,
                    ])->values(),
                ];
            });

        return view('marketing.direct-sales.pos', [
            'products' => $products,
            'indices' => $indices,
            'bundles' => $bundles,
            'customers' => \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get(),
            'nextSiNumber' => \App\Http\Controllers\POSController::getNextSiNumber('pos'),
            'title' => 'New Sale - Point of Sale',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }


    public function posProducts()
    {
        // Fetch all books for POS management
        $products = Book::withSum('inventory as stock', 'quantity')
            ->orderBy('name', 'asc')
            ->get();

        return view('marketing.direct-sales.products', [
            'products' => $products,
            'title' => 'POS Products Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    // MIBF POS
    public function ecomPos()
    {
        $mibfStocks = \App\Models\TeamStock::where('team_name', 'MIBF')->get();
        $bookStocksMap = $mibfStocks->whereNotNull('book_id')->pluck('quantity', 'book_id')->toArray();
        $indexStocksMap = $mibfStocks->whereNotNull('book_index_id')->pluck('quantity', 'book_index_id')->toArray();
        $bundleStocksMap = $mibfStocks->whereNotNull('book_bundle_id')->pluck('quantity', 'book_bundle_id')->toArray();

        // 1. Books (is_book = true)
        $books = Book::where('is_active', true)->where('is_book', true)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($b) use ($bookStocksMap) {
                $stock = (int) ($bookStocksMap[$b->id] ?? 0);
                $effectivePrice = ($b->mibf_price !== null && $b->mibf_price > 0) ? (float)$b->mibf_price : (float)$b->price;
                return [
                    'id' => 'book_' . $b->id,
                    'type' => 'book',
                    'real_id' => $b->id,
                    'book_id' => $b->id,
                    'category' => 'books',
                    'name' => $b->name,
                    'price' => $effectivePrice,
                    'stock' => $stock,
                    'barcode' => $b->barcode ?: ($b->isbn ?: ($b->sku ?: '')),
                    'image' => $b->image ? asset('storage/' . $b->image) : asset('images/no-book-cover.svg')
                ];
            });

        // 2. Book Indices
        $indices = \App\Models\BookIndex::with('book')->get()
            ->map(function($idx) use ($indexStocksMap) {
                $stock = (int) ($indexStocksMap[$idx->id] ?? 0);
                $normalPrice = (float) (($idx->price && $idx->price > 0) ? $idx->price : ($idx->book?->price ?? 0));
                $effectivePrice = ($idx->mibf_price !== null && $idx->mibf_price > 0) ? (float)$idx->mibf_price : $normalPrice;
                $img = $idx->book?->image ? asset('storage/' . $idx->book->image) : asset('images/no-book-cover.svg');
                return [
                    'id' => 'index_' . $idx->id,
                    'type' => 'index',
                    'real_id' => $idx->id,
                    'book_id' => $idx->book_id,
                    'book_index_id' => $idx->id,
                    'category' => 'indices',
                    'name' => $idx->display_name,
                    'price' => $effectivePrice,
                    'stock' => $stock,
                    'barcode' => $idx->barcode ?: ($idx->article ?: ($idx->nbs_barcode ?: '')),
                    'image' => $img
                ];
            });

        // 3. Non-Books (is_book = false)
        $nonBooks = Book::where('is_active', true)->where('is_book', false)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($nb) use ($bookStocksMap) {
                $stock = (int) ($bookStocksMap[$nb->id] ?? 0);
                $effectivePrice = ($nb->mibf_price !== null && $nb->mibf_price > 0) ? (float)$nb->mibf_price : (float)$nb->price;
                return [
                    'id' => 'book_' . $nb->id,
                    'type' => 'book',
                    'real_id' => $nb->id,
                    'book_id' => $nb->id,
                    'category' => 'non-books',
                    'name' => $nb->name,
                    'price' => $effectivePrice,
                    'stock' => $stock,
                    'barcode' => $nb->barcode ?: ($nb->sku ?: ($nb->item_code ?: '')),
                    'image' => $nb->image ? asset('storage/' . $nb->image) : asset('images/no-book-cover.svg')
                ];
            });

        // 4. Book Bundles
        $bundles = \App\Models\BookBundle::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($bun) use ($bundleStocksMap) {
                $stock = (int) ($bundleStocksMap[$bun->id] ?? 0);
                $effectivePrice = ($bun->mibf_price !== null && $bun->mibf_price > 0) ? (float)$bun->mibf_price : (float)$bun->price;
                return [
                    'id' => 'bundle_' . $bun->id,
                    'type' => 'bundle',
                    'real_id' => $bun->id,
                    'book_bundle_id' => $bun->id,
                    'category' => 'bundle',
                    'name' => $bun->name . ' (bundle)',
                    'price' => $effectivePrice,
                    'stock' => $stock,
                    'barcode' => $bun->sku ?: '',
                    'image' => asset('images/no-book-cover.svg')
                ];
            });

        $products = $books->concat($indices)->concat($nonBooks)->concat($bundles)->values();

        return view('marketing.ecom.pos', [
            'title' => 'MIBF POS',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'products' => $products,
            'nextSiNumber' => \App\Http\Controllers\POSController::getNextSiNumber('mibf'),
            'customers' => \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get()
        ]);
    }

    // Suppliers & Purchases
    public function suppliers()
    {
        return view('marketing.suppliers', [
            'title' => 'Supplier Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }


    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // This will now be permanent because SoftDeletes was removed from model

        return response()->json(['message' => 'Product deleted and erased from database successfully']);
    }

    public function destroySalesOrder($id)
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admin can delete sales orders.');
        }

        $so = \App\Models\SalesOrder::findOrFail($id);
        
        // 1. Restore stock if already gathered/deducted
        \App\Http\Controllers\Production\LogisticController::restoreSalesOrderStock($so);

        // 2. Delete associated pick lists & pick list items
        $pickLists = \App\Models\PickList::where('sales_order_id', $so->id)->get();
        foreach ($pickLists as $pl) {
            \App\Models\PickListItem::where('pick_list_id', $pl->id)->delete();
            $pl->delete();
        }

        // 3. Delete order items and sales order
        $so->items()->delete();
        $so->delete();

        return redirect()->route('marketing.sales-orders.list')->with('success', 'Sales Order deleted successfully! Deducted stock (if any) has been returned to inventory.');
    }

    // Ads and Promo
    public function adsPromoCampaigns()
    {
        return view('marketing.ads-promo.campaigns');
    }

    public function adsPromoPromotions()
    {
        return view('marketing.ads-promo.promotions', [
            'title' => 'Promotions',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function crpr()
    {
        return view('marketing.ads-promo.crpr', [
            'title' => 'Marketing Plan Itinerary Budget',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function sponsors()
    {
        $sponsors = \App\Models\Sponsor::latest()->get();
        return view('marketing.ads-promo.sponsors', [
            'title' => 'List of Sponsors',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'sponsors' => $sponsors
        ]);
    }

    public function storeSponsor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'remarks' => 'nullable|string',
            'contact_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('contact_file')) {
            $path = $request->file('contact_file')->store('sponsors', 'public');
            $validated['file_path'] = $path;
        }

        \App\Models\Sponsor::create($validated);

        return redirect()->back()->with('success', 'Sponsor added successfully.');
    }

    public function updateSponsor(Request $request, $id)
    {
        $sponsor = \App\Models\Sponsor::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'remarks' => 'nullable|string',
            'contact_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('contact_file')) {
            if ($sponsor->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sponsor->file_path);
            }
            $path = $request->file('contact_file')->store('sponsors', 'public');
            $validated['file_path'] = $path;
        }

        $sponsor->update($validated);

        return redirect()->back()->with('success', 'Sponsor updated successfully.');
    }

    public function destroySponsor($id)
    {
        $sponsor = \App\Models\Sponsor::findOrFail($id);
        
        if ($sponsor->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($sponsor->file_path);
        }
        
        $sponsor->delete();

        return redirect()->back()->with('success', 'Sponsor deleted successfully.');
    }

    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);
        
        try {
            // Delete associated POS listing if exists
            if ($book->product) {
                $book->product->delete();
            }

            // Attempt to force delete to allow SKU reuse
            $book->forceDelete();

            return response()->json(['message' => 'Book deleted permanently from Master Registry']);
        } catch (\Exception $e) {
            // Refetch the book to reset any Eloquent internal flags (like forceDeleting)
            $book = Book::findOrFail($id);

            // Fallback: Soft delete if book is referenced by other tables (sales invoices, transactions, etc.)
            // Rename SKU and other unique fields to allow SKU reuse
            $timestamp = time();
            $book->sku = $book->sku . '-DELETED-' . $timestamp;
            
            if ($book->item_code) {
                $book->item_code = $book->item_code . '-DELETED-' . $timestamp;
            }
            if ($book->barcode) {
                $book->barcode = $book->barcode . '-DELETED-' . $timestamp;
            }
            if ($book->nbs_barcode) {
                $book->nbs_barcode = $book->nbs_barcode . '-DELETED-' . $timestamp;
            }
            
            $book->save();
            $book->delete();

            return response()->json(['message' => 'Book is referenced in transactions and has been safely archived. SKU is now free to be reused.']);
        }
    }

    public function getCategories()
    {
        $categories = BookCategory::whereNull('parent_id')->with('children')->orderBy('name', 'asc')->get();
        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:book_categories,id',
        ]);

        $category = BookCategory::create($validated);

        return response()->json([
            'message' => 'Category added successfully',
            'category' => $category
        ]);
    }

    public function getSubcategories($id)
    {
        $subcategories = BookCategory::where('parent_id', $id)->orderBy('name', 'asc')->get();
        return response()->json($subcategories);
    }

    public function destroyCategory($id)
    {
        $category = BookCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function storeBundle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:book_bundles,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'mibf_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $bundle = \App\Models\BookBundle::create([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'mibf_price' => $request->filled('mibf_price') ? $validated['mibf_price'] : null,
            'stock' => $validated['stock'],
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        foreach ($validated['items'] as $item) {
            $bundle->books()->attach($item['book_id'], ['quantity' => $item['quantity']]);
        }

        return response()->json([
            'message' => 'Book bundle created successfully',
            'bundle' => $bundle
        ]);
    }

    public function editBundle($id)
    {
        $bundle = \App\Models\BookBundle::with('books')->findOrFail($id);
        return response()->json($bundle);
    }

    public function updateBundle(Request $request, $id)
    {
        $bundle = \App\Models\BookBundle::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:book_bundles,sku,' . $bundle->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'mibf_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $bundle->update([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'mibf_price' => $request->filled('mibf_price') ? $validated['mibf_price'] : null,
            'stock' => $validated['stock'],
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        $syncData = [];
        foreach ($validated['items'] as $item) {
            $syncData[$item['book_id']] = ['quantity' => $item['quantity']];
        }
        $bundle->books()->sync($syncData);

        return response()->json([
            'message' => 'Book bundle updated successfully',
            'bundle' => $bundle
        ]);
    }

    public function destroyBundle($id)
    {
        \DB::transaction(function() use ($id) {
            $bundle = \App\Models\BookBundle::findOrFail($id);
            \App\Models\StockTransfer::where('book_bundle_id', $bundle->id)->update(['book_bundle_id' => null]);
            \App\Models\SiteInventory::where('book_bundle_id', $bundle->id)->delete();
            $bundle->delete();
        });

        return response()->json([
            'message' => 'Book bundle deleted successfully'
        ]);
    }

    public function storeNonBook(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:books,sku',
            'barcode' => 'nullable|string',
            'nbs_barcode' => 'nullable|string',
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'sub_category' => 'nullable|string',
            'size' => 'nullable|string',
            'pages' => 'nullable|integer',
            'cover_type' => 'nullable|string',
            'book_type' => 'nullable|string',
            'copyright' => 'nullable|string',
            'weight' => 'nullable|string',
            'stock' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'max_stock' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'mibf_price' => 'nullable|numeric|min:0',
            'shelf_number' => 'nullable|string|max:50',
            'rack_number' => 'nullable|string|max:50',
            'category' => 'nullable|string',
            'category_id' => 'nullable|exists:book_categories,id',
            'sub_category_id' => 'nullable|exists:book_categories,id',
            'purchase_description' => 'nullable',
            'item_code' => 'nullable|string|unique:books,item_code',
            'email' => 'nullable|email',
            'contact_number' => 'nullable|string',
            'royalty' => 'nullable|string',
            'article' => 'nullable|string',
            'cogs_account' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        if (empty($validated['item_code'])) {
            $validated['item_code'] = null;
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = null;
        }

        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['reorder_point'] = $validated['reorder_point'] ?? 0;
        $validated['max_stock'] = $validated['max_stock'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['pages'] = $validated['pages'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['mibf_price'] = $request->filled('mibf_price') ? $validated['mibf_price'] : null;
        $validated['is_active'] = $request->has('is_active');
        $validated['is_book'] = false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        Book::create($validated);

        return response()->json(['message' => 'Non-Book added to Master Registry']);
    }

    public function editNonBook($id)
    {
        $book = Book::findOrFail($id);
        return response()->json($book);
    }

    public function updateNonBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:books,sku,' . $id,
            'barcode' => 'nullable',
            'nbs_barcode' => 'nullable|string',
            'author' => 'nullable',
            'publisher' => 'nullable',
            'size' => 'nullable',
            'pages' => 'nullable|integer',
            'unit' => 'nullable',
            'copyright' => 'nullable',
            'book_type' => 'nullable',
            'weight' => 'nullable',
            'cover_type' => 'nullable',
            'royalty' => 'nullable',
            'article' => 'nullable',
            'sub_category' => 'nullable',
            'email' => 'nullable|email',
            'contact_number' => 'nullable',
            'stock' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'max_stock' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'cogs_account' => 'nullable',
            'purchase_description' => 'nullable',
            'price' => 'nullable|numeric',
            'mibf_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string',
            'category_id' => 'nullable|exists:book_categories,id',
            'sub_category_id' => 'nullable|exists:book_categories,id',
            'item_code' => 'nullable|string|unique:books,item_code,' . $id,
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if (empty($validated['item_code'])) {
            $validated['item_code'] = null;
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = null;
        }

        $validated['stock'] = $validated['stock'] ?? $book->stock;
        $validated['reorder_point'] = $validated['reorder_point'] ?? 0;
        $validated['max_stock'] = $validated['max_stock'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['pages'] = $validated['pages'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['mibf_price'] = $request->filled('mibf_price') ? $validated['mibf_price'] : null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated);

        return response()->json(['message' => 'Master Non-Book entry updated successfully']);
    }

    public function destroyNonBook($id)
    {
        $book = Book::findOrFail($id);
        
        try {
            if ($book->product) {
                $book->product->delete();
            }

            $book->forceDelete();

            return response()->json(['message' => 'Non-Book deleted permanently from Master Registry']);
        } catch (\Exception $e) {
            $book = Book::findOrFail($id);

            $timestamp = time();
            $book->sku = $book->sku . '-DELETED-' . $timestamp;
            
            if ($book->item_code) {
                $book->item_code = $book->item_code . '-DELETED-' . $timestamp;
            }
            if ($book->barcode) {
                $book->barcode = $book->barcode . '-DELETED-' . $timestamp;
            }
            if ($book->nbs_barcode) {
                $book->nbs_barcode = $book->nbs_barcode . '-DELETED-' . $timestamp;
            }
            
            $book->save();
            $book->delete();

            return response()->json(['message' => 'Non-Book is referenced in transactions and has been safely archived. SKU is now free to be reused.']);
        }
    }

    /**
     * Area Sales - Team Stocks Index Page
     */
    public function teamStocksIndex()
    {
        $teamStocks = \App\Models\TeamStock::with(['book', 'bookIndex.book', 'bookBundle'])->get();
        $transfers = \App\Models\TeamStockTransfer::with(['transferredByUser', 'items.book', 'items.bookIndex', 'items.bookBundle'])
            ->latest()
            ->get();
        $teamUsers = \App\Models\User::whereNotNull('sales_team')->get();
        $mainProducts = $this->getUnifiedProducts();

        $teamStockJsonData = $teamStocks->where('quantity', '>', 0)->map(function($ts) {
            $barcodes = [];
            if ($ts->book) {
                foreach (['barcode', 'sku', 'item_code', 'nbs_barcode'] as $f) {
                    if (!empty($ts->book->$f)) $barcodes[] = (string)$ts->book->$f;
                }
            }
            if ($ts->bookIndex) {
                foreach (['barcode', 'nbs_barcode', 'article'] as $f) {
                    if (!empty($ts->bookIndex->$f)) $barcodes[] = (string)$ts->bookIndex->$f;
                }
                if ($ts->bookIndex->book) {
                    foreach (['barcode', 'sku', 'item_code', 'nbs_barcode'] as $f) {
                        if (!empty($ts->bookIndex->book->$f)) $barcodes[] = (string)$ts->bookIndex->book->$f;
                    }
                }
            }
            if ($ts->bookBundle) {
                if (!empty($ts->bookBundle->sku)) $barcodes[] = (string)$ts->bookBundle->sku;
            }
            $uniqueBarcodes = array_values(array_unique(array_filter($barcodes)));

            $productId = $ts->book_index_id ? ('index_' . $ts->book_index_id) : ($ts->book_bundle_id ? ('bundle_' . $ts->book_bundle_id) : ('book_' . $ts->book_id));

            return [
                'id' => $ts->id,
                'team_name' => $ts->team_name,
                'product_id' => $productId,
                'product_name' => $ts->product_name,
                'available_qty' => (int)$ts->quantity,
                'barcodes' => $uniqueBarcodes,
            ];
        })->values();

        return view('marketing.area-sales.team-stocks', compact('teamStocks', 'transfers', 'teamUsers', 'mainProducts', 'teamStockJsonData'));
    }

    /**
     * Download blank Excel Template for Team Stock Transfers (Title, Barcode, Quantity to Transfer)
     */
    public function downloadTeamStockTransferTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Transfer Template');

        // Title & Instructions
        $sheet->setCellValue('A1', 'CLARETIAN ERP — TEAM STOCK TRANSFER TEMPLATE');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFD9251C']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->setCellValue('A2', 'Instructions: Enter the Title or Barcode (or Item Code / ISBN) and Quantity to Transfer for each item. Save and upload this file in the Transfer Stock modal.');
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF555555']],
        ]);

        // Table Headers (Row 4)
        $headers = [
            'A4' => 'Title',
            'B4' => 'Barcode',
            'C4' => 'Quantity to Transfer',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9251C']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ],
        ]);

        // 10 Blank rows ready for user input
        for ($row = 5; $row <= 15; $row++) {
            $sheet->setCellValue("A{$row}", '');
            $sheet->setCellValue("B{$row}", '');
            $sheet->setCellValue("C{$row}", '');

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE0E0E0']
                    ]
                ]
            ]);
            $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(25);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="Team_Stock_Transfer_Template.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Parse uploaded Excel file and match products by Barcode/Title for team stock transfer
     */
    public function parseTeamStockTransferExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt',
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $allProducts = $this->getUnifiedProducts();
            
            // Build lookups for Barcode and Title matching
            $productMapByBarcode = [];
            $productMapByName = [];

            foreach ($allProducts as $p) {
                $pId = is_object($p) ? $p->id : ($p['id'] ?? '');
                $pName = is_object($p) ? $p->name : ($p['name'] ?? '');
                $pStock = (int) (is_object($p) ? ($p->stock ?? $p->main_stock ?? 0) : ($p['stock'] ?? $p['main_stock'] ?? 0));
                $pIsbn = is_object($p) ? ($p->isbn ?? '') : ($p['isbn'] ?? '');

                $itemObj = [
                    'id' => $pId,
                    'name' => $pName,
                    'stock' => $pStock,
                ];

                $cleanBaseName = strtolower(trim(preg_replace('/\s*\((book|non-book|bundle)\)\s*$/i', '', $pName)));
                $cleanFullName = strtolower(trim(preg_replace('/\s+/', ' ', $pName)));

                if (!empty($cleanBaseName)) $productMapByName[$cleanBaseName] = $itemObj;
                if (!empty($cleanFullName)) $productMapByName[$cleanFullName] = $itemObj;

                if (!empty($pIsbn)) {
                    $productMapByBarcode[strtolower(trim((string)$pIsbn))] = $itemObj;
                }

                // Check actual model records for extra barcodes/item codes/article numbers
                if (str_starts_with($pId, 'book_')) {
                    $realId = str_replace('book_', '', $pId);
                    $bookModel = \App\Models\Book::find($realId);
                    if ($bookModel) {
                        foreach (['isbn', 'barcode', 'item_code', 'sku'] as $attr) {
                            if (!empty($bookModel->$attr)) {
                                $productMapByBarcode[strtolower(trim((string)$bookModel->$attr))] = $itemObj;
                            }
                        }
                    }
                } elseif (str_starts_with($pId, 'index_')) {
                    $realId = str_replace('index_', '', $pId);
                    $indexModel = \App\Models\BookIndex::with('book')->find($realId);
                    if ($indexModel) {
                        foreach (['article_number', 'isbn', 'barcode'] as $attr) {
                            if (!empty($indexModel->$attr)) {
                                $productMapByBarcode[strtolower(trim((string)$indexModel->$attr))] = $itemObj;
                            }
                        }
                        if ($indexModel->book) {
                            foreach (['isbn', 'barcode', 'item_code', 'sku'] as $attr) {
                                if (!empty($indexModel->book->$attr) && !isset($productMapByBarcode[strtolower(trim((string)$indexModel->book->$attr))])) {
                                    $productMapByBarcode[strtolower(trim((string)$indexModel->book->$attr))] = $itemObj;
                                }
                            }
                        }
                    }
                } elseif (str_starts_with($pId, 'bundle_')) {
                    $realId = str_replace('bundle_', '', $pId);
                    $bundleModel = \App\Models\BookBundle::find($realId);
                    if ($bundleModel) {
                        foreach (['sku', 'barcode'] as $attr) {
                            if (!empty($bundleModel->$attr)) {
                                $productMapByBarcode[strtolower(trim((string)$bundleModel->$attr))] = $itemObj;
                            }
                        }
                    }
                }
            }

            // Dynamically locate table headers and column mapping
            $titleCol = 0;
            $barcodeCol = 1;
            $qtyCol = 2;
            $startRowIndex = 0;

            foreach ($rows as $idx => $row) {
                if (!is_array($row)) continue;
                $foundHeader = false;
                foreach ($row as $colIdx => $val) {
                    $str = strtolower(trim((string)$val));
                    if (str_contains($str, 'title') || str_contains($str, 'product')) {
                        $titleCol = $colIdx;
                        $foundHeader = true;
                    }
                    if (str_contains($str, 'barcode') || str_contains($str, 'isbn') || str_contains($str, 'code')) {
                        $barcodeCol = $colIdx;
                        $foundHeader = true;
                    }
                    if (str_contains($str, 'quantity') || str_contains($str, 'qty') || str_contains($str, 'transfer')) {
                        $qtyCol = $colIdx;
                        $foundHeader = true;
                    }
                }
                if ($foundHeader) {
                    $startRowIndex = $idx + 1;
                    break;
                }
            }

            $importedItems = [];
            $unmatched = [];

            for ($i = $startRowIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (!is_array($row)) continue;

                $titleVal = trim((string)($row[$titleCol] ?? ''));
                $barcodeVal = trim((string)($row[$barcodeCol] ?? ''));
                $qtyVal = trim((string)($row[$qtyCol] ?? ''));

                // Extract numeric quantity
                $qty = 0;
                if (is_numeric($qtyVal) && (int)$qtyVal > 0) {
                    $qty = (int)$qtyVal;
                } else {
                    foreach ($row as $cVal) {
                        if (is_numeric(trim((string)$cVal)) && (int)trim((string)$cVal) > 0) {
                            $qty = (int)trim((string)$cVal);
                            break;
                        }
                    }
                }

                if ($qty <= 0) continue;

                $matched = null;

                // 1. Match by Barcode / ISBN / Item Code
                if (!empty($barcodeVal)) {
                    $cleanBc = strtolower(trim($barcodeVal));
                    if (isset($productMapByBarcode[$cleanBc])) {
                        $matched = $productMapByBarcode[$cleanBc];
                    }
                }

                // 2. Match by Title
                if (!$matched && !empty($titleVal)) {
                    $cleanTitle = strtolower(trim(preg_replace('/\s+/', ' ', $titleVal)));
                    $cleanTitleBase = strtolower(trim(preg_replace('/\s*\((book|non-book|bundle)\)\s*$/i', '', $cleanTitle)));

                    if (isset($productMapByName[$cleanTitleBase])) {
                        $matched = $productMapByName[$cleanTitleBase];
                    } elseif (isset($productMapByName[$cleanTitle])) {
                        $matched = $productMapByName[$cleanTitle];
                    } else {
                        foreach ($productMapByName as $nameKey => $prodObj) {
                            if (!empty($nameKey) && (str_contains($cleanTitleBase, $nameKey) || str_contains($nameKey, $cleanTitleBase))) {
                                $matched = $prodObj;
                                break;
                            }
                        }
                    }
                }

                // 3. Fallback: match by product ID if present
                if (!$matched && !empty($titleVal) && isset($allProducts[$titleVal])) {
                    $matched = $allProducts[$titleVal];
                }

                if ($matched) {
                    $importedItems[] = [
                        'product_id' => $matched['id'],
                        'product_name' => $matched['name'],
                        'stock' => $matched['stock'],
                        'quantity' => $qty,
                    ];
                } else {
                    $unmatched[] = !empty($titleVal) ? $titleVal : $barcodeVal;
                }
            }

            if (empty($importedItems)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No matching products with positive "Quantity to Transfer" were found in the uploaded file.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'items' => $importedItems,
                'count' => count($importedItems),
                'skipped' => count($unmatched),
                'message' => 'Successfully imported ' . count($importedItems) . ' item(s) from Excel file.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Area Sales - Execute Stock Transfer to Team
     */
    public function storeTeamStockTransfer(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string|in:Team A,Team B,Team C,Book Sales,MIBF',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        \DB::beginTransaction();
        try {
            $transferNumber = 'TST-' . date('Ymd') . '-' . strtoupper(\Str::random(4));
            $transfer = \App\Models\TeamStockTransfer::create([
                'transfer_number' => $transferNumber,
                'team_name' => $request->team_name,
                'transferred_by' => auth()->id(),
                'notes' => $request->notes,
                'status' => 'pending_mkt_approval',
            ]);

            foreach ($request->items as $itemData) {
                $target = $this->resolveItemTarget($itemData['product_id']);
                $qty = (int) $itemData['quantity'];

                // Check Main Warehouse stock availability
                if ($target['book_index_id']) {
                    $index = \App\Models\BookIndex::find($target['book_index_id']);
                    $availableStock = (int) ($index->stock ?? $index->quantity ?? 0);
                    if (!$index || $availableStock < $qty) {
                        throw new \Exception("Insufficient stock in Main Warehouse for " . ($index ? $index->display_name : 'selected item') . ". Available: " . $availableStock);
                    }
                } elseif ($target['book_id']) {
                    $book = \App\Models\Book::find($target['book_id']);
                    $availableStock = (int) ($book->stock ?? 0);
                    if (!$book || $availableStock < $qty) {
                        throw new \Exception("Insufficient stock in Main Warehouse for " . ($book ? $book->name : 'selected item') . ". Available: " . $availableStock);
                    }
                } elseif ($target['bundle_id']) {
                    $bundle = \App\Models\BookBundle::find($target['bundle_id']);
                    $availableStock = (int) ($bundle->stock ?? $bundle->quantity ?? 0);
                    if (!$bundle || $availableStock < $qty) {
                        throw new \Exception("Insufficient stock in Main Warehouse for " . ($bundle ? $bundle->name : 'selected item') . ". Available: " . $availableStock);
                    }
                }

                // Record transfer item
                \App\Models\TeamStockTransferItem::create([
                    'team_stock_transfer_id' => $transfer->id,
                    'book_id' => $target['book_id'],
                    'book_index_id' => $target['book_index_id'],
                    'book_bundle_id' => $target['bundle_id'],
                    'quantity' => $qty,
                ]);
            }

            \DB::commit();

            return redirect()->route('marketing.area-sales.team-stocks.index')
                ->with('success', 'Stock transfer request #' . $transferNumber . ' submitted! Sent to Marketing Approval Queue.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to execute stock transfer: ' . $e->getMessage());
        }
    }

    /**
     * Area Sales - Execute Stock Return from Team to Main Warehouse
     */
    public function storeTeamStockReturn(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.returned_qty' => 'required|integer|min:0',
            'items.*.lost_qty' => 'required|integer|min:0',
        ]);

        $teamName = trim($request->team_name);

        \DB::beginTransaction();
        try {
            $transferNumber = 'TSR-' . date('Ymd') . '-' . strtoupper(\Str::random(4));
            $transfer = \App\Models\TeamStockTransfer::create([
                'transfer_number' => $transferNumber,
                'transfer_type' => 'return',
                'team_name' => $teamName,
                'transferred_by' => auth()->id(),
                'notes' => $request->notes,
                'remarks' => 'Stock return from ' . $teamName . ' to Main Warehouse',
                'status' => 'completed',
            ]);

            $cleanName = trim(preg_replace('/^(site\s+|team\s+)+/i', '', $teamName));
            $variations = array_unique([
                $teamName,
                'Team ' . $cleanName,
                'SITE TEAM ' . strtoupper($cleanName),
                'SITE TEAM ' . $cleanName,
                'SITE ' . strtoupper($cleanName),
                'SITE ' . $cleanName,
                $cleanName,
                strtoupper($teamName),
                strtolower($teamName),
            ]);

            $totalReturned = 0;
            $totalLost = 0;

            foreach ($request->items as $itemData) {
                $returnedQty = (int) ($itemData['returned_qty'] ?? 0);
                $lostQty = (int) ($itemData['lost_qty'] ?? 0);
                $totalItemQty = $returnedQty + $lostQty;

                if ($totalItemQty <= 0) continue;

                $target = $this->resolveItemTarget($itemData['product_id']);

                // Find TeamStock record
                $ts = \App\Models\TeamStock::where(function($q) use ($variations) {
                    foreach ($variations as $var) {
                        $q->orWhere('team_name', $var)
                          ->orWhereRaw('LOWER(team_name) = ?', [strtolower($var)]);
                    }
                })->where(function($q) use ($target) {
                    if (!empty($target['book_index_id'])) $q->where('book_index_id', $target['book_index_id']);
                    elseif (!empty($target['book_id'])) $q->where('book_id', $target['book_id']);
                    elseif (!empty($target['bundle_id'])) $q->where('book_bundle_id', $target['bundle_id']);
                })->first();

                $currentTeamStock = $ts ? (int)$ts->quantity : 0;
                if ($totalItemQty > $currentTeamStock) {
                    throw new \Exception("Cannot return/mark lost {$totalItemQty} pcs for " . $target['name'] . ". Team stock only has {$currentTeamStock} pcs available.");
                }

                // 1. Deduct (returned + lost) from TeamStock
                if ($ts) {
                    $ts->quantity = max(0, $ts->quantity - $totalItemQty);
                    $ts->save();
                }

                // 2. Add ONLY returned_qty back to Main Warehouse stock
                if ($returnedQty > 0) {
                    $mainWarehouse = \App\Models\Site::where('name', 'Main Warehouse')->first();
                    $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

                    if (!empty($target['book_index_id'])) {
                        $index = \App\Models\BookIndex::find($target['book_index_id']);
                        if ($index) {
                            $index->stock = ($index->stock ?? 0) + $returnedQty;
                            $index->save();

                            \App\Models\SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_index_id' => $index->id],
                                ['quantity' => $index->stock]
                            );
                        }
                    } elseif (!empty($target['book_id'])) {
                        $book = \App\Models\Book::find($target['book_id']);
                        if ($book) {
                            $book->stock = ($book->stock ?? 0) + $returnedQty;
                            $book->save(); // BookObserver automatically syncs Main Warehouse SiteInventory to $book->stock
                        }
                    } elseif (!empty($target['bundle_id'])) {
                        $bundle = \App\Models\BookBundle::find($target['bundle_id']);
                        if ($bundle) {
                            $bundle->stock = ($bundle->stock ?? 0) + $returnedQty;
                            $bundle->save();

                            \App\Models\SiteInventory::updateOrCreate(
                                ['site_id' => $mainSiteId, 'book_bundle_id' => $bundle->id],
                                ['quantity' => $bundle->stock]
                            );
                        }
                    }
                }

                // 3. Create TeamStockTransferItem record
                \App\Models\TeamStockTransferItem::create([
                    'team_stock_transfer_id' => $transfer->id,
                    'book_id' => $target['book_id'],
                    'book_index_id' => $target['book_index_id'],
                    'book_bundle_id' => $target['bundle_id'],
                    'quantity' => $returnedQty,
                    'lost_quantity' => $lostQty,
                    'status' => 'completed',
                ]);

                // 4. Create LostInventory record if lostQty > 0
                if ($lostQty > 0) {
                    \App\Models\LostInventory::create([
                        'product_type'   => $target['type'],
                        'book_id'        => $target['book_id'],
                        'book_index_id'  => $target['book_index_id'],
                        'book_bundle_id' => $target['bundle_id'],
                        'quantity'       => $lostQty,
                        'site_id'        => null,
                        'team_name'      => $teamName,
                        'reason'         => 'Team Stock Return lost stock' . ($request->notes ? ' (' . $request->notes . ')' : ''),
                        'user_id'        => auth()->id(),
                        'lost_date'      => now(),
                    ]);

                    if (!empty($target['book_id'])) {
                        \App\Models\InventoryTransaction::create([
                            'book_id'          => $target['book_id'],
                            'type'             => 'LOST',
                            'quantity'         => -$lostQty,
                            'location'         => $teamName,
                            'source'           => 'Team Stock Return',
                            'reference_number' => $transferNumber,
                            'notes'            => 'Lost stock recorded during team stock return' . ($request->notes ? ' - ' . $request->notes : ''),
                            'status'           => 'completed',
                            'transaction_date' => now(),
                            'user_id'          => auth()->id(),
                        ]);
                    }
                }

                $totalReturned += $returnedQty;
                $totalLost += $lostQty;
            }

            if ($totalReturned == 0 && $totalLost == 0) {
                throw new \Exception("No items with positive returned or lost quantities were submitted.");
            }

            \DB::commit();

            $msg = "Stock return #{$transferNumber} successfully processed! Returned {$totalReturned} pcs to Main Warehouse";
            if ($totalLost > 0) {
                $msg .= " and recorded {$totalLost} pcs as Lost.";
            }

            return redirect()->route('marketing.area-sales.team-stocks.index')
                ->with('success', $msg);

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to execute stock return: ' . $e->getMessage());
        }
    }

    /**
     * Export Book List (Master Registry) to Excel
     */
    public function exportBooks(Request $request)
    {
        $search = $request->input('search');
        $query = \App\Models\Book::with(['consignmentOwner', 'bookCategory', 'bookSubCategory'])->where('is_active', true);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('sub_category', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('book_type', 'like', "%{$search}%");
            });
        }

        $books = $query->orderBy('name')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Book List');

        // Title
        $sheet->setCellValue('A1', 'CLARETIAN ERP — BOOK LIST (MASTER REGISTRY)');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFD9251C']],
        ]);

        $sheet->setCellValue('A2', 'Exported on ' . date('M d, Y h:i A') . ' | Total Records: ' . $books->count());
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF666666']],
        ]);

        // Headers
        $headers = [
            'A4' => 'Item Code',
            'B4' => 'ISBN / Barcode',
            'C4' => 'Book Title / Name',
            'D4' => 'Category',
            'E4' => 'Sub Category',
            'F4' => 'Classification',
            'G4' => 'Publisher',
            'H4' => 'Price (₱)',
            'I4' => 'Main Warehouse Stock (pcs)',
            'J4' => 'Status',
            'K4' => 'Description',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getStyle('A4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9251C']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ],
        ]);

        $row = 5;
        foreach ($books as $b) {
            $stock = (int) ($b->main_stock ?? $b->stock ?? 0);
            $status = $b->is_active ? 'Active' : 'Inactive';
            $subCategory = $b->sub_category ?: ($b->bookSubCategory?->name ?: '—');
            $classification = $b->book_type ?: ($b->consignmentOwner ? 'Consignment' : '—');
            $publisher = $b->publisher ?: ($b->consignmentOwner?->name ?: '—');

            $sheet->setCellValue("A{$row}", $b->item_code ?: '—');
            $sheet->setCellValue("B{$row}", $b->isbn ?: $b->barcode ?: '—');
            $sheet->setCellValue("C{$row}", $b->name);
            $sheet->setCellValue("D{$row}", $b->category ?: 'Books');
            $sheet->setCellValue("E{$row}", $subCategory);
            $sheet->setCellValue("F{$row}", $classification);
            $sheet->setCellValue("G{$row}", $publisher);
            $sheet->setCellValue("H{$row}", (float)$b->price);
            $sheet->setCellValue("I{$row}", $stock);
            $sheet->setCellValue("J{$row}", $status);
            $sheet->setCellValue("K{$row}", $b->description ?: ($b->sales_description ?: '—'));

            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE0E0E0']
                    ]
                ]
            ]);
            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(35);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="Book_List_Master.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Export Book Indices Mapping to Excel
     */
    public function exportBookIndices(Request $request)
    {
        $search = $request->input('search');
        $query = \App\Models\BookIndex::with('book');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('index_value', 'like', '%' . $search . '%')
                  ->orWhere('article', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%')
                  ->orWhere('nbs_barcode', 'like', '%' . $search . '%')
                  ->orWhereHas('book', function($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%')
                         ->orWhere('article', 'like', '%' . $search . '%')
                         ->orWhere('barcode', 'like', '%' . $search . '%')
                         ->orWhere('nbs_barcode', 'like', '%' . $search . '%');
                  });
            });
        }

        $indices = $query->get()->sortBy(function($item) {
            return strtolower($item->display_name);
        })->values();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Book Indices');

        // Title
        $sheet->setCellValue('A1', 'CLARETIAN ERP — BOOK INDICES MAPPING');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFD9251C']],
        ]);

        $sheet->setCellValue('A2', 'Exported on ' . date('M d, Y h:i A') . ' | Total Records: ' . $indices->count());
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF666666']],
        ]);

        // Headers
        $headers = [
            'A4' => 'Article / ISBN Number',
            'B4' => 'Index Display Name',
            'C4' => 'Parent Book Title',
            'D4' => 'Price (₱)',
            'E4' => 'Main Warehouse Stock (pcs)',
            'F4' => 'Status',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9251C']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ],
        ]);

        $row = 5;
        foreach ($indices as $idx) {
            $stock = (int) ($idx->main_stock ?? $idx->stock ?? 0);
            $price = (float) (($idx->price && $idx->price > 0) ? $idx->price : ($idx->book?->price ?? 0));
            $article = $idx->article ?: ($idx->barcode ?: ($idx->nbs_barcode ?: '—'));
            $status = ($idx->is_active ?? true) ? 'Active' : 'Inactive';

            $sheet->setCellValue("A{$row}", $article);
            $sheet->setCellValue("B{$row}", $idx->display_name);
            $sheet->setCellValue("C{$row}", $idx->book?->name ?: '—');
            $sheet->setCellValue("D{$row}", $price);
            $sheet->setCellValue("E{$row}", $stock);
            $sheet->setCellValue("F{$row}", $status);

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE0E0E0']
                    ]
                ]
            ]);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(15);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="Book_Indices_Mapping.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Export Book Bundles List to Excel
     */
    public function exportBookBundles(Request $request)
    {
        $search = $request->input('bundle_search') ?: $request->input('search');
        $query = \App\Models\BookBundle::with('books');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bundles = $query->orderBy('name')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Book Bundles');

        // Title
        $sheet->setCellValue('A1', 'CLARETIAN ERP — BOOK BUNDLES LIST');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFD9251C']],
        ]);

        $sheet->setCellValue('A2', 'Exported on ' . date('M d, Y h:i A') . ' | Total Records: ' . $bundles->count());
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF666666']],
        ]);

        // Headers
        $headers = [
            'A4' => 'SKU / Barcode',
            'B4' => 'Bundle Name',
            'C4' => 'Included Books',
            'D4' => 'Price (₱)',
            'E4' => 'Stock (pcs)',
            'F4' => 'Status',
            'G4' => 'Description',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9251C']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC']
                ]
            ],
        ]);

        $row = 5;
        foreach ($bundles as $bun) {
            $stock = (int) ($bun->main_stock ?? $bun->stock ?? 0);
            $status = $bun->is_active ? 'Active' : 'Inactive';

            $included = $bun->books->map(function($b) {
                return $b->name . ' (x' . ($b->pivot->quantity ?? 1) . ')';
            })->implode(', ');

            $sheet->setCellValue("A{$row}", $bun->sku ?: '—');
            $sheet->setCellValue("B{$row}", $bun->name);
            $sheet->setCellValue("C{$row}", $included ?: 'None');
            $sheet->setCellValue("D{$row}", (float)$bun->price);
            $sheet->setCellValue("E{$row}", $stock);
            $sheet->setCellValue("F{$row}", $status);
            $sheet->setCellValue("G{$row}", $bun->description ?: '—');

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE0E0E0']
                    ]
                ]
            ]);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(55);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(35);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="Book_Bundles_List.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Approve Team Stock Transfer by Marketing (moves to Production approval queue)
     */
    public function approveTeamStockTransferByMarketing(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::findOrFail($id);
        if ($transfer->status !== 'pending_mkt_approval') {
            return redirect()->back()->with('error', 'This transfer is not pending Marketing approval.');
        }

        $remarks = $request->input('approval_remarks') ?: ($request->input('remarks') ?: null);
        $updateData = [
            'status' => 'pending_af_approval',
        ];
        if ($remarks) {
            $existingRemarks = $transfer->remarks;
            $updateData['remarks'] = $existingRemarks ? ($existingRemarks . "\n[Marketing]: " . $remarks) : ('[Marketing]: ' . $remarks);
        }

        $transfer->update($updateData);

        return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' approved by Marketing! Moved to Admin & Finance Approval Queue.');
    }

    /**
     * Reject Team Stock Transfer by Marketing
     */
    public function rejectTeamStockTransferByMarketing(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::findOrFail($id);
        if ($transfer->status !== 'pending_mkt_approval') {
            return redirect()->back()->with('error', 'This transfer is not pending Marketing approval.');
        }

        $reason = $request->input('rejection_reason');
        $existingRemarks = $transfer->remarks;
        $transfer->update([
            'status' => 'rejected',
            'remarks' => $existingRemarks ? ($existingRemarks . "\n[Marketing Rejection]: " . $reason) : ('[Marketing Rejection]: ' . $reason),
        ]);

        return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' rejected.');
    }
}
