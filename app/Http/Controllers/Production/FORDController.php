<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EfordPayout;
use App\Models\EfordPayoutItem;

class FORDController extends Controller
{
    public function autoDebitIndex()
    {
        $debits = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->latest()
            ->paginate(15);

        return view('production.ford.auto-debit.index', [
            'title' => 'Auto Debit Letters',
            'role' => auth()->user()->position,
            'debits' => $debits
        ]);
    }

    public function autoDebitCreate()
    {
        return view('production.ford.auto-debit.create');
    }

    public function clientPaymentPosting()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        return view('production.ford.client-payment-posting', compact('customers'));
    }

    public function eFordPayout()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $reports = EfordPayout::with(['customer', 'creator'])
            ->where('prepared_by', auth()->id())
            ->latest()
            ->get();
        return view('production.ford.eford-payout', compact('customers', 'reports'));
    }

    public function storeEfordPayout(Request $request)
    {
        $request->validate([
            'period' => 'required|string|max:255',
            'customer_id' => 'nullable|integer',
            'order_no' => 'required|array',
            'order_no.*' => 'nullable|string',
            'date' => 'required|array',
            'date.*' => 'nullable|date',
            'si_no' => 'required|array',
            'si_no.*' => 'nullable|string',
            'customer' => 'required|array',
            'customer.*' => 'nullable|string',
            'amount' => 'required|array',
            'amount.*' => 'nullable|numeric|min:0',
            'freight' => 'required|array',
            'freight.*' => 'nullable|numeric|min:0',
            'payment_method' => 'required|array',
            'payment_method.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('eford_payouts', $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }

        // Calculate totals
        $totalAmount = 0;
        $totalFreight = 0;
        $totalGrossSales = 0;

        foreach ($request->amount as $index => $amt) {
            $a = (float)$amt;
            $f = (float)($request->freight[$index] ?? 0);
            $totalAmount += $a;
            $totalFreight += $f;
            $totalGrossSales += ($a + $f);
        }

        $payout = EfordPayout::create([
            'prepared_by' => auth()->id(),
            'period' => $request->period,
            'customer_id' => $request->customer_id,
            'total_amount' => $totalAmount,
            'total_freight' => $totalFreight,
            'total_gross_sales' => $totalGrossSales,
            'attachments' => $attachmentPaths,
        ]);

        foreach ($request->order_no as $index => $orderNo) {
            $customerName = $request->customer[$index] ?? null;
            $amt = $request->amount[$index] ?? 0;
            if ($customerName || $amt > 0) {
                EfordPayoutItem::create([
                    'eford_payout_id' => $payout->id,
                    'order_no' => $orderNo,
                    'date' => $request->date[$index] ?? null,
                    'si_no' => $request->si_no[$index] ?? null,
                    'customer_name' => $customerName,
                    'amount' => $amt,
                    'freight' => $request->freight[$index] ?? 0,
                    'gross_sales' => $amt + ($request->freight[$index] ?? 0),
                    'payment_method' => $request->payment_method[$index] ?? null,
                ]);
            }
        }

        return redirect()->route('production.ford.eford-payout')->with('success', 'E-FORD Sales Summary Report has been generated and submitted to Accounting.');
    }

    public function accountingIndex()
    {
        $reports = EfordPayout::with(['customer', 'creator'])->latest()->get();
        return view('admin-finance.accounting.eford-payouts.index', [
            'title' => 'E-FORD Payout Reports',
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance',
            'reports' => $reports
        ]);
    }

    public function accountingShow($id)
    {
        $report = EfordPayout::with(['customer', 'creator', 'items'])->findOrFail($id);
        return view('admin-finance.accounting.eford-payouts.show', [
            'title' => 'E-FORD Report #' . str_pad($report->id, 5, '0', STR_PAD_LEFT),
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance',
            'report' => $report
        ]);
    }

    public function downloadAttachment($id, $index)
    {
        $payout = EfordPayout::findOrFail($id);
        $attachments = $payout->attachments;
        if (!is_array($attachments) || !isset($attachments[$index])) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }

        $relativePath = $attachments[$index];
        $path = storage_path('app/public/' . $relativePath);
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $inlineExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'];

        if (in_array($extension, $inlineExtensions)) {
            $contentType = 'application/octet-stream';
            if ($extension === 'pdf') {
                $contentType = 'application/pdf';
            } elseif (in_array($extension, ['jpg', 'jpeg'])) {
                $contentType = 'image/jpeg';
            } elseif ($extension === 'png') {
                $contentType = 'image/png';
            } elseif ($extension === 'gif') {
                $contentType = 'image/gif';
            } elseif ($extension === 'txt') {
                $contentType = 'text/plain';
            }

            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        }

        return response()->download($path, basename($path));
    }

    public function getUnpaidInvoices($customerId)
    {
        $orders = \App\Models\SalesOrder::with(['customer'])
            ->where('customer_id', $customerId)
            ->where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->get();

        $unpaidSIs = [];
        foreach ($orders as $order) {
            $si = \App\Models\SalesInvoice::where('so_id', $order->id)->first();
            $siNumber = $si ? $si->si_number : 'SI-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
            $unpaidSIs[] = [
                'order_no' => $order->so_number,
                'date' => $order->created_at->format('Y-m-d'),
                'si_no' => $siNumber,
                'customer' => $order->customer->customer_name ?? '',
                'amount' => (float)$order->total_amount,
                'freight' => (float)($order->freight_charges ?? 0),
                'gross_sales' => (float)$order->total_amount + (float)($order->freight_charges ?? 0),
                'payment_method' => $order->payment_method ?? ''
            ];
        }

        return response()->json($unpaidSIs);
    }

    public function paymentRequest()
    {
        $requests = \App\Models\PaymentRequest::with(['requester', 'directorApprovedBy', 'adminApprovedBy', 'financeApprovedBy'])
            ->where('requester_id', auth()->id())
            ->latest()
            ->get();

        return view('production.ford.payment-request', compact('requests'));
    }

    public function purchaseOrder()
    {
        $suppliers = \App\Models\Supplier::where('status', 'active')->get();
        
        // Self-healing: Ensure every active Book has a corresponding Product record
        $books = \App\Models\Book::where('is_active', true)->get();
        foreach ($books as $book) {
            $product = \App\Models\Product::where('book_id', $book->id)->first();
            if (!$product) {
                \App\Models\Product::create([
                    'book_id' => $book->id,
                    'name' => $book->name,
                    'price' => $book->price ?? 0.00,
                    'is_active' => true,
                ]);
            }
        }

        $products = \App\Models\Product::with('book')->where('is_active', true)->orderBy('name')->get();

        return view('production.ford.purchase-order', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function requestForQuotation()
    {
        $books = \App\Models\Book::orderBy('name', 'asc')->get();
        return view('production.ford.request-for-quotation', [
            'books' => $books
        ]);
    }

    public function salesOrder(Request $request)
    {
        $query = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where(function($q) {
                $q->where('type', 'foreign')
                  ->orWhere('so_number', 'like', 'FORD-SO-%');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('production.ford.sales-order', [
            'title' => 'Foreign Sales Orders',
            'role' => auth()->user()->position ?? 'FORD Staff',
            'sidebar' => 'production',
            'orders' => $orders,
        ]);
    }

    public function salesOrderCreate()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $books = [];
        $products = [];
        $areaSalesStaff = \App\Models\User::where(function($q) {
            $q->where('position', 'like', '%Sales%')
              ->orWhere('position', 'like', '%Area%');
        })->get();

        return view('production.ford.sales-order-create', [
            'title' => 'Create Foreign Sales Order',
            'role' => 'FORD Staff',
            'sidebar' => 'production',
            'customers' => $customers,
            'books' => $books,
            'products' => $products,
            'areaSalesStaff' => $areaSalesStaff,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $term = trim($request->input('q', ''));
        $userTeam = auth()->user()->sales_team ?? null;
        $limit = 30;

        $teamStocksMap = [];
        $allowedBookIds = null;
        $allowedIndexIds = null;
        $allowedBundleIds = null;

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

            $tsList = \App\Models\TeamStock::where(function($q) use ($variations) {
                foreach ($variations as $var) {
                    $q->orWhere('team_name', $var)
                      ->orWhereRaw('LOWER(team_name) = ?', [strtolower($var)]);
                }
            })->where('quantity', '>', 0)->get();

            foreach ($tsList as $ts) {
                if ($ts->book_index_id) {
                    $teamStocksMap['index_' . $ts->book_index_id] = (int)$ts->quantity;
                } elseif ($ts->book_bundle_id) {
                    $teamStocksMap['bundle_' . $ts->book_bundle_id] = (int)$ts->quantity;
                } elseif ($ts->book_id) {
                    $teamStocksMap['book_' . $ts->book_id] = (int)$ts->quantity;
                }
            }

            $allowedBookIds = array_map(fn($k) => (int)str_replace('book_', '', $k), array_keys(array_filter($teamStocksMap, fn($v, $k) => str_starts_with($k, 'book_'), ARRAY_FILTER_USE_BOTH)));
            $allowedIndexIds = array_map(fn($k) => (int)str_replace('index_', '', $k), array_keys(array_filter($teamStocksMap, fn($v, $k) => str_starts_with($k, 'index_'), ARRAY_FILTER_USE_BOTH)));
            $allowedBundleIds = array_map(fn($k) => (int)str_replace('bundle_', '', $k), array_keys(array_filter($teamStocksMap, fn($v, $k) => str_starts_with($k, 'bundle_'), ARRAY_FILTER_USE_BOTH)));
        }

        $booksQuery = \App\Models\Book::where('is_active', true);
        if (!empty($userTeam)) {
            $booksQuery->whereIn('id', $allowedBookIds ?? [0]);
        }
        if ($term !== '') {
            $booksQuery->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%");
            });
        }
        $books = $booksQuery->select(['id', 'name', 'price', 'sku', 'barcode', 'item_code', 'is_book', 'category', 'book_type', 'image', 'stock'])
            ->limit($limit)
            ->get()
            ->map(function($b) use ($userTeam, $teamStocksMap) {
                $isNonBook = (isset($b->is_book) && $b->is_book === false) || 
                             (isset($b->category) && strtolower($b->category) === 'non-book') ||
                             (isset($b->book_type) && strtolower($b->book_type) === 'non-book');
                $typeSuffix = $isNonBook ? ' (non-book)' : ' (book)';
                $prefix = $isNonBook ? '[Non-Book] ' : '[Book] ';
                $code = $b->barcode ?: ($b->sku ?: ($b->item_code ?: ''));
                $price = (float)$b->price;
                $stock = !empty($userTeam) ? (int)($teamStocksMap['book_' . $b->id] ?? 0) : (int)($b->stock ?? 0);
                return [
                    'id' => 'book_' . $b->id,
                    'text' => $prefix . $b->name . $typeSuffix . ' - ₱' . number_format($price, 2) . ($code ? " ({$code})" : '') . " (Stock: {$stock})",
                    'name' => $b->name . $typeSuffix,
                    'price' => $price,
                    'isbn' => $code,
                    'stock' => $stock
                ];
            });

        $remainingLimit = max(0, $limit - $books->count());
        $indices = collect();
        if ($remainingLimit > 0) {
            $idxQuery = \App\Models\BookIndex::with('book');
            if (!empty($userTeam)) {
                $idxQuery->whereIn('id', $allowedIndexIds ?? [0]);
            }
            if ($term !== '') {
                $idxQuery->where(function($q) use ($term) {
                    $q->whereHas('book', function($bq) use ($term) {
                        $bq->where('name', 'like', "%{$term}%");
                    })
                    ->orWhere('index_value', 'like', "%{$term}%")
                    ->orWhere('custom_name', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%");
                });
            }
            $indices = $idxQuery->limit($remainingLimit)->get()->map(function($idx) use ($userTeam, $teamStocksMap) {
                $code = $idx->barcode ?: ($idx->book?->barcode ?: ($idx->book?->sku ?: ''));
                $price = (float) (($idx->price && $idx->price > 0) ? $idx->price : ($idx->book?->price ?? 0));
                $dispName = $idx->display_name ?? ($idx->custom_name ?: ($idx->book?->name . ' - ' . $idx->index_value));
                $stock = !empty($userTeam) ? (int)($teamStocksMap['index_' . $idx->id] ?? 0) : (int)($idx->stock ?? 0);
                return [
                    'id' => 'index_' . $idx->id,
                    'text' => '[Index] ' . $dispName . ' - ₱' . number_format($price, 2) . ($code ? " ({$code})" : '') . " (Stock: {$stock})",
                    'name' => $dispName,
                    'price' => $price,
                    'isbn' => $code,
                    'stock' => $stock
                ];
            });
        }

        $remainingLimit2 = max(0, $limit - $books->count() - $indices->count());
        $bundles = collect();
        if ($remainingLimit2 > 0) {
            $bunQuery = \App\Models\BookBundle::where('is_active', true);
            if (!empty($userTeam)) {
                $bunQuery->whereIn('id', $allowedBundleIds ?? [0]);
            }
            if ($term !== '') {
                $bunQuery->where(function($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                      ->orWhere('sku', 'like', "%{$term}%");
                });
            }
            $bundles = $bunQuery->limit($remainingLimit2)->get()->map(function($bun) use ($userTeam, $teamStocksMap) {
                $price = (float)$bun->price;
                $stock = !empty($userTeam) ? (int)($teamStocksMap['bundle_' . $bun->id] ?? 0) : (int)($bun->stock ?? 0);
                return [
                    'id' => 'bundle_' . $bun->id,
                    'text' => '[Bundle] ' . $bun->name . ' (bundle) - ₱' . number_format($price, 2) . " (Stock: {$stock})",
                    'name' => $bun->name . ' (bundle)',
                    'price' => $price,
                    'isbn' => $bun->sku ?? '',
                    'stock' => $stock
                ];
            });
        }

        $results = $books->concat($indices)->concat($bundles)->values();
        return response()->json(['results' => $results]);
    }

    public function reviewSalesOrder($id)
    {
        $order = \App\Models\SalesOrder::with([
            'customer', 
            'items.book', 
            'items.bookIndex.book', 
            'items.bundle', 
            'items.product',
            'items.pickListItems',
            'preparedBy'
        ])->findOrFail($id);

        return view('production.sales-orders.review', [
            'title' => 'Review Sales Order #' . $order->so_number,
            'role' => auth()->user()->position ?? 'Production Manager',
            'sidebar' => 'production',
            'order' => $order,
        ]);
    }

    public function storeSalesOrder(Request $request)
    {
        $request->validate([
            'so_number' => 'required|string|unique:sales_orders,so_number',
            'customer_id' => 'nullable|integer',
            'type' => 'nullable|string',
            'currency' => 'nullable|string|in:PHP,USD,EUR',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'proof_of_payment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'items' => 'required|array|min:1|max:24',
        ]);

        if (count(array_filter($request->items ?? [], fn($i) => !empty($i['product_id']))) > 24) {
            return redirect()->back()->with('error', 'Cannot proceed with Foreign Sales Order: Maximum of 24 products allowed per order.')->withInput();
        }

        $customerObj = null;
        if ($request->filled('customer_id')) {
            $customerObj = \App\Models\Customer::find($request->customer_id);
        }
        if (!$customerObj && $request->filled('customer')) {
            $customerObj = \App\Models\Customer::where('customer_name', $request->customer)->first();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('sales_orders', 'public');
        }

        $proofOfPaymentPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofOfPaymentPath = $request->file('proof_of_payment')->store('sales_orders', 'public');
        }

        $address = $request->billing_address ?? $request->address ?? ($customerObj ? ($customerObj->shipping_address ?? $customerObj->billing_address) : '');
        $soType = $request->type ?? 'foreign';
        $currency = $request->currency ?? 'USD';

        $soData = [
            'so_number' => $request->so_number,
            'customer_id' => $customerObj ? $customerObj->customer_id : null,
            'customer_representative' => $request->customer_representative ?? null,
            'customer_contact' => $request->customer_contact ?? null,
            'area_sales_staff_id' => $soType === 'area_sales_consignment' ? $request->area_sales_staff_id : null,
            'billing_address' => $address,
            'shipping_address' => $address,
            'type' => $soType,
            'currency' => $currency,
            'status' => 'pending_prod_approval',
            'terms' => $request->terms,
            'ref_number' => $request->ref_number,
            'remarks' => $request->remarks,
            'prepared_by' => auth()->id(),
            'attachment' => $attachmentPath,
            'proof_of_payment' => $proofOfPaymentPath,
            'freight_option' => $request->freight_option ?? null,
            'forwarder' => $request->forwarder ?? null,
            'total_amount' => 0,
            'created_at' => $request->date ? \Carbon\Carbon::parse($request->date) : now(),
        ];

        $so = \App\Models\SalesOrder::create($soData);
        $so->items()->delete();

        $totalAmount = 0;
        $mktCtrl = new \App\Http\Controllers\MarketingController();

        // Standard structured items array (Marketing style)
        if (!empty($request->items) && is_array($request->items)) {
            foreach ($request->items as $item) {
                if (empty($item['product_id'])) continue;

                $target = $mktCtrl->resolveItemTarget($item['product_id']);
                if (!$target['exists']) continue;

                $qty = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? 0);
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';

                $gross = $qty * $price;
                $discAmount = $discType === 'percentage' ? ($gross * ($discVal / 100)) : $discVal;
                $subtotal = max(0, $gross - $discAmount);
                $totalAmount += $subtotal;

                $so->items()->create([
                    'book_id' => $target['book_id'],
                    'bundle_id' => $target['bundle_id'],
                    'book_index_id' => $target['book_index_id'],
                    'quantity' => $qty,
                    'price' => $price,
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
        // Simple arrays fallback (quantity[], unit_price[], description[], etc.)
        elseif (!empty($request->quantity) && is_array($request->quantity)) {
            foreach ($request->quantity as $i => $qty) {
                $qtyVal = (float) $qty;
                $priceVal = (float) ($request->unit_price[$i] ?? 0);
                $amtVal = $qtyVal * $priceVal;
                $totalAmount += $amtVal;

                $so->items()->create([
                    'book_id' => $request->book_id[$i] ?? null,
                    'item_type' => 'book',
                    'quantity' => $qtyVal,
                    'price' => $priceVal,
                    'total_price' => $amtVal,
                    'subtotal' => $amtVal,
                    'area' => $request->area[$i] ?? null,
                ]);
            }
        }

        // Apply Overall Header Discount
        $discountAmount = 0;
        $discountPercentage = 0;
        if ($request->filled('discount_value') && $request->discount_value > 0) {
            $discVal = (float) $request->discount_value;
            if ($request->discount_type === 'percentage') {
                $discountPercentage = $discVal;
                $discountAmount = $totalAmount * ($discountPercentage / 100);
            } else {
                $discountAmount = $discVal;
                $discountPercentage = 0;
            }
        }

        $serviceFee = 0;
        if ($request->filled('freight_option')) {
            $serviceFee = ($currency === 'USD' || $currency === 'EUR') ? 1.00 : 50.00;
        }

        $finalTotal = $totalAmount - $discountAmount + $serviceFee;

        $so->update([
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'total_amount' => max(0, $finalTotal),
        ]);

        // Deduct stock immediately upon Sales Order creation
        \App\Services\StockDeductionService::deductForSalesOrder($so);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'FORD Sales Order Created',
            'description' => 'Foreign Sales Order #' . $so->so_number . ' saved and submitted to FORD Supervisor for approval.',
            'reference_type' => 'SalesOrder',
            'reference_id' => $so->id,
            'details' => json_encode(['so_number' => $so->so_number, 'total' => $finalTotal, 'currency' => $so->currency]),
        ]);

        return redirect()->route('production.ford.sales-order')
            ->with('success', 'Foreign Sales Order #' . $so->so_number . ' has been created and submitted for FORD Supervisor approval.');
    }

    public function approveSalesOrder($id)
    {
        $userPos = auth()->user()->position ?? '';
        $userRole = auth()->user()->role ?? '';
        $isAllowed = str_contains(strtolower($userPos), 'manager') 
            || str_contains(strtolower($userPos), 'supervisor') 
            || str_contains(strtolower($userPos), 'admin') 
            || str_contains(strtolower($userRole), 'admin') 
            || str_contains(strtolower($userRole), 'supervisor')
            || (isset(auth()->user()->is_admin) && auth()->user()->is_admin);

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Only Production Managers, Supervisors, and Admins can approve Foreign Sales Orders.');
        }

        $order = \App\Models\SalesOrder::with('items')->findOrFail($id);
        
        $order->update([
            'status' => 'picking',
            'approved_by_prod' => auth()->id(),
            'prod_approved_at' => now()
        ]);

        // Automatically generate PickList and PickListItems for Logistics
        $existingPickList = \App\Models\PickList::where('sales_order_id', $order->id)->first();
        if (!$existingPickList && $order->items && $order->items->count() > 0) {
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

        return redirect()->back()->with('success', 'Foreign Sales Order #' . $order->so_number . ' has been approved by Production and sent to Logistics for picking.');
    }

    public function rejectSalesOrder(Request $request, $id)
    {
        $userPos = auth()->user()->position ?? '';
        $userRole = auth()->user()->role ?? '';
        $isAllowed = str_contains(strtolower($userPos), 'manager') 
            || str_contains(strtolower($userPos), 'supervisor') 
            || str_contains(strtolower($userPos), 'admin') 
            || str_contains(strtolower($userRole), 'admin') 
            || str_contains(strtolower($userRole), 'supervisor')
            || (isset(auth()->user()->is_admin) && auth()->user()->is_admin);

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Only Production Managers, Supervisors, and Admins can reject Foreign Sales Orders.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        $userTitle = auth()->user()->name . ' (Production Rejection)';
        $remarksText = $request->remarks ? $request->remarks : 'Rejected by Production Supervisor';
        $newRemarks = trim(($order->remarks ? $order->remarks . "\n" : '') . '[' . $userTitle . ']: ' . $remarksText);
        $order->update([
            'status' => 'cancelled',
            'remarks' => $newRemarks
        ]);

        \App\Services\StockDeductionService::restoreForSalesOrder($order, 'Foreign Production Rejection');

        return redirect()->route('production.approval-queue')->with('warning', 'Foreign Sales Order #' . $order->so_number . ' has been rejected.');
    }

    public function transmittal()
    {
        return view('production.ford.transmittal');
    }

    public function storeClientPaymentPosting(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|array|min:1',
            'customer_id.*' => 'required|exists:customers,customer_id',
            'bank_date' => 'nullable|array',
            'bank_date.*' => 'nullable|string',
            'document_no' => 'nullable|array',
            'document_no.*' => 'nullable|string',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0',
            'proof_file' => 'nullable|array',
        ]);

        try {
            $postingId = \DB::transaction(function () use ($request) {
                $posting = \App\Models\ClientPaymentPosting::create([
                    'date' => $request->date,
                    'status' => 'pending',
                    'prepared_by' => auth()->id(),
                ]);

                foreach ($request->customer_id as $index => $customerId) {
                    $attachmentPath = null;
                    if ($request->hasFile("proof_file.{$index}")) {
                        $file = $request->file("proof_file.{$index}");
                        $mime = $file->getMimeType();
                        $filename = time() . '_' . $index . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                        $uploadDir = public_path('proof_attachments');
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $filePath = $uploadDir . '/' . $filename;
                        if (str_starts_with($mime, 'image/') && function_exists('imagecreatefromstring')) {
                            try {
                                $img = @imagecreatefromstring(file_get_contents($file->getRealPath()));
                                if ($img !== false) {
                                    imagejpeg($img, $filePath, 75);
                                    imagedestroy($img);
                                } else {
                                    $file->move($uploadDir, $filename);
                                }
                            } catch (\Exception $ex) {
                                $file->move($uploadDir, $filename);
                            }
                        } else {
                            $file->move($uploadDir, $filename);
                        }

                        $attachmentPath = 'proof_attachments/' . $filename;
                    }

                    $docNo = $request->document_no[$index] ?? null;
                    $posting->items()->create([
                        'customer_id' => $customerId,
                        'bank_date' => $request->bank_date[$index] ?? null,
                        'document_no' => $docNo,
                        'reference_no' => $docNo,
                        'payment_date' => $request->date,
                        'amount' => $request->amount[$index],
                        'proof_attachment' => $attachmentPath,
                    ]);
                }
                return $posting->id;
            });

            return response()->json([
                'success' => true,
                'message' => 'Client Payment Posting request generated successfully and sent to Accounting.',
                'id' => $postingId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paymentPostingIndex(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\ClientPaymentPosting::with(['preparer', 'items.customer', 'items.account'])
            ->withSum('items', 'amount');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('items.customer', function($sub) use ($search) {
                      $sub->where('customer_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($sub) use ($search) {
                      $sub->where('invoice_no', 'like', "%{$search}%")
                          ->orWhere('receipt_no', 'like', "%{$search}%")
                          ->orWhere('reference_no', 'like', "%{$search}%")
                          ->orWhere('document_no', 'like', "%{$search}%");
                  });
            });
        }

        $postings = $query->latest()->paginate(15)->withQueryString();

        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $depositAccounts = \App\Models\ChartOfAccount::where('is_active', 1)
            ->where('is_postable', 1)
            ->where('type', 'Asset')
            ->orderBy('code')
            ->get();
        $invoices = \App\Models\SalesInvoice::orderBy('id', 'desc')->take(100)->get();

        return view('admin-finance.accounting.payment-posting.index', [
            'title' => 'Client Payment Posting Requests',
            'role' => 'Finance Staff',
            'postings' => $postings,
            'customers' => $customers,
            'depositAccounts' => $depositAccounts,
            'invoices' => $invoices,
        ]);
    }

    public function storeDirectPaymentPosting(Request $request)
    {
        $request->validate([
            'payment_date'       => 'required|date',
            'customer_id'        => 'required|exists:customers,customer_id',
            'amount'             => 'required|numeric|min:0.01',
            'payment_method'     => 'required|string',
            'chart_of_account_id'=> 'nullable|exists:chart_of_accounts,id',
            'reference_no'       => 'nullable|string|max:255',
            'invoice_no'         => 'nullable|string|max:255',
            'receipt_no'         => 'nullable|string|max:255',
            'check_number'       => 'nullable|string|max:255',
            'check_date'         => 'nullable|date',
            'bank_name'          => 'nullable|string|max:255',
            'proof_file'         => 'nullable|file|max:25600',
        ]);

        try {
            $attachmentPath = null;
            if ($request->hasFile('proof_file')) {
                $file = $request->file('proof_file');
                $mime = $file->getMimeType();
                $filename = time() . '_direct_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $uploadDir = public_path('proof_attachments');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $filePath = $uploadDir . '/' . $filename;
                if (str_starts_with($mime, 'image/') && function_exists('imagecreatefromstring')) {
                    try {
                        $img = @imagecreatefromstring(file_get_contents($file->getRealPath()));
                        if ($img !== false) {
                            imagejpeg($img, $filePath, 75);
                            imagedestroy($img);
                        } else {
                            $file->move($uploadDir, $filename);
                        }
                    } catch (\Exception $ex) {
                        $file->move($uploadDir, $filename);
                    }
                } else {
                    $file->move($uploadDir, $filename);
                }

                $attachmentPath = 'proof_attachments/' . $filename;
            }

            $coaId = $request->chart_of_account_id ?: \App\Models\ChartOfAccount::where('is_active', 1)->where('type', 'Asset')->value('id');

            $posting = \App\Models\ClientPaymentPosting::create([
                'date' => $request->payment_date,
                'status' => 'pending',
                'prepared_by' => auth()->id(),
            ]);

            $posting->items()->create([
                'customer_id' => $request->customer_id,
                'invoice_no' => $request->invoice_no,
                'receipt_no' => $request->receipt_no,
                'reference_no' => $request->reference_no,
                'payment_method' => $request->payment_method,
                'chart_of_account_id' => $coaId,
                'check_number' => $request->check_number,
                'check_date' => $request->check_date,
                'bank_name' => $request->bank_name,
                'payment_date' => $request->payment_date,
                'bank_date' => $request->bank_name ? ($request->bank_name . ($request->payment_date ? ' ' . $request->payment_date : '')) : null,
                'document_no' => $request->receipt_no ?: ($request->check_number ?: $request->reference_no),
                'amount' => $request->amount,
                'proof_attachment' => $attachmentPath,
            ]);

            return redirect()->route('admin-finance.accounting.payment-posting.index')
                ->with('success', 'Payment Posting Request created successfully and added to Pending queue.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create Payment Posting: ' . $e->getMessage())->withInput();
        }
    }

    public function paymentPostingShow($id)
    {
        $posting = \App\Models\ClientPaymentPosting::with(['preparer', 'items.customer', 'items.account'])
            ->findOrFail($id);

        return view('admin-finance.accounting.payment-posting.show', [
            'title' => 'Client Payment Posting Details',
            'role' => 'Finance Staff',
            'posting' => $posting
        ]);
    }

    public function paymentPostingPost($id)
    {
        $posting = \App\Models\ClientPaymentPosting::with(['items.customer', 'items.account'])->findOrFail($id);
        
        if ($posting->status === 'posted') {
            return redirect()->back()->with('info', 'This payment posting has already been posted.');
        }

        \DB::transaction(function () use ($posting) {
            // Dynamic AR account lookup via database type and name
            $arAccount = \App\Models\ChartOfAccount::where('type', 'Asset')
                ->where(function($q) {
                    $q->where('name', 'like', '%Receivable%')->orWhere('code', 'like', '12%');
                })->first() ?? \App\Models\ChartOfAccount::where('type', 'Asset')->first();
            
            $totalAmount = $posting->items->sum('amount') ?: 0.00;
            $firstItem = $posting->items->first();

            $depositAccountId = $firstItem ? $firstItem->chart_of_account_id : null;
            if (!$depositAccountId) {
                $depositAcc = \App\Models\ChartOfAccount::where('type', 'Asset')
                    ->where(function($q) {
                        $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%');
                    })->first() ?? \App\Models\ChartOfAccount::where('type', 'Asset')->first();
                $depositAccountId = $depositAcc ? $depositAcc->id : null;
            }

            $customerName = $firstItem && $firstItem->customer ? $firstItem->customer->customer_name : 'Client';
            $refNo = $firstItem && $firstItem->receipt_no ? $firstItem->receipt_no : ($firstItem->reference_no ?? 'PP-' . str_pad($posting->id, 5, '0', STR_PAD_LEFT));

            $entry = \App\Models\JournalEntry::create([
                'entry_no' => 'JV-PP-' . str_pad($posting->id, 5, '0', STR_PAD_LEFT),
                'entry_type' => 'CR',
                'date' => $posting->date ?: now(),
                'reference' => $refNo,
                'memo' => "Client Payment Posting for {$customerName} (Ref: {$refNo})",
                'currency' => 'PHP',
                'exchange_rate' => 1.0000,
                'created_by' => auth()->id() ?? 1,
                'status' => 'posted',
            ]);

            \App\Models\JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $depositAccountId,
                'debit' => $totalAmount,
                'credit' => 0,
                'memo' => "Cash/Bank Deposit for Payment Posting #" . $posting->id,
            ]);

            \App\Models\JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $totalAmount,
                'memo' => "Accounts Receivable reduction for {$customerName}",
            ]);

            $posting->update(['status' => 'posted']);
        });

        return redirect()->route('admin-finance.accounting.payment-posting.index')
            ->with('success', 'Client Payment Posting #' . str_pad($posting->id, 5, '0', STR_PAD_LEFT) . ' has been posted successfully to the General Ledger & Chart of Accounts!');
    }

    public function autoDebitStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'debit_date' => 'required|date',
            'item_reason' => 'required|string|max:255',
            'source_origin' => 'required|string|max:255',
        ]);

        try {
            $debit = \App\Models\AutoDebit::create([
                'date' => $request->date,
                'amount' => $request->amount,
                'debit_date' => $request->debit_date,
                'item_reason' => $request->item_reason,
                'source_origin' => $request->source_origin,
                'prepared_by' => auth()->id(),
                'status' => 'pending_director',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auto Debit letter generated successfully and sent to Director for approval.',
                'id' => $debit->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Auto Debit letter: ' . $e->getMessage()
            ], 500);
        }
    }

    public function autoDebitShow($id)
    {
        $debit = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->findOrFail($id);

        $user = auth()->user();
        $sidebar = 'production';
        if (str_contains(strtolower($user->division ?? ''), 'admin') || str_contains(strtolower($user->division ?? ''), 'finance') || str_contains(strtolower($user->department ?? ''), 'admin') || str_contains(strtolower($user->department ?? ''), 'finance')) {
            $sidebar = 'admin-finance';
        }
        if (str_contains(strtolower($user->position ?? ''), 'director')) {
            $sidebar = 'director';
        }

        return view('production.ford.auto-debit.show', [
            'title' => 'Auto Debit Letter Details',
            'role' => $user->position,
            'sidebar' => $sidebar,
            'debit' => $debit
        ]);
    }

    public function autoDebitApproveDirector($id)
    {
        $user = auth()->user();
        $pos = $user->position ?? '';
        $isDirector = str_contains(strtolower($pos), 'director') || $user->isSuperAdmin();
        if (!$isDirector) {
            return redirect()->back()->with('error', 'Only the Director can perform this approval.');
        }

        $debit = \App\Models\AutoDebit::findOrFail($id);
        
        if ($debit->status !== 'pending_director') {
            return redirect()->back()->with('error', 'This request is not pending Director approval.');
        }

        $debit->update([
            'status' => 'pending_finance',
            'director_approved_by' => $user->id,
            'director_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Auto Debit has been approved by the Director and sent to Admin & Finance Manager/Supervisor.');
    }

    public function autoDebitApproveFinance($id)
    {
        $user = auth()->user();
        $pos = $user->position ?? '';
        $isAFManager = str_contains($pos, 'Manager') || str_contains($pos, 'Supervisor') || $pos === 'A&F Manager' || $user->isSuperAdmin();
        if (!$isAFManager) {
            return redirect()->back()->with('error', 'Only Admin and Finance Managers/Supervisors can perform this approval.');
        }

        $debit = \App\Models\AutoDebit::findOrFail($id);
        
        if ($debit->status !== 'pending_finance') {
            return redirect()->back()->with('error', 'This request is not pending Finance approval.');
        }

        $debit->update([
            'status' => 'approved',
            'finance_approved_by' => $user->id,
            'finance_approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Auto Debit has been approved by the Admin & Finance Manager/Supervisor.');
    }

    public function autoDebitReject($id)
    {
        $debit = \App\Models\AutoDebit::findOrFail($id);
        $debit->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Auto Debit has been rejected.');
    }

    public function accountingAutoDebitIndex()
    {
        $debits = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);

        return view('admin-finance.accounting.auto-debit.index', [
            'title' => 'Approved Auto Debit Letters',
            'role' => 'Finance Staff',
            'debits' => $debits
        ]);
    }

    public function accountingAutoDebitShow($id)
    {
        $debit = \App\Models\AutoDebit::with(['preparer', 'directorApprover', 'financeApprover'])
            ->findOrFail($id);

        return view('admin-finance.accounting.auto-debit.show', [
            'title' => 'Auto Debit Details',
            'role' => 'Finance Staff',
            'debit' => $debit
        ]);
    }

    /**
     * Save a Ford Purchase Order to the database and redirect to the Logistics show page.
     */
    public function storeFordPurchaseOrder(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'po_number'     => 'required|unique:purchase_orders,po_number',
            'date'          => 'required|date',
            'product_id'    => 'required|array|min:1',
            'product_id.*'  => 'required|string',
            'description'   => 'required|array|min:1',
            'description.*' => 'required|string',
            'quantity'      => 'required|array|min:1',
            'quantity.*'    => 'required|numeric|min:1',
            'unit_price'    => 'required|array|min:1',
            'unit_price.*'  => 'required|numeric|min:0',
        ]);

        $items = [];
        $totalAmount = 0;

        foreach ($request->description as $i => $desc) {
            $qty   = $request->quantity[$i] ?? 0;
            $price = $request->unit_price[$i] ?? 0;
            $total = $qty * $price;
            $totalAmount += $total;

            $prodId = $request->product_id[$i] ?? null;

            if ($prodId === 'new_custom_book') {
                // Automatically create Book if it doesn't exist
                $book = \App\Models\Book::where('name', $desc)->first();
                if (!$book) {
                    // Generate a unique SKU
                    $uniqueSku = 'SKU-' . strtoupper(substr(md5(uniqid($desc, true)), 0, 8));
                    while (\App\Models\Book::where('sku', $uniqueSku)->exists()) {
                        $uniqueSku = 'SKU-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    }

                    $book = \App\Models\Book::create([
                        'name' => $desc,
                        'sku' => $uniqueSku,
                        'price' => 0.00,
                        'cost' => 0.00,
                        'stock' => 0,
                        'is_active' => true,
                    ]);
                }

                // Automatically create Product for the new Book
                $product = \App\Models\Product::where('book_id', $book->id)->first();
                if (!$product) {
                    $product = \App\Models\Product::create([
                        'book_id' => $book->id,
                        'name' => $desc,
                        'price' => 0.00,
                        'is_active' => true,
                    ]);
                }

                $prodId = $product->id;
            }

            $items[] = [
                'product_id'   => $prodId,
                'description'  => $desc,
                'language'     => $request->language[$i] ?? null,
                'ft'           => $request->ft[$i] ?? null,
                'quantity'     => $qty,
                'unit_price'   => $price,
                'total_amount' => $total,
                'bindings'     => $request->bindings[$i] ?? null,
                'item_remarks' => $request->item_remarks[$i] ?? null,
            ];
        }

        $po = \App\Models\PurchaseOrder::create([
            'po_number'         => $request->po_number,
            'supplier_id'       => $request->supplier_id,
            'date'              => $request->date,
            'terms'             => $request->terms,
            'total_amount'      => $totalAmount,
            'status'            => 'ordered',
            'source'            => 'ford',
            'vendor_name'       => $request->vendor_name,
            'contact_persons'   => $request->contact_persons,
            'vendor_address'    => $request->vendor_address,
            'payment_schedule'  => $request->payment_schedule,
            'payment_schedule2' => $request->payment_schedule2,
            'currency'          => $request->currency ?? 'USD',
            'prepared_by'       => auth()->id(),
        ]);

        foreach ($items as $item) {
            $po->items()->create($item);
        }

        \App\Models\ActivityLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'Ford PO created',
            'description'    => 'Ford PO #' . $po->po_number . ' saved and sent to Logistics.',
            'reference_type' => 'PurchaseOrder',
            'reference_id'   => $po->id,
            'details'        => json_encode(['po_number' => $po->po_number, 'total' => $totalAmount]),
        ]);

        return redirect()
            ->route('production.logistic.purchase-order.show', $po->id)
            ->with('success', 'Purchase Order #' . $po->po_number . ' saved and sent to Logistics.');
    }

    public function freightQuotationIndex(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');
        
        $query = \App\Models\FreightQuotation::with(['createdBy', 'respondedBy', 'salesOrder', 'customer'])
            ->where(function($q) {
                $q->where('source', 'ford')->orWhere('quote_number', 'like', 'FRQ-FORD-%');
            });

        if ($status !== 'all') {
            $query->where('workflow_status', $status);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('quote_number', 'like', '%' . $search . '%')
                  ->orWhere('origin_province', 'like', '%' . $search . '%')
                  ->orWhere('destination_province', 'like', '%' . $search . '%')
                  ->orWhere('service_mode', 'like', '%' . $search . '%')
                  ->orWhere('forwarder', 'like', '%' . $search . '%')
                  ->orWhere('terms', 'like', '%' . $search . '%')
                  ->orWhere('customer_representative', 'like', '%' . $search . '%')
                  ->orWhereHas('createdBy', function($u) use ($search) {
                      $u->where(function($sub) use ($search) {
                          $sub->where('first_name', 'like', '%' . $search . '%')
                              ->orWhere('last_name', 'like', '%' . $search . '%')
                              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%']);
                      });
                  })
                  ->orWhereHas('salesOrder', function($so) use ($search) {
                      $so->where('so_number', 'like', '%' . $search . '%')
                         ->orWhere('forwarder', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('customer_name', 'like', '%' . $search . '%');
                  });
            });
        }

        $quotations = $query->latest()->paginate(10)->withQueryString();

        return view('marketing.freight-quotations.list', [
            'title' => 'Freight Quotations (FORD)',
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'quotations' => $quotations,
            'currentStatus' => $status,
            'search' => $search,
            'isFord' => true,
            'indexRoute' => 'production.ford.freight-quotation.index',
            'createRoute' => 'production.ford.freight-quotation.create',
            'showRoute' => 'production.ford.freight-quotation.show',
        ]);
    }

    public function freightQuotationCreate()
    {
        $customers = \App\Models\Customer::all();
        $products = (new \App\Http\Controllers\MarketingController)->getUnifiedProducts();
        
        return view('marketing.freight-quotations.create', [
            'title' => 'Create Freight Quotation (FORD)',
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'customers' => $customers,
            'products' => $products,
            'isFord' => true,
            'storeRoute' => route('production.ford.freight-quotation.store'),
        ]);
    }

    public function freightQuotationStore(Request $request)
    {
        $request->merge(['source' => 'ford']);
        $fqCtrl = new \App\Http\Controllers\Marketing\FreightQuotationController();
        $response = $fqCtrl->store($request);
        
        return redirect()->route('production.ford.freight-quotation.index')
            ->with('success', 'Freight Quotation created successfully and sent to Logistics for review.');
    }

    public function freightQuotationShow($id)
    {
        $quotation = \App\Models\FreightQuotation::with(['createdBy', 'respondedBy', 'salesOrder.items'])->findOrFail($id);
        $allBooks = \App\Models\Book::where('is_active', true)->orderBy('name')->get();
        $products = (new \App\Http\Controllers\MarketingController)->getUnifiedProducts();
        
        return view('marketing.freight-quotations.show', [
            'title' => 'Freight Quotation: ' . $quotation->quote_number,
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'quotation' => $quotation,
            'allBooks' => $allBooks,
            'products' => $products,
            'isFord' => true,
            'indexRoute' => 'production.ford.freight-quotation.index',
            'createSoRoute' => 'production.ford.freight-quotation.create-so-directly',
            'proceedSoRoute' => 'production.ford.freight-quotation.proceed-to-so',
        ]);
    }

    public function freightQuotationUpdate(Request $request, $id)
    {
        $quotation = \App\Models\FreightQuotation::findOrFail($id);
        $fqCtrl = new \App\Http\Controllers\Marketing\FreightQuotationController();
        return $fqCtrl->update($request, $quotation);
    }
}

