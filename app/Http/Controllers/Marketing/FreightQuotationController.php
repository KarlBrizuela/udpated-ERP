<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\FreightQuotation;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FreightQuotationController extends Controller
{
    /**
     * Display a listing of freight quotations in marketing
     */
    public function list(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');
        
        $query = FreightQuotation::with(['createdBy', 'respondedBy', 'salesOrder', 'customer'])
            ->where(function($q) {
                $q->where('source', 'marketing')->orWhereNull('source');
            })
            ->where('quote_number', 'not like', 'FRQ-FORD-%');

        if (auth()->user()->position !== 'Super Admin' && !str_contains(auth()->user()->position, 'Manager')) {
            $query->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereNull('created_by');
            });
        }

        // Filter by workflow status
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
            'title' => 'Freight Quotations',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'quotations' => $quotations,
            'currentStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new freight quotation
     */
    public function create()
    {
        $customers = Customer::all();
        $products = (new \App\Http\Controllers\MarketingController)->getUnifiedProducts();
        
        return view('marketing.freight-quotations.create', [
            'title' => 'Create Freight Quotation',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created freight quotation
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'transaction_type' => 'nullable|string|max:50',
                'origin_contact' => 'required|string|max:255',
                'origin_address' => 'required|string',
                'origin_province' => 'required|string|max:255',
                'destination_contact' => 'required|string|max:255',
                'destination_address' => 'required|string',
                'destination_province' => 'required|string|max:255',
                'service_mode' => 'required|string|max:255',
                'freight_mode' => 'nullable|string|max:255',
                'forwarder' => 'nullable|string|max:255',
                'freight_option' => 'nullable|string|in:freight_collect,freight_billing,bill_client',
                'currency' => 'nullable|string|in:PHP,USD,EUR',
                'forwarder' => 'nullable|string|max:255',
                'cargo_qty' => 'nullable|array',
                'cargo_qty.*' => 'nullable|integer|min:1',
                'cargo_package_type' => 'nullable|array',
                'cargo_package_type.*' => 'nullable|string',
                'cargo_dimensions' => 'nullable|array',
                'cargo_dimensions.*' => 'nullable|string',
                'so_items' => 'nullable|array|max:24',
                'so_items.*.product_id' => 'nullable|string',
                'so_items.*.quantity' => 'nullable|integer|min:1',
                'so_items.*.price' => 'nullable|numeric|min:0',
                'so_items.*.discount_value' => 'nullable|numeric|min:0',
                'so_items.*.discount_type' => 'nullable|string|in:amount,percentage',
            ], [
                'customer_id.required' => 'Customer is required',
                'customer_id.exists' => 'Selected customer does not exist',
                'origin_contact.required' => 'Origin contact is required',
                'destination_contact.required' => 'Destination contact is required',
            ]);

            if (!empty($request->so_items) && is_array($request->so_items)) {
                $validItems = array_filter($request->so_items, fn($item) => !empty($item['product_id']) && !empty($item['quantity']));
                if (count($validItems) > 24) {
                    return redirect()->back()->with('error', 'Cannot proceed with Freight Quotation: Maximum of 24 products allowed per order.')->withInput();
                }
            }

            DB::beginTransaction();

            // Generate quote number
            $source = $request->input('source', 'marketing');
            $prefix = $source === 'ford' ? 'FRQ-FORD-' : 'FRQ-';
            $quoteNumber = $prefix . date('Y') . '-' . str_pad(
                FreightQuotation::whereYear('created_at', date('Y'))->where('source', $source)->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            // Build cargo items array
            $cargoItems = [];
            if ($request->cargo_qty && is_array($request->cargo_qty)) {
                foreach ($request->cargo_qty as $index => $qty) {
                    if (!empty($qty)) {
                        $cargoItems[] = [
                            'qty' => (int) $qty,
                            'package_type' => $request->cargo_package_type[$index] ?? null,
                            'dimensions' => $request->cargo_dimensions[$index] ?? null,
                        ];
                    }
                }
            }

            // Create freight quotation record
            $quotation = FreightQuotation::create([
                'quote_number' => $quoteNumber,
                'quote_date' => now()->toDateString(),
                'validity_days' => 30,
                'customer_id' => $validated['customer_id'],
                'customer_representative' => $request->customer_representative,
                'transaction_type' => $validated['transaction_type'] ?? 'paid',
                'origin_contact' => $validated['origin_contact'],
                'origin_address' => $validated['origin_address'],
                'origin_province' => $validated['origin_province'],
                'destination_contact' => $validated['destination_contact'],
                'destination_address' => $validated['destination_address'],
                'destination_province' => $validated['destination_province'],
                'service_mode' => $validated['service_mode'],
                'freight_mode' => $validated['forwarder'] ?? $validated['freight_mode'] ?? null,
                'forwarder' => $validated['forwarder'] ?? $validated['freight_mode'] ?? null,
                'freight_option' => $validated['freight_option'] ?? null,
                'currency' => $request->input('currency', 'PHP'),
                'cargo_items' => !empty($cargoItems) ? json_encode($cargoItems) : null,
                'estimated_freight' => 0,
                'total_amount' => 0,
                'status' => 'pending',
                'workflow_status' => 'draft',
                'source' => $source,
                'created_by' => auth()->id(),
            ]);

            // Create draft SO if SO items are provided
            $salesOrder = null;
            if (!empty($request->so_items) && is_array($request->so_items)) {
                // Filter out empty items
                $soItems = array_filter($request->so_items, function($item) {
                    return !empty($item['product_id']) && !empty($item['quantity']);
                });

                if (!empty($soItems)) {
                    $isFordQuotation = ($validated['source'] ?? null) === 'ford' || $request->input('source') === 'ford';
                    if ($isFordQuotation) {
                        $soNumber = 'FORD-SO-' . date('Ymd') . '-' . rand(1000, 9999);
                        $soType = 'foreign';
                    } else {
                        $soNumber = 'SO-' . date('Y') . '-' . str_pad(
                            SalesOrder::whereYear('created_at', date('Y'))->count() + 1,
                            4,
                            '0',
                            STR_PAD_LEFT
                        );
                        $soType = $validated['transaction_type'] ?? 'paid';
                    }

                    // Calculate items total
                    $itemsTotal = 0;
                    foreach ($soItems as $item) {
                        $qty = (int) ($item['quantity'] ?? 0);
                        $price = (float) ($item['price'] ?? 0);
                        $discVal = (float) ($item['discount_value'] ?? 0);
                        $discType = $item['discount_type'] ?? 'percentage';
                        $gross = $qty * $price;
                        $discAmount = $discType === 'percentage' ? $gross * ($discVal / 100) : $discVal;
                        $itemsTotal += max(0, $gross - $discAmount);
                    }

                    if (($validated['freight_option'] ?? null) === 'freight_collect') {
                        $serviceFee = 50.00;
                        $fqCurr = $validated['currency'] ?? ($quotation->currency ?? 'PHP');
                        if ($fqCurr === 'USD') {
                            $serviceFee = 50.00 / 56.0;
                        } elseif ($fqCurr === 'EUR') {
                            $serviceFee = 50.00 / 62.0;
                        }
                        $itemsTotal += $serviceFee;
                    }

                    $salesOrder = SalesOrder::create([
                        'customer_id' => $validated['customer_id'],
                        'customer_representative' => $request->customer_representative ?? null,
                        'customer_contact' => $validated['destination_contact'] ?? null,
                        'shipping_address' => $validated['destination_address'] ?? null,
                        'billing_address' => $validated['destination_address'] ?? null,
                        'so_number' => $soNumber,
                        'type' => $soType,
                        'currency' => $validated['currency'] ?? ($quotation->currency ?? 'PHP'),
                        'status' => 'draft',
                        'total_amount' => $itemsTotal,
                        'freight_option' => $validated['freight_option'] ?? ($quotation->freight_option ?? null),
                        'forwarder' => $validated['forwarder'] ?? ($quotation->forwarder ?? null),
                        'prepared_by' => auth()->id(),
                        'remarks' => 'Created from Freight Quotation #' . $quoteNumber,
                    ]);

                    // Create SO items
                    $marketingCtrl = new \App\Http\Controllers\MarketingController();
                    foreach ($soItems as $item) {
                        $qty = (int) ($item['quantity'] ?? 0);
                        $price = (float) ($item['price'] ?? 0);
                        $discVal = (float) ($item['discount_value'] ?? 0);
                        $discType = $item['discount_type'] ?? 'percentage';
                        $gross = $qty * $price;
                        $discAmount = $discType === 'percentage' ? $gross * ($discVal / 100) : $discVal;
                        $subtotal = max(0, $gross - $discAmount);

                        $target = $marketingCtrl->resolveItemTarget($item['product_id']);

                        $salesOrder->items()->create([
                            'book_id' => $target['book_id'],
                            'bundle_id' => $target['bundle_id'],
                            'book_index_id' => $target['book_index_id'],
                            'quantity' => $qty,
                            'price' => $price,
                            'discount_value' => $discVal,
                            'discount_type' => $discType,
                            'discount_amount' => $discAmount,
                            'subtotal' => $subtotal,
                        ]);
                    }

                    // Deduct stock immediately upon Sales Order creation
                    \App\Services\StockDeductionService::deductForSalesOrder($salesOrder);

                    // Link freight quotation to SO
                    $quotation->update([
                        'sales_order_id' => $salesOrder->id,
                    ]);
                }
            }

            DB::commit();

            $message = 'Freight quotation #' . $quoteNumber . ' created successfully! Please wait for logistics review.';
            if ($salesOrder) {
                $message .= ' Sales Order #' . $salesOrder->so_number . ' has been created with ' . count($soItems) . ' item(s).';
            }

            return redirect()->route('marketing.freight-quotations.show', $quotation->id)
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating freight quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating freight quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified freight quotation
     */
    public function show(FreightQuotation $freightQuotation)
    {
        // Check authorization
        if ($freightQuotation->created_by !== auth()->id() && auth()->user()->position !== 'Super Admin') {
            return redirect()->route('marketing.freight-quotations.list')
                ->with('error', 'You are not authorized to view this quotation');
        }

        // Get all active books
        $allBooks = Book::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('marketing.freight-quotations.show', [
            'title' => 'Freight Quotation Details',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'quotation' => $freightQuotation,
            'allBooks' => $allBooks,
        ]);
    }

    /**
     * Create sales order directly from approved freight quotation
     */
    public function createSalesOrderFromApprovedQuotation(FreightQuotation $freightQuotation)
    {
        try {
            // Check authorization
            if ($freightQuotation->created_by !== auth()->id() && auth()->user()->position !== 'Super Admin') {
                return redirect()->route('marketing.freight-quotations.list')
                    ->with('error', 'You are not authorized to perform this action');
            }

            // Check if quotation is approved by logistics
            if ($freightQuotation->workflow_status !== 'approved') {
                return redirect()->route('marketing.freight-quotations.show', $freightQuotation->id)
                    ->with('error', 'This quotation must be approved by logistics before creating a sales order');
            }

            // Check if already linked to an SO
            if ($freightQuotation->sales_order_id) {
                return redirect()->route('marketing.sales-orders.show', $freightQuotation->sales_order_id)
                    ->with('info', 'Sales Order already created from this quotation');
            }

            // Generate SO number & type
            $isFordQuotation = $freightQuotation->source === 'ford';
            if ($isFordQuotation) {
                $soNumber = 'FORD-SO-' . date('Ymd') . '-' . rand(1000, 9999);
                $soType = 'foreign';
            } else {
                $soNumber = 'SO-' . date('Y') . '-' . str_pad(
                    SalesOrder::whereYear('created_at', date('Y'))->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
                $soType = 'paid';
            }

            // Create Sales Order with freight charges
            $customer = Customer::find($freightQuotation->customer_id) ?? 
                       Customer::find($freightQuotation->origin_contact);

            $serviceFee = 0;
            if ($freightQuotation->freight_option === 'freight_collect') {
                $fqCurr = $freightQuotation->currency ?? 'PHP';
                if ($fqCurr === 'USD') {
                    $serviceFee = 50.00 / 56.0;
                } elseif ($fqCurr === 'EUR') {
                    $serviceFee = 50.00 / 62.0;
                } else {
                    $serviceFee = 50.00;
                }
            }

            $salesOrder = SalesOrder::create([
                'customer_id' => $freightQuotation->customer_id ?? $customer?->customer_id,
                'customer_representative' => $freightQuotation->customer_representative ?? null,
                'customer_contact' => $freightQuotation->destination_contact ?? null,
                'shipping_address' => $freightQuotation->destination_address ?? $customer?->shipping_address ?? $customer?->billing_address ?? '',
                'billing_address' => $freightQuotation->destination_address ?? $customer?->shipping_address ?? $customer?->billing_address ?? '',
                'so_number' => $soNumber,
                'type' => $soType,
                'currency' => $freightQuotation->currency ?? 'PHP',
                'status' => 'pending_mkt_approval',
                'total_amount' => $freightQuotation->total_amount + $serviceFee,
                'freight_charges' => $freightQuotation->total_amount,
                'freight_notes' => $freightQuotation->logistics_notes ?? 'Freight approved from Quotation #' . $freightQuotation->quote_number,
                'freight_option' => $freightQuotation->freight_option,
                'forwarder' => $freightQuotation->forwarder ?? $freightQuotation->freight_mode ?? null,
                'prepared_by' => auth()->id(),
                'remarks' => 'Created from Freight Quotation #' . $freightQuotation->quote_number,
            ]);

            // Link FQ to SO and update status
            $freightQuotation->update([
                'sales_order_id' => $salesOrder->id,
                'workflow_status' => 'linked_to_so',
            ]);

            // Deduct stock immediately upon Sales Order creation
            \App\Services\StockDeductionService::deductForSalesOrder($salesOrder);

            if ($isFordQuotation) {
                return redirect()->route('production.ford.sales-order')
                    ->with('success', 'FORD Sales Order #' . $soNumber . ' created successfully with freight charges!');
            }

            return redirect()->route('marketing.sales-orders.show', $salesOrder->id)
                ->with('success', 'Sales Order #' . $soNumber . ' created successfully with freight charges of ₱' . number_format($freightQuotation->total_amount, 2));

        } catch (\Exception $e) {
            Log::error('Error creating sales order from quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating sales order: ' . $e->getMessage());
        }
    }

    /**
     * Proceed to create sales order from approved freight quotation
     */
    public function proceedToSalesOrder(FreightQuotation $freightQuotation)
    {
        try {
            // Check authorization
            if ($freightQuotation->created_by !== auth()->id() && auth()->user()->position !== 'Super Admin') {
                return redirect()->route('marketing.freight-quotations.list')
                    ->with('error', 'You are not authorized to perform this action');
            }

            // Check if quotation is approved by logistics
            if ($freightQuotation->workflow_status !== 'approved') {
                return redirect()->route('marketing.freight-quotations.show', $freightQuotation->id)
                    ->with('error', 'This quotation must be approved by logistics before creating a sales order');
            }

            // Check if already linked to an SO
            if ($freightQuotation->sales_order_id) {
                return redirect()->route('marketing.sales-orders.show', $freightQuotation->sales_order_id)
                    ->with('info', 'This freight quotation is already linked to Sales Order #' . $freightQuotation->salesOrder->so_number);
            }

            // Prepare data for SO creation
            $quotation = $freightQuotation->load(['createdBy', 'respondedBy']);

            return view('marketing.sales-orders.create-from-freight', [
                'title' => 'Create Sales Order from Freight Quotation',
                'role' => auth()->user()->position,
                'sidebar' => 'marketing',
                'quotation' => $quotation,
                'customers' => Customer::all(),
                'products' => (new \App\Http\Controllers\MarketingController)->getUnifiedProducts(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error proceeding to SO: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Create sales order from freight quotation
     */
    public function createSalesOrderFromQuotation(Request $request, FreightQuotation $freightQuotation)
    {
        try {
            // Validate freight quotation
            if ($freightQuotation->workflow_status !== 'approved') {
                return response()->json([
                    'message' => 'Freight quotation must be approved by logistics',
                ], 422);
            }

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'type' => 'required|in:paid,charge,area_consignment,direct_consignment,foreign,complimentary,cod,evaluation',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:books,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0.01',
                'items.*.discount_value' => 'nullable|numeric|min:0',
                'items.*.discount_type' => 'nullable|string|in:amount,percentage',
            ]);

            DB::beginTransaction();

            try {
                // Create sales order
                $isFordQuotation = $freightQuotation->source === 'ford' || $request->input('source') === 'ford';
                if ($isFordQuotation) {
                    $soNumber = 'FORD-SO-' . date('Ymd') . '-' . rand(1000, 9999);
                    $soType = 'foreign';
                } else {
                    $soNumber = 'SO-' . date('Y') . '-' . str_pad(
                        SalesOrder::whereYear('created_at', date('Y'))->count() + 1,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );
                    $soType = $validated['type'];
                }

                // Calculate total
                $totalAmount = 0;
                foreach ($validated['items'] as $item) {
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
                }

                // Add freight charges
                $totalAmount += $freightQuotation->total_amount;

                if ($freightQuotation->freight_option === 'freight_collect') {
                    $serviceFee = 50.00;
                    $fqCurr = $freightQuotation->currency ?? 'PHP';
                    if ($fqCurr === 'USD') {
                        $serviceFee = 50.00 / 56.0;
                    } elseif ($fqCurr === 'EUR') {
                        $serviceFee = 50.00 / 62.0;
                    }
                    $totalAmount += $serviceFee;
                }

                $salesOrder = SalesOrder::create([
                    'customer_id' => $validated['customer_id'],
                    'customer_representative' => $freightQuotation->customer_representative ?? null,
                    'customer_contact' => $freightQuotation->destination_contact ?? null,
                    'shipping_address' => $freightQuotation->destination_address ?? null,
                    'billing_address' => $freightQuotation->destination_address ?? null,
                    'so_number' => $soNumber,
                    'type' => $soType,
                    'currency' => $freightQuotation->currency ?? 'PHP',
                    'status' => 'draft',
                    'total_amount' => $totalAmount,
                    'freight_charges' => $freightQuotation->total_amount,
                    'freight_option' => $freightQuotation->freight_option,
                    'forwarder' => $freightQuotation->forwarder ?? $freightQuotation->freight_mode ?? null,
                    'prepared_by' => auth()->id(),
                    'remarks' => 'Created from Freight Quotation #' . $freightQuotation->quote_number,
                ]);

                // Create SO items
                foreach ($validated['items'] as $item) {
                    $gross = $item['quantity'] * $item['price'];
                    $discVal = (float) ($item['discount_value'] ?? 0);
                    $discType = $item['discount_type'] ?? 'percentage';
                    if ($discType === 'percentage') {
                        $discAmount = $gross * ($discVal / 100);
                    } else {
                        $discAmount = $discVal;
                    }
                    $subtotal = max(0, $gross - $discAmount);

                    $salesOrder->items()->create([
                        'book_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount_value' => $discVal,
                        'discount_type' => $discType,
                        'discount_amount' => $discAmount,
                        'subtotal' => $subtotal,
                    ]);
                }

                // Link freight quotation to SO
                $freightQuotation->update([
                    'sales_order_id' => $salesOrder->id,
                    'workflow_status' => 'linked_to_so',
                ]);

                DB::commit();

                if ($isFordQuotation) {
                    return redirect()->route('production.ford.sales-order')
                        ->with('success', 'FORD Foreign Sales Order #' . $soNumber . ' created successfully with freight quotation linked!');
                }

                return redirect()->route('marketing.sales-orders.show', $salesOrder->id)
                    ->with('success', 'Sales Order #' . $soNumber . ' created successfully with freight quotation linked!');

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating SO from quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * View approve/response from logistics
     */
    public function viewLogisticsResponse(FreightQuotation $freightQuotation)
    {
        if ($freightQuotation->workflow_status !== 'approved') {
            return redirect()->route('marketing.freight-quotations.show', $freightQuotation->id)
                ->with('error', 'Logistics has not responded yet');
        }

        return view('marketing.freight-quotations.logistics-response', [
            'title' => 'Logistics Response',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'quotation' => $freightQuotation->load(['respondedBy']),
        ]);
    }

    /**
     * Remove the specified freight quotation from storage.
     */
    public function destroy(FreightQuotation $freightQuotation)
    {
        try {
            if ($freightQuotation->sales_order_id) {
                return redirect()->back()->with('error', 'Cannot delete a freight quotation linked to a Sales Order.');
            }

            $freightQuotation->delete();

            return redirect()->route('marketing.freight-quotations.list')
                ->with('success', 'Freight Quotation deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting freight quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the freight quotation.');
        }
    }
}
