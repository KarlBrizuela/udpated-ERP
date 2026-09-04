<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\PaymentSetting;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;

class POSController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }
    /**
     * Get the next SI Number based on the latest entered SI number in the specific channel.
     * Supported channels:
     * - 'mibf' (MIBF POS)
     * - 'pos' or 'direct_sales' (Direct Sales POS / Calculator POS)
     * - 'sales_invoice' or 'accounting' (General Sales Invoices / Accounting)
     */
    public static function getNextSiNumber(string $channel = 'pos'): string
    {
        $normalizedChannel = strtolower(trim($channel));

        if (in_array($normalizedChannel, ['mibf', 'mibf_pos', 'ecom', 'ecom_pos'])) {
            // 1. MIBF POS: Check latest MIBF SalesOrder with non-empty si_number
            $lastOrder = SalesOrder::whereNotNull('si_number')
                ->where('si_number', '!=', '')
                ->where(function($q) {
                    $q->where('type', 'ecom_direct')
                      ->orWhere('ecom_platform', 'MIBF')
                      ->orWhere('platform', 'mibf')
                      ->orWhere('so_number', 'like', 'MIBF-%');
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($lastOrder && !empty($lastOrder->si_number)) {
                return self::calculateNextSiNumber($lastOrder->si_number);
            }

            // Also check latest SalesInvoice with transaction_type = 'mibf_si'
            $lastInvoice = \App\Models\SalesInvoice::whereNotNull('si_number')
                ->where('si_number', '!=', '')
                ->where('transaction_type', 'mibf_si')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastInvoice && !empty($lastInvoice->si_number)) {
                return self::calculateNextSiNumber($lastInvoice->si_number);
            }

            return '00001';
        }

        if (in_array($normalizedChannel, ['pos', 'direct_sales', 'direct_sale', 'calculator_pos'])) {
            // 2. Direct Sales POS: Check latest POS SalesOrder with non-empty si_number
            $lastOrder = SalesOrder::whereNotNull('si_number')
                ->where('si_number', '!=', '')
                ->where(function($q) {
                    $q->whereIn('type', ['calculator_pos', 'pos', 'direct_sale'])
                      ->orWhere('so_number', 'like', 'POS-%');
                })
                ->where(function($q) {
                    $q->where('so_number', 'not like', 'MIBF-%')
                      ->where(function($sq) {
                          $sq->whereNull('ecom_platform')
                             ->orWhere('ecom_platform', '!=', 'MIBF');
                      });
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($lastOrder && !empty($lastOrder->si_number)) {
                return self::calculateNextSiNumber($lastOrder->si_number);
            }

            // Also check latest SalesInvoice with transaction_type = 'pos_si'
            $lastInvoice = \App\Models\SalesInvoice::whereNotNull('si_number')
                ->where('si_number', '!=', '')
                ->where('transaction_type', 'pos_si')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastInvoice && !empty($lastInvoice->si_number)) {
                return self::calculateNextSiNumber($lastInvoice->si_number);
            }

            return '00001';
        }

        // 3. Sales Invoice / Accounting / General Sales Orders
        $lastOrder = SalesOrder::whereNotNull('si_number')
            ->where('si_number', '!=', '')
            ->whereNotIn('type', ['calculator_pos', 'pos', 'ecom_direct'])
            ->where('so_number', 'not like', 'POS-%')
            ->where('so_number', 'not like', 'MIBF-%')
            ->where(function($q) {
                $q->whereNull('ecom_platform')
                  ->orWhere('ecom_platform', '!=', 'MIBF');
            })
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && !empty($lastOrder->si_number)) {
            return self::calculateNextSiNumber($lastOrder->si_number);
        }

        // Also check latest SalesInvoice for general sales invoices
        $lastInvoice = \App\Models\SalesInvoice::whereNotNull('si_number')
            ->where('si_number', '!=', '')
            ->whereNotIn('transaction_type', ['pos_si', 'mibf_si'])
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice && !empty($lastInvoice->si_number)) {
            return self::calculateNextSiNumber($lastInvoice->si_number);
        }

        return '00001';
    }

    /**
     * Automatically increment the numeric part of the SI number while preserving leading zeros and prefix.
     * e.g., '00123' -> '00124', 'SI-00123' -> 'SI-00124', '1' -> '2'
     */
    public static function calculateNextSiNumber(?string $currentSi): string
    {
        if (empty($currentSi)) {
            return '00001';
        }

        $trimmed = trim($currentSi);
        if ($trimmed === '') {
            return '00001';
        }

        // Match prefix and numeric suffix
        if (preg_match('/^(.*?)(\d+)$/', $trimmed, $matches)) {
            $prefix = $matches[1];
            $digits = $matches[2];
            $digitLen = strlen($digits);
            $nextNum = intval($digits) + 1;
            $nextDigits = str_pad((string)$nextNum, $digitLen, '0', STR_PAD_LEFT);
            return $prefix . $nextDigits;
        }

        return $trimmed . '1';
    }

    /**
     * JSON endpoint to fetch the next SI number dynamically
     */
    public function getNextSiNumberResponse(Request $request)
    {
        $channel = $request->query('channel', $request->query('type', 'pos'));
        return response()->json([
            'success' => true,
            'channel' => $channel,
            'next_si_number' => self::getNextSiNumber($channel)
        ]);
    }

    /**
     * Process a POS order
     */
    public function processOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id'         => 'nullable|exists:customers,customer_id',
            'si_number'           => 'nullable|string|max:50',
            'payment_method'      => 'required|in:cash,gcash,paymaya,maya,card,bank,bank_transfer,check',
            'payment_reference'   => 'required_unless:payment_method,cash|nullable|string|max:20',
            'cash_received'       => ['nullable', 'required_if:payment_method,cash', 'numeric', 'min:0'],
            'items'               => 'required|array|min:1|max:24',
            'items.*.product_id'  => 'nullable|exists:books,id',
            'items.*.bundle_id'   => 'nullable|exists:book_bundles,id',
            'items.*.book_index_id' => 'nullable|exists:book_indices,id',
            'items.*.quantity'    => 'required|numeric|min:0.1',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.discount_value'  => 'nullable|numeric|min:0',
            'items.*.discount_type'   => 'nullable|string|in:amount,percentage',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.subtotal'        => 'nullable|numeric|min:0',
            'subtotal'            => 'required|numeric|min:0',
            'tax'                 => 'required|numeric|min:0',
            'total'               => 'required|numeric|min:0',
            'discount_value'      => 'nullable|numeric|min:0',
            'discount_type'       => 'nullable|string|in:amount,percentage',
        ]);

        if (count($validated['items']) > 24) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum of 24 products allowed per order.'
            ], 422);
        }

        // Validate cash payment
        if ($validated['payment_method'] === 'cash') {
            if ($validated['cash_received'] < $validated['total']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient cash received'
                ], 422);
            }
        }

        // ── STOCK VALIDATION ────────────────────────────────────────────────
        $insufficientItems = [];

        foreach ($validated['items'] as $item) {
            $qty = (int) $item['quantity'];

            if (!empty($item['bundle_id'])) {
                // --- Bundle item ---
                $bundle = \App\Models\BookBundle::with(['books' => fn($q) => $q->withPivot('quantity')->withSum('inventory as stock', 'quantity')])
                            ->find($item['bundle_id']);

                if (!$bundle || $bundle->stock < $qty) {
                    $name = $bundle ? $bundle->name : "Bundle #{$item['bundle_id']}";
                    $avail = $bundle ? $bundle->stock : 0;
                    $insufficientItems[] = "$name (Bundle stock available: $avail, requested: $qty)";
                } else {
                    // Check each book inside the bundle
                    foreach ($bundle->books as $book) {
                        $need = $book->pivot->quantity * $qty;
                        if ($book->stock < $need) {
                            $insufficientItems[] = "{$book->name} in bundle {$bundle->name} (Available: {$book->stock} pcs, Needed: $need pcs)";
                        }
                    }
                }
            } elseif (!empty($item['book_index_id'])) {
                // --- Book Index item ---
                $index = \App\Models\BookIndex::find($item['book_index_id']);
                $stock = $index ? (int)($index->main_stock ?? $index->stock ?? 0) : 0;
                if (!$index || $stock < $qty) {
                    $name = $index ? $index->display_name : "Index #{$item['book_index_id']}";
                    $insufficientItems[] = "$name (Index stock available: $stock, requested: $qty)";
                }
            } else {
                // --- Regular book item ---
                $book = Book::withSum('inventory as stock', 'quantity')->find($item['product_id']);
                if (!$book || $book->stock < $qty) {
                    $bookName  = $book ? $book->name : "Product #{$item['product_id']}";
                    $available = $book ? $book->stock : 0;
                    $insufficientItems[] = "$bookName (Available: $available pcs, Requested: $qty pcs)";
                }
            }
        }

        if (!empty($insufficientItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock: ' . implode('; ', $insufficientItems)
            ], 422);
        }
        // ────────────────────────────────────────────────────────────────────

        try {
            DB::beginTransaction();

            // Generate order number
            $posConfig   = \App\Models\PaymentSetting::getSetting('pos_config', ['orderPrefix' => 'POS']);
            $prefix      = $posConfig['orderPrefix'] ?? 'POS';
            $date        = now()->format('Ymd');
            $lastOrder   = SalesOrder::where('so_number', 'like', "{$prefix}-{$date}-%")
                            ->orderBy('so_number', 'desc')->first();
            $lastNumber  = $lastOrder ? (int) substr($lastOrder->so_number, -4) : 0;
            $orderNumber = "{$prefix}-{$date}-" . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $changeAmount = $validated['payment_method'] === 'cash'
                ? $validated['cash_received'] - $validated['total']
                : null;

            $discountAmount = 0;
            $discountPercentage = 0;
            if (!empty($validated['discount_value']) && $validated['discount_value'] > 0) {
                $discountValue = (float) $validated['discount_value'];
                if ($validated['discount_type'] === 'percentage') {
                    $discountPercentage = $discountValue;
                    $discountAmount = $validated['subtotal'] * ($discountPercentage / 100);
                } else {
                    $discountAmount = $discountValue;
                }
            }

            // Create sales order header
            $order = SalesOrder::create([
                'customer_id'      => $validated['customer_id'] ?? null,
                'so_number'        => $orderNumber,
                'si_number'        => !empty($validated['si_number']) ? trim($validated['si_number']) : null,
                'type'             => 'calculator_pos',
                'status'           => 'completed',
                'payment_method'   => $validated['payment_method'],
                'payment_reference'=> $validated['payment_reference'] ?? null,
                'cash_received'    => $validated['cash_received'] ?? null,
                'change_amount'    => $changeAmount,
                'total_amount'     => $validated['total'],
                'tax_amount'       => $validated['tax'],
                'discount_amount'  => $discountAmount,
                'discount_percentage' => $discountPercentage ?? 0,
                'prepared_by'      => auth()->id(),
                'approved_by_mkt'  => auth()->id(),
                'approved_by_acct' => auth()->id(),
                'mkt_approved_at'  => now(),
                'acct_approved_at' => now(),
            ]);

            // Aggregate duplicate item entries to prevent duplicate line items
            $aggregatedItems = [];
            foreach ($validated['items'] as $item) {
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                $key = (!empty($item['bundle_id']) ? 'bundle_' . $item['bundle_id'] : (!empty($item['book_index_id']) ? 'index_' . $item['book_index_id'] : 'prod_' . ($item['product_id'] ?? 'none')))
                     . '_' . $discVal . '_' . $discType;

                if (isset($aggregatedItems[$key])) {
                    $aggregatedItems[$key]['quantity'] += (float) $item['quantity'];
                } else {
                    $aggregatedItems[$key] = [
                        'product_id'     => $item['product_id'] ?? null,
                        'bundle_id'      => $item['bundle_id'] ?? null,
                        'book_index_id'  => $item['book_index_id'] ?? null,
                        'quantity'       => (float) $item['quantity'],
                        'price'          => (float) $item['price'],
                        'discount_value' => $discVal,
                        'discount_type'  => $discType,
                        'subtotal'       => (float) ($item['subtotal'] ?? 0),
                    ];
                }
            }
            $validated['items'] = array_values($aggregatedItems);

            // ── PROCESS EACH LINE ITEM ───────────────────────────────────────
            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['price'];
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                $gross = $qty * $price;
                $discAmount = $discType === 'percentage' ? $gross * ($discVal / 100) : $discVal;
                $discAmount = min($gross, max(0, $discAmount));
                $subtotal = isset($item['subtotal']) && $item['subtotal'] > 0 ? (float) $item['subtotal'] : max(0, $gross - $discAmount);

                if (!empty($item['bundle_id'])) {
                    // ── Bundle item ───────────────────────────────────────
                    $bundle = \App\Models\BookBundle::with(['books' => fn($q) => $q->withPivot('quantity')])
                                ->find($item['bundle_id']);

                    // 1. Save the line item (book_id is null for bundle lines)
                    SalesOrderItem::create([
                        'sales_order_id'      => $order->id,
                        'book_id'             => null,
                        'bundle_id'           => $bundle->id,
                        'quantity'            => $qty,
                        'price'               => $price,
                        'discount_value'      => $discVal,
                        'discount_type'       => $discType,
                        'discount_amount'     => $discAmount,
                        'subtotal'            => $subtotal,
                        'unit'                => 'pcs',
                        'source_price_at_sale'=> 0,
                    ]);

                } elseif (!empty($item['book_index_id'])) {
                    // ── Book Index item ───────────────────────────────────
                    $index = \App\Models\BookIndex::with('book')->find($item['book_index_id']);

                    SalesOrderItem::create([
                        'sales_order_id'      => $order->id,
                        'book_id'             => $index ? $index->book_id : null,
                        'book_index_id'       => $index ? $index->id : null,
                        'bundle_id'           => null,
                        'quantity'            => $qty,
                        'price'               => $price,
                        'discount_value'      => $discVal,
                        'discount_type'       => $discType,
                        'discount_amount'     => $discAmount,
                        'subtotal'            => $subtotal,
                        'unit'                => 'pcs',
                        'source_price_at_sale'=> $index ? ($index->price ?: ($index->book?->source_price ?? 0)) : 0,
                    ]);

                } else {
                    // ── Regular book item ─────────────────────────────────
                    $book = Book::find($item['product_id']);

                    SalesOrderItem::create([
                        'sales_order_id'      => $order->id,
                        'book_id'             => $item['product_id'],
                        'bundle_id'           => null,
                        'quantity'            => $qty,
                        'price'               => $price,
                        'discount_value'      => $discVal,
                        'discount_type'       => $discType,
                        'discount_amount'     => $discAmount,
                        'subtotal'            => $subtotal,
                        'unit'                => 'pcs',
                        'source_price_at_sale'=> $book ? $book->source_price : 0,
                    ]);
                }
            }
            // ────────────────────────────────────────────────────────────────

            // Deduct stock via StockDeductionService (respects user sales_team vs Main Warehouse)
            \App\Services\StockDeductionService::deductForSalesOrder($order);

            // Synchronize Sales Invoice record if SI number was provided
            if (!empty($order->si_number)) {
                \App\Models\SalesInvoice::updateOrCreate(
                    ['so_id' => $order->id],
                    [
                        'so_number'        => $order->so_number,
                        'si_number'        => $order->si_number,
                        'customer_id'      => $order->customer_id,
                        'customer_name'    => $order->customer?->customer_name ?? 'N/A',
                        'transaction_type' => 'pos_si',
                        'total_amount'     => $order->total_amount,
                        'status'           => 'approved',
                        'created_by'       => auth()->id(),
                        'approved_by'      => auth()->id(),
                        'posted_by'        => auth()->id(),
                    ]
                );
            }

            // Accounting integration
            $this->accounting->postSalesOrderEntry($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully',
                'next_si_number' => self::getNextSiNumber('pos'),
                'order'   => [
                    'id'             => $order->id,
                    'order_number'   => $orderNumber,
                    'si_number'      => $order->si_number,
                    'total'          => $validated['total'],
                    'payment_method' => $validated['payment_method'],
                    'change'         => $changeAmount,
                    'created_at'     => $order->created_at->format('Y-m-d h:i A'),
                    'print_url'      => route('marketing.sales-orders.print-invoice', $order->id),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get POS order history
     */
    public function getOrders(Request $request)
    {
        $orders = SalesOrder::with(['customer', 'preparedBy'])
            ->where('type', 'calculator_pos')
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    /**
     * Get specific order details
     */
    public function getOrderDetails($id)
    {
        $order = SalesOrder::with(['customer', 'items.book', 'preparedBy'])
            ->where('type', 'calculator_pos')
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Get payment settings for POS
     */
    public function getPaymentSettings()
    {
        $settings = PaymentSetting::getAllSettings();
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Save payment settings (for super admin)
     */
    public function savePaymentSettings(Request $request)
    {
        $validated = $request->validate([
            'gcash' => 'nullable|array',
            'gcash.number' => 'nullable|string',
            'gcash.qr' => 'nullable|string',
            'paymaya' => 'nullable|array',
            'paymaya.number' => 'nullable|string',
            'paymaya.qr' => 'nullable|string',
            'bank' => 'nullable|array',
            'bank.name' => 'nullable|string',
            'bank.accountName' => 'nullable|string',
            'bank.accountNumber' => 'nullable|string',
            'bank.qr' => 'nullable|string',
            'pos_config' => 'nullable|array',
            'pos_config.taxRate' => 'nullable|numeric',
            'pos_config.currencySymbol' => 'nullable|string',
            'pos_config.orderPrefix' => 'nullable|string',
        ]);

        try {
            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    PaymentSetting::setSetting($key, $value);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment settings saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process E-commerce POS order
     */
    public function processEcomOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'si_number' => 'nullable|string|max:50',
            'platform' => 'nullable|string',
            'payment_method' => 'required|in:cod,cash,gcash,lazada,shopee,paymaya,maya,card,bank,check,bank_transfer',
            'items' => 'required|array|min:1|max:24',
            'items.*.product_id' => 'nullable',
            'items.*.type' => 'nullable|string',
            'items.*.book_id' => 'nullable',
            'items.*.book_index_id' => 'nullable',
            'items.*.book_bundle_id' => 'nullable',
            'items.*.quantity' => 'required|numeric|min:0.1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'cash_received' => 'nullable|numeric|min:0',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string|in:amount,percentage',
        ]);

        if (count($validated['items']) > 24) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum of 24 products allowed per order.'
            ], 422);
        }

        $platformName = !empty($validated['platform']) ? $validated['platform'] : 'MIBF';

        // STOCK VALIDATION: Check if all items have sufficient stock in MIBF Team Stock
        $insufficientItems = [];
        foreach ($validated['items'] as $item) {
            $bookId = $item['book_id'] ?? ($item['type'] === 'book' ? $item['product_id'] : null);
            $indexId = $item['book_index_id'] ?? ($item['type'] === 'index' ? $item['product_id'] : null);
            $bundleId = $item['book_bundle_id'] ?? ($item['type'] === 'bundle' ? $item['product_id'] : null);

            $ts = \App\Models\TeamStock::where('team_name', 'MIBF')
                ->where(function($q) use ($bookId, $indexId, $bundleId) {
                    if ($indexId) {
                        $q->where('book_index_id', $indexId);
                    } elseif ($bundleId) {
                        $q->where('book_bundle_id', $bundleId);
                    } elseif ($bookId) {
                        $q->where('book_id', $bookId);
                    }
                })->first();

            $avail = $ts ? (int)$ts->quantity : 0;
            if ($avail < $item['quantity']) {
                $itemName = "Item #" . ($item['product_id'] ?? '');
                if ($indexId) {
                    $idxObj = \App\Models\BookIndex::find($indexId);
                    if ($idxObj) $itemName = $idxObj->display_name;
                } elseif ($bundleId) {
                    $bunObj = \App\Models\BookBundle::find($bundleId);
                    if ($bunObj) $itemName = $bunObj->name;
                } elseif ($bookId) {
                    $bObj = Book::find($bookId);
                    if ($bObj) $itemName = $bObj->name;
                }
                $insufficientItems[] = "$itemName (MIBF Stock Available: $avail pcs, Requested: {$item['quantity']} pcs)";
            }
        }

        if (!empty($insufficientItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient MIBF stock: ' . implode('; ', $insufficientItems)
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate order number for MIBF
            $date = now()->format('Ymd');
            $prefix = 'MIBF';
            
            $lastOrder = SalesOrder::where('so_number', 'like', "{$prefix}-{$date}-%")
                ->orderBy('so_number', 'desc')
                ->first();
            
            if ($lastOrder) {
                $lastNumber = (int) substr($lastOrder->so_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            $orderNumber = "{$prefix}-{$date}-{$newNumber}";
            $paymentStatus = ($validated['payment_method'] === 'cod') ? 'unpaid' : 'paid';

            $changeAmount = ($validated['payment_method'] === 'cash' && !empty($validated['cash_received']))
                ? max(0, $validated['cash_received'] - $validated['total'])
                : null;

            $discountAmount = 0;
            $discountPercentage = 0;
            if (!empty($validated['discount_value']) && $validated['discount_value'] > 0) {
                $discountValue = (float) $validated['discount_value'];
                if ($validated['discount_type'] === 'percentage') {
                    $discountPercentage = $discountValue;
                    $discountAmount = $validated['subtotal'] * ($discountPercentage / 100);
                } else {
                    $discountAmount = $discountValue;
                }
            }

            // Create sales order
            $order = SalesOrder::create([
                'customer_id' => $validated['customer_id'],
                'so_number' => $orderNumber,
                'si_number' => !empty($validated['si_number']) ? trim($validated['si_number']) : null,
                'type' => 'ecom_direct',
                'status' => 'completed',
                'platform' => in_array(strtolower($platformName), ['lazada', 'shopee', 'tiktok', 'website', 'facebook', 'other']) ? strtolower($platformName) : 'other',
                'ecom_platform' => $platformName,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'payment_reference' => $validated['payment_reference'] ?? null,
                'cash_received' => $validated['cash_received'] ?? null,
                'change_amount' => $changeAmount,
                'total_amount' => $validated['total'],
                'tax_amount' => $validated['tax'],
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage ?? 0,
                'remarks' => $validated['notes'] ?? null,
                'prepared_by' => auth()->id(),
                'approved_by_mkt' => auth()->id(),
                'approved_by_acct' => auth()->id(),
                'mkt_approved_at' => now(),
                'acct_approved_at' => now(),
            ]);

            // Create order items & deduct stock from MIBF TeamStock
            foreach ($validated['items'] as $item) {
                $bookId = $item['book_id'] ?? ($item['type'] === 'book' ? $item['product_id'] : null);
                $indexId = $item['book_index_id'] ?? ($item['type'] === 'index' ? $item['product_id'] : null);
                $bundleId = $item['book_bundle_id'] ?? ($item['type'] === 'bundle' ? $item['product_id'] : null);

                $sourcePrice = 0;
                if ($bookId) {
                    $b = Book::find($bookId);
                    if ($b) $sourcePrice = $b->source_price;
                }

                $qty = (float) $item['quantity'];
                $price = (float) $item['price'];
                $discVal = (float) ($item['discount_value'] ?? 0);
                $discType = $item['discount_type'] ?? 'percentage';
                $gross = $qty * $price;
                $discAmount = $discType === 'percentage' ? $gross * ($discVal / 100) : $discVal;
                $discAmount = min($gross, max(0, $discAmount));
                $subtotal = isset($item['subtotal']) && $item['subtotal'] > 0 ? (float) $item['subtotal'] : max(0, $gross - $discAmount);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'book_id' => $bookId,
                    'book_index_id' => $indexId,
                    'bundle_id' => $bundleId,
                    'quantity' => $qty,
                    'price' => $price,
                    'discount_value' => $discVal,
                    'discount_type' => $discType,
                    'discount_amount' => $discAmount,
                    'subtotal' => $subtotal,
                    'unit' => 'pcs',
                    'source_price_at_sale' => $sourcePrice,
                ]);

                // Decrement MIBF TeamStock
                $ts = \App\Models\TeamStock::where('team_name', 'MIBF')
                    ->where(function($q) use ($bookId, $indexId, $bundleId) {
                        if ($indexId) {
                            $q->where('book_index_id', $indexId);
                        } elseif ($bundleId) {
                            $q->where('book_bundle_id', $bundleId);
                        } elseif ($bookId) {
                            $q->where('book_id', $bookId);
                        }
                    })->first();

                if ($ts) {
                    $ts->quantity = max(0, ($ts->quantity ?? 0) - $item['quantity']);
                    $ts->save();
                }

                // Record inventory transaction
                \App\Models\InventoryTransaction::create([
                    'book_id' => $bookId,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'location' => 'MIBF',
                    'source' => 'MIBF POS',
                    'reference_number' => $orderNumber,
                    'unit_cost' => 0,
                    'total_cost' => 0,
                    'notes' => 'MIBF POS Order #' . $orderNumber,
                    'status' => 'completed',
                    'transaction_date' => now(),
                    'user_id' => auth()->id()
                ]);
            }

            // Synchronize site inventory for MIBF team site
            \App\Services\StockDeductionService::syncTeamSitesInventory();

            // Synchronize Sales Invoice record if SI number was provided
            if (!empty($order->si_number)) {
                \App\Models\SalesInvoice::updateOrCreate(
                    ['so_id' => $order->id],
                    [
                        'so_number'        => $order->so_number,
                        'si_number'        => $order->si_number,
                        'customer_id'      => $order->customer_id,
                        'customer_name'    => $order->customer?->customer_name ?? 'N/A',
                        'transaction_type' => 'mibf_si',
                        'total_amount'     => $order->total_amount,
                        'status'           => 'approved',
                        'created_by'       => auth()->id(),
                        'approved_by'      => auth()->id(),
                        'posted_by'        => auth()->id(),
                    ]
                );
            }

            // Accounting integration
            $this->accounting->postSalesOrderEntry($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'MIBF order processed successfully',
                'next_si_number' => self::getNextSiNumber('mibf'),
                'order' => [
                    'id' => $order->id,
                    'order_number' => $orderNumber,
                    'si_number' => $order->si_number,
                    'total' => $validated['total'],
                    'payment_status' => $paymentStatus,
                    'created_at' => $order->created_at->format('Y-m-d h:i A'),
                    'print_url' => route('marketing.sales-orders.print-invoice', $order->id),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process MIBF order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lookup product by barcode (for barcode scanner)
     */
    public function lookupByBarcode(Request $request)
    {
        $barcode = $request->input('barcode', '');
        
        if (!$barcode || strlen($barcode) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode'
            ], 400);
        }

        $barcode = trim($barcode);

        // Search by barcode or SKU
        $product = Book::where('is_active', true)
            ->where(function($query) use ($barcode) {
                $query->where('barcode', $barcode)
                      ->orWhere('sku', $barcode)
                      ->orWhere('nbs_barcode', $barcode);
            })
            ->select('id', 'name', 'price', 'barcode', 'sku', 'category', 'image', 'is_book')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->price,
                'barcode' => $product->barcode,
                'sku' => $product->sku,
                'category' => $product->is_book ? 'books' : 'non-books',
                'image' => $product->image ? asset('storage/' . $product->image) : asset('images/no-book-cover.svg')
            ]
        ]);
    }
}
