<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Customer::whereNotIn('customer_name', ['Lazada', 'Shopee', 'TikTok']);

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(customer_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(first_name, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(last_name, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(mobile, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(main_phone, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(work_phone, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(main_email, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(account_number, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(rep, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(customer_type, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(custom_contact_person, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(shipping_address, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(billing_address, "")) LIKE ?', ["%{$search}%"]);
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->orderBy('customer_id', 'desc')->paginate(15)->withQueryString();

        return view('marketing.customers', [
            'customers' => $customers,
            'title' => 'Customer Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Not needed for modal-based creation
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Smart defaulting before validation to satisfy 'required' rules
        if (!$request->has('mobile') || empty($request->mobile)) {
            $request->merge(['mobile' => 'N/A']);
        }
        if (!$request->has('billing_address') || empty($request->billing_address)) {
            $request->merge(['billing_address' => 'N/A']);
        }
        if (!$request->has('shipping_address') || empty($request->shipping_address)) {
            $request->merge(['shipping_address' => $request->billing_address ?? 'N/A']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|unique:customers,account_number',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_date' => 'nullable|date',
            'currency_code' => 'nullable|string|max:20',
            'customer_type' => 'nullable|string|max:100',
            'rep' => 'nullable|string|max:100',
            'class' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:10',
            'first_name' => 'nullable|string|max:100',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'main_phone' => 'nullable|string',
            'home_phone' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'mobile' => 'required|string',
            'fax' => 'nullable|string',
            'main_email' => 'nullable|email',
            'cc_email' => 'nullable|email',
            'website' => 'nullable|string',
            'other_contact' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'billing_address_line1' => 'nullable|string|max:255',
            'billing_address_line2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_province' => 'nullable|string|max:100',
            'billing_country' => 'nullable|string|max:100',
            'shipping_address' => 'required|string',
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_province' => 'nullable|string|max:100',
            'shipping_country' => 'nullable|string|max:100',
            'is_default_shipping' => 'nullable|boolean',
            'payment_terms' => 'nullable|in:Net 15,Net 30,Net 60,Due on receipt',
            'preferred_delivery_method' => 'nullable|in:Lazada,Shopee,Main Warehouse',
            'preferred_payment_method' => 'nullable|in:check,cash',
            'credit_limit' => 'nullable|numeric',
            'price_level' => 'nullable|in:standard,wholesale',
            'card_number_last4' => 'nullable|string|max:4',
            'card_exp_month' => 'nullable|string|max:2',
            'card_exp_year' => 'nullable|string|max:4',
            'card_name' => 'nullable|string',
            'card_billing_address' => 'nullable|string',
            'card_zip' => 'nullable|string|max:20',
            'custom_contact_person' => 'nullable|string',
            'custom_customer_field' => 'nullable|string',
            'representatives' => 'nullable|array',
            'is_inactive' => 'nullable|boolean',
        ]);

        // Smart fill individual address components if passed or parsed
        $parseAddr = function($addrStr) {
            if (!$addrStr || $addrStr === 'N/A') return ['', '', '', '', 'Philippines'];
            if (str_contains($addrStr, '|')) {
                $p = array_map('trim', explode('|', $addrStr));
                return [$p[0] ?? '', $p[1] ?? '', $p[2] ?? '', $p[3] ?? '', $p[4] ?? 'Philippines'];
            }
            $parts = array_values(array_filter(array_map('trim', explode(',', $addrStr))));
            $len = count($parts);
            if ($len >= 5) return [$parts[0], $parts[1], $parts[2], $parts[3], $parts[4]];
            elseif ($len == 4) return [$parts[0], '', $parts[1], $parts[2], $parts[3]];
            elseif ($len == 3) return [$parts[0], '', $parts[1], '', $parts[2]];
            elseif ($len == 2) return [$parts[0], '', $parts[1], '', 'Philippines'];
            return [$parts[0] ?? '', '', '', '', 'Philippines'];
        };

        if (empty($validated['billing_city']) && !empty($validated['billing_address'])) {
            list($b1, $b2, $bCity, $bProv, $bCoun) = $parseAddr($validated['billing_address']);
            $validated['billing_address_line1'] = $validated['billing_address_line1'] ?? ($b1 ?: null);
            $validated['billing_address_line2'] = $validated['billing_address_line2'] ?? ($b2 ?: null);
            $validated['billing_city']          = $validated['billing_city'] ?? ($bCity ?: null);
            $validated['billing_province']      = $validated['billing_province'] ?? ($bProv ?: null);
            $validated['billing_country']       = $validated['billing_country'] ?? ($bCoun ?: 'Philippines');
        }

        if (empty($validated['shipping_city']) && !empty($validated['shipping_address'])) {
            list($s1, $s2, $sCity, $sProv, $sCoun) = $parseAddr($validated['shipping_address']);
            $validated['shipping_address_line1'] = $validated['shipping_address_line1'] ?? ($s1 ?: null);
            $validated['shipping_address_line2'] = $validated['shipping_address_line2'] ?? ($s2 ?: null);
            $validated['shipping_city']          = $validated['shipping_city'] ?? ($sCity ?: null);
            $validated['shipping_province']      = $validated['shipping_province'] ?? ($sProv ?: null);
            $validated['shipping_country']       = $validated['shipping_country'] ?? ($sCoun ?: 'Philippines');
        }

        // Provide defaults for mandatory DB fields missing in quick registration
        if (empty($validated['company_name'])) {
            $validated['company_name'] = 'Individual';
        }
        if (empty($validated['account_number'])) {
            $validated['account_number'] = 'CUST-' . strtoupper(uniqid());
        }
        


        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['manual_status'] = 'good'; // Automatically set new customers as good

        $customer = Customer::create($validated);

        return response()->json([
            'message' => 'Customer created successfully',
            'customer' => $customer
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        // Super Admin or Marketing with permission can edit customers
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can edit customers.'], 403);
        }

        // Smart defaulting before validation
        if (!$request->has('mobile') || empty($request->mobile)) {
            $request->merge(['mobile' => 'N/A']);
        }
        if (!$request->has('billing_address') || empty($request->billing_address)) {
            $request->merge(['billing_address' => 'N/A']);
        }
        if (!$request->has('shipping_address') || empty($request->shipping_address)) {
            $request->merge(['shipping_address' => $request->billing_address ?? 'N/A']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|unique:customers,account_number,' . $customer->customer_id . ',customer_id',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_date' => 'nullable|date',
            'currency_code' => 'nullable|string|max:20',
            'customer_type' => 'nullable|string|max:100',
            'rep' => 'nullable|string|max:100',
            'class' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:10',
            'first_name' => 'nullable|string|max:100',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'main_phone' => 'nullable|string',
            'home_phone' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'mobile' => 'required|string',
            'fax' => 'nullable|string',
            'main_email' => 'nullable|email',
            'cc_email' => 'nullable|email',
            'website' => 'nullable|string',
            'other_contact' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'billing_address_line1' => 'nullable|string|max:255',
            'billing_address_line2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_province' => 'nullable|string|max:100',
            'billing_country' => 'nullable|string|max:100',
            'shipping_address' => 'required|string',
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_province' => 'nullable|string|max:100',
            'shipping_country' => 'nullable|string|max:100',
            'is_default_shipping' => 'nullable|boolean',
            'payment_terms' => 'nullable|in:Net 15,Net 30,Net 60,Due on receipt',
            'preferred_delivery_method' => 'nullable|in:Lazada,Shopee,Main Warehouse',
            'preferred_payment_method' => 'nullable|in:check,cash',
            'credit_limit' => 'nullable|numeric',
            'price_level' => 'nullable|in:standard,wholesale',
            'card_number_last4' => 'nullable|string|max:4',
            'card_exp_month' => 'nullable|string|max:2',
            'card_exp_year' => 'nullable|string|max:4',
            'card_name' => 'nullable|string',
            'card_billing_address' => 'nullable|string',
            'card_zip' => 'nullable|string|max:20',
            'custom_contact_person' => 'nullable|string',
            'custom_customer_field' => 'nullable|string',
            'representatives' => 'nullable|array',
            'is_inactive' => 'nullable|boolean',
        ]);

        $parseAddr = function($addrStr) {
            if (!$addrStr || $addrStr === 'N/A') return ['', '', '', '', 'Philippines'];
            if (str_contains($addrStr, '|')) {
                $p = array_map('trim', explode('|', $addrStr));
                return [$p[0] ?? '', $p[1] ?? '', $p[2] ?? '', $p[3] ?? '', $p[4] ?? 'Philippines'];
            }
            $parts = array_values(array_filter(array_map('trim', explode(',', $addrStr))));
            $len = count($parts);
            if ($len >= 5) return [$parts[0], $parts[1], $parts[2], $parts[3], $parts[4]];
            elseif ($len == 4) return [$parts[0], '', $parts[1], $parts[2], $parts[3]];
            elseif ($len == 3) return [$parts[0], '', $parts[1], '', $parts[2]];
            elseif ($len == 2) return [$parts[0], '', $parts[1], '', 'Philippines'];
            return [$parts[0] ?? '', '', '', '', 'Philippines'];
        };

        if (empty($validated['billing_city']) && !empty($validated['billing_address'])) {
            list($b1, $b2, $bCity, $bProv, $bCoun) = $parseAddr($validated['billing_address']);
            $validated['billing_address_line1'] = $validated['billing_address_line1'] ?? ($b1 ?: null);
            $validated['billing_address_line2'] = $validated['billing_address_line2'] ?? ($b2 ?: null);
            $validated['billing_city']          = $validated['billing_city'] ?? ($bCity ?: null);
            $validated['billing_province']      = $validated['billing_province'] ?? ($bProv ?: null);
            $validated['billing_country']       = $validated['billing_country'] ?? ($bCoun ?: 'Philippines');
        }

        if (empty($validated['shipping_city']) && !empty($validated['shipping_address'])) {
            list($s1, $s2, $sCity, $sProv, $sCoun) = $parseAddr($validated['shipping_address']);
            $validated['shipping_address_line1'] = $validated['shipping_address_line1'] ?? ($s1 ?: null);
            $validated['shipping_address_line2'] = $validated['shipping_address_line2'] ?? ($s2 ?: null);
            $validated['shipping_city']          = $validated['shipping_city'] ?? ($sCity ?: null);
            $validated['shipping_province']      = $validated['shipping_province'] ?? ($sProv ?: null);
            $validated['shipping_country']       = $validated['shipping_country'] ?? ($sCoun ?: 'Philippines');
        }

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;

        $customer->update($validated);

        return response()->json(['message' => 'Customer updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        // Super Admin or Marketing with permission can delete customers
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can delete customers.'], 403);
        }
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }

    /**
     * Remove multiple customers from storage.
     */
    public function destroyBatch(Request $request)
    {
        // Super Admin or Marketing with permission can delete customers
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can delete customers.'], 403);
        }

        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,customer_id',
        ]);

        $deletedCount = Customer::whereIn('customer_id', $validated['customer_ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} customer(s)."
        ]);
    }

    /**
     * Get transaction history for a customer
     */
    public function getTransactionHistory(Request $request, Customer $customer)
    {
        $allOrders = $customer->salesOrders()->with(['invoice', 'payments'])->latest()->get();

        // Status Filter
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);
            $allOrders = $allOrders->filter(function($order) use ($statusFilter) {
                $effectiveStatus = $order->computed_payment_status;

                if ($statusFilter === 'paid') {
                    return $effectiveStatus === 'paid';
                } elseif ($statusFilter === 'unpaid') {
                    return $effectiveStatus === 'unpaid';
                } elseif ($statusFilter === 'partially_paid') {
                    return $effectiveStatus === 'partially_paid';
                } elseif ($statusFilter === 'completed') {
                    return $order->status === 'completed';
                } elseif ($statusFilter === 'overdue') {
                    return $effectiveStatus !== 'paid' && $order->due_date && $order->due_date->isPast();
                } elseif ($statusFilter === 'cancelled') {
                    return $order->status === 'cancelled';
                }
                return true;
            })->values();
        }

        // Search Filter (SO Number, Ref Number, or SI Number)
        if ($request->filled('search')) {
            $searchTerm = strtolower(trim($request->search));
            $allOrders = $allOrders->filter(function($order) use ($searchTerm) {
                $soMatch = str_contains(strtolower($order->so_number ?? ''), $searchTerm);
                $refMatch = str_contains(strtolower($order->ref_number ?? ''), $searchTerm);
                $siMatch = $order->invoice && str_contains(strtolower($order->invoice->si_number ?? ''), $searchTerm);
                return $soMatch || $refMatch || $siMatch;
            })->values();
        }

        $perPage = max(1, (int) $request->get('per_page', 10));
        $page = max(1, (int) $request->get('page', 1));

        $total = $allOrders->count();
        $sliced = $allOrders->slice(($page - 1) * $perPage, $perPage)->values();

        $history = $sliced->map(function($order) {
            $totalAmount = (float)($order->total_amount ?? 0);
            $paidAmount = (float)$order->total_paid_amount;
            $remainingBalance = (float)$order->remaining_balance;
            $effectivePaymentStatus = $order->computed_payment_status;
            $isPaid = $effectivePaymentStatus === 'paid';
            $hasProof = !empty($order->proof_of_payment);
            $isOverdue = !$isPaid && $order->due_date && $order->due_date->isPast();

            return [
                'id' => $order->id,
                'so_number' => $order->so_number,
                'si_number' => $order->invoice ? $order->invoice->si_number : null,
                'date' => $order->created_at ? $order->created_at->format('Y-m-d') : 'N/A',
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_balance' => $remainingBalance,
                'payment_status' => $effectivePaymentStatus,
                'raw_payment_status' => $order->payment_status ?? 'unpaid',
                'has_proof_of_payment' => $hasProof,
                'proof_of_payment_url' => $hasProof ? asset('storage/' . $order->proof_of_payment) : null,
                'status' => $order->status ?? 'pending',
                'status_label' => ucfirst(str_replace('_', ' ', $order->status ?? 'pending')),
                'terms' => $order->terms ?? 'COD',
                'due_date' => $order->due_date ? $order->due_date->format('M d, Y') : 'N/A',
                'is_overdue' => $isOverdue,
            ];
        });

        $lastPage = max(1, (int) ceil($total / $perPage));

        return response()->json([
            'customer_name' => $customer->customer_name,
            'balance' => (float)$customer->balance,
            'is_bad_client' => $customer->is_bad_client,
            'manual_status' => $customer->manual_status,
            'history' => $history,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => min($page * $perPage, $total),
            ]
        ]);
    }

    /**
     * Record a partial or full payment for a customer sales order
     */
    public function recordPayment(Request $request, Customer $customer, SalesOrder $salesOrder)
    {
        $remainingBalance = (float) $salesOrder->remaining_balance;

        if ($remainingBalance <= 0) {
            return response()->json(['message' => 'This order is already fully paid.'], 422);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $remainingBalance,
            'payment_method' => 'required|string|in:cash,gcash,maya,bank_transfer,check,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
            'proof_of_payment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $amountPaid = (float) $validated['amount'];

        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('proof_of_payments', 'public');
        }

        // Create Payment record
        Payment::create([
            'customer_id' => $customer->customer_id,
            'sales_order_id' => $salesOrder->id,
            'amount' => $amountPaid,
            'payment_method' => $validated['payment_method'],
            'payment_date' => now()->toDateString(),
            'status' => 'verified',
            'reference_number' => $validated['reference_number'] ?? null,
            'verified_by' => auth()->id(),
            'notes' => $validated['notes'] ?? ("Installment payment for " . $salesOrder->so_number),
            'proof_of_payment' => $proofPath,
        ]);

        // Update sales order proof_of_payment if not set
        if ($proofPath && empty($salesOrder->proof_of_payment)) {
            $salesOrder->update(['proof_of_payment' => $proofPath]);
        }

        // Recalculate remaining balance
        $salesOrder->unsetRelation('payments');
        $newRemaining = (float) $salesOrder->remaining_balance;

        if ($newRemaining <= 0) {
            $salesOrder->update(['payment_status' => 'paid']);
        } else {
            $salesOrder->update(['payment_status' => 'partially_paid']);
        }

        // Log Activity
        if (class_exists('\App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id() ?: 1,
                'action' => 'Payment Recorded',
                'description' => "Recorded payment of ₱" . number_format($amountPaid, 2) . " for SO {$salesOrder->so_number} (Customer: {$customer->customer_name}). Remaining balance: ₱" . number_format($newRemaining, 2),
                'affected_model' => 'SalesOrder',
                'affected_model_id' => $salesOrder->id,
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json([
            'message' => 'Payment recorded successfully!',
            'paid_amount' => $amountPaid,
            'remaining_balance' => $newRemaining,
            'new_payment_status' => $salesOrder->fresh()->computed_payment_status,
            'new_customer_balance' => (float) $customer->fresh()->balance,
        ]);
    }

    /**
     * Change customer manual status (Super Admin only)
     */
    public function updateManualStatus(Request $request, Customer $customer)
    {
        // Super Admin or Marketing with permission can change customer status
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can change customer status.'], 403);
        }

        $validated = $request->validate([
            'manual_status' => 'nullable|in:good,bad',
        ]);

        $customer->update(['manual_status' => $validated['manual_status']]);

        return response()->json(['message' => 'Customer status updated successfully']);
    }

    /**
     * Get payment history breakdown for a customer sales order
     */
    public function getPaymentHistory(Customer $customer, SalesOrder $salesOrder)
    {
        $payments = $salesOrder->payments()->with('verifiedBy')->latest()->get();

        $formattedPayments = $payments->map(function($payment) {
            $hasProof = !empty($payment->proof_of_payment);
            return [
                'id' => $payment->id,
                'date' => $payment->payment_date ? $payment->payment_date->format('M d, Y') : ($payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A'),
                'amount' => (float) $payment->amount,
                'method' => ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'cash')),
                'reference_number' => $payment->reference_number ?: 'N/A',
                'notes' => $payment->notes ?: 'N/A',
                'has_proof' => $hasProof,
                'proof_url' => $hasProof ? asset('storage/' . $payment->proof_of_payment) : null,
                'recorded_by' => $payment->verifiedBy->name ?? 'System',
            ];
        });

        return response()->json([
            'so_number' => $salesOrder->so_number,
            'total_amount' => (float) $salesOrder->total_amount,
            'total_paid' => (float) $salesOrder->total_paid_amount,
            'remaining_balance' => (float) $salesOrder->remaining_balance,
            'payment_status' => $salesOrder->computed_payment_status,
            'payments' => $formattedPayments,
        ]);
    }

    /**
     * Export Customer Excel Template for Import
     */
    public function exportExcel()
    {
        return $this->downloadTemplate();
    }

    /**
     * Download blank Excel template for customer import
     */
    public function downloadTemplate()
    {
        return $this->buildCustomerSpreadsheet(collect([]), 'Customer_Import_Template.xlsx');
    }

    private function buildCustomerSpreadsheet($customers, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customers');

        $headers = [
            'Customer Name*', 'Company Name', 'Opening Balance', 'As Of Date (YYYY-MM-DD)', 'Currency (PHP/USD)',
            'Title', 'First Name', 'Middle Initial', 'Last Name', 'Job Title',
            'Main Phone', 'Work Phone', 'Mobile*', 'Fax', 'Main Email', 'CC Email', 'Website', 'Other Contact',
            'Billing Address Line 1', 'Billing Address Line 2', 'Billing Town/City', 'Billing Province/Region', 'Billing Country',
            'Shipping Address Line 1*', 'Shipping Address Line 2', 'Shipping Town/City*', 'Shipping Province/Region', 'Shipping Country',
            'Payment Terms', 'Preferred Delivery Method', 'Preferred Payment Method',
            'Credit Limit', 'Price Level', 'Card Number Last 4', 'Card Exp Month', 'Card Exp Year', 'Card Name',
            'Card Billing Address', 'Card Zip', 'Customer Type', 'Rep', 'Class', 'Contact Person', 'Custom Customer Field',
            'Status (good/bad)'
        ];

        // Format header row
        foreach ($headers as $index => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $cell = $colLetter . '1';
            $sheet->setCellValue($cell, $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Fill data rows
        $rowNum = 2;
        $parseAddr = function($addrStr) {
            if (!$addrStr || $addrStr === 'N/A') {
                return ['', '', '', '', 'Philippines'];
            }
            if (str_contains($addrStr, '|')) {
                $p = array_map('trim', explode('|', $addrStr));
                return [$p[0] ?? '', $p[1] ?? '', $p[2] ?? '', $p[3] ?? '', $p[4] ?? 'Philippines'];
            }
            $parts = array_values(array_filter(array_map('trim', explode(',', $addrStr))));
            $len = count($parts);
            if ($len >= 5) {
                return [$parts[0], $parts[1], $parts[2], $parts[3], $parts[4]];
            } elseif ($len == 4) {
                return [$parts[0], '', $parts[1], $parts[2], $parts[3]];
            } elseif ($len == 3) {
                return [$parts[0], '', $parts[1], '', $parts[2]];
            } elseif ($len == 2) {
                return [$parts[0], '', $parts[1], '', 'Philippines'];
            }
            return [$parts[0] ?? '', '', '', '', 'Philippines'];
        };

        foreach ($customers as $c) {
            list($pb1, $pb2, $pbCity, $pbProv, $pbCoun) = $parseAddr($c->billing_address ?? '');
            list($ps1, $ps2, $psCity, $psProv, $psCoun) = $parseAddr($c->shipping_address ?? '');

            $b1 = $c->billing_address_line1 ?: $pb1;
            $b2 = $c->billing_address_line2 ?: $pb2;
            $bCity = $c->billing_city ?: $pbCity;
            $bProv = $c->billing_province ?: $pbProv;
            $bCoun = $c->billing_country ?: $pbCoun;

            $s1 = $c->shipping_address_line1 ?: $ps1;
            $s2 = $c->shipping_address_line2 ?: $ps2;
            $sCity = $c->shipping_city ?: $psCity;
            $sProv = $c->shipping_province ?: $psProv;
            $sCoun = $c->shipping_country ?: $psCoun;

            $data = [
                $c->customer_name ?? '',
                $c->company_name ?? '',
                (float) ($c->opening_balance ?? 0),
                $c->opening_balance_date ? date('Y-m-d', strtotime($c->opening_balance_date)) : '',
                $c->currency_code ?? 'PHP',
                $c->title ?? '',
                $c->first_name ?? '',
                $c->middle_initial ?? '',
                $c->last_name ?? '',
                $c->job_title ?? '',
                $c->main_phone ?? '',
                $c->work_phone ?? '',
                $c->mobile ?? '',
                $c->fax ?? '',
                $c->main_email ?? '',
                $c->cc_email ?? '',
                $c->website ?? '',
                $c->other_contact ?? '',
                $b1,
                $b2,
                $bCity,
                $bProv,
                $bCoun,
                $s1,
                $s2,
                $sCity,
                $sProv,
                $sCoun,
                $c->payment_terms ?? '',
                $c->preferred_delivery_method ?? '',
                $c->preferred_payment_method ?? '',
                (float) ($c->credit_limit ?? 0),
                $c->price_level ?? '',
                $c->card_number_last4 ?? '',
                $c->card_exp_month ?? '',
                $c->card_exp_year ?? '',
                $c->card_name ?? '',
                $c->card_billing_address ?? '',
                $c->card_zip ?? '',
                $c->customer_type ?? '',
                $c->rep ?? '',
                $c->class ?? '',
                $c->custom_contact_person ?? '',
                $c->custom_customer_field ?? '',
                $c->manual_status ?? 'good'
            ];

            foreach ($data as $colIndex => $val) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($colLetter . $rowNum, $val);
            }
            $rowNum++;
        }

        // Add Data Validation Dropdowns for columns (rows 2 to 250)
        $existingTypes = Customer::whereNotNull('customer_type')->where('customer_type', '!=', '')->distinct()->pluck('customer_type')->toArray();
        $typesList = array_values(array_unique(array_merge(['TEAM A', 'TEAM B', 'TEAM C'], $existingTypes)));

        $existingReps = Customer::whereNotNull('rep')->where('rep', '!=', '')->distinct()->pluck('rep')->toArray();
        $repsList = array_values(array_unique(array_merge(['CLE', 'MKT'], $existingReps)));

        $existingClasses = Customer::whereNotNull('class')->where('class', '!=', '')->distinct()->pluck('class')->toArray();
        $classesList = array_values(array_unique(array_merge(['LAG', 'MNL'], $existingClasses)));

        $addDropdownValidation = function($sheet, $colLetter, array $options, $maxRow = 250) {
            if (empty($options)) return;
            $formula = '"' . implode(',', $options) . '"';
            for ($r = 2; $r <= $maxRow; $r++) {
                $validation = $sheet->getCell($colLetter . $r)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Select Option');
                $validation->setError('Please select a valid option from the dropdown list.');
                $validation->setFormula1($formula);
            }
        };

        // E = Currency, AC = Terms, AD = Delivery Method, AE = Payment Method, AG = Price Level, AN = Type, AO = Rep, AP = Class, AS = Status
        $addDropdownValidation($sheet, 'E', ['PHP', 'USD']);
        $addDropdownValidation($sheet, 'AC', ['Net 15', 'Net 30', 'Net 60', 'Due on receipt']);
        $addDropdownValidation($sheet, 'AD', ['Main Warehouse', 'Lazada', 'Shopee']);
        $addDropdownValidation($sheet, 'AE', ['check', 'cash']);
        $addDropdownValidation($sheet, 'AG', ['standard', 'wholesale']);
        $addDropdownValidation($sheet, 'AN', $typesList);
        $addDropdownValidation($sheet, 'AO', $repsList);
        $addDropdownValidation($sheet, 'AP', $classesList);
        $addDropdownValidation($sheet, 'AS', ['good', 'bad']);

        // Auto-fit column widths
        foreach (range(1, count($headers)) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import multiple customers from Excel / CSV
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv', 'txt'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file format. Please upload an Excel (.xlsx, .xls) or CSV (.csv) file.'
            ], 422);
        }
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get raw array with cell reference keys ('A', 'B'...)
            $rows = $sheet->toArray(null, true, false, true);

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file is empty.'
                ], 422);
            }

            // Remove leading completely empty rows
            foreach ($rows as $rKey => $rowCells) {
                $hasVal = false;
                foreach ($rowCells as $v) {
                    if ($v !== null && trim((string)$v) !== '') {
                        $hasVal = true;
                        break;
                    }
                }
                if (!$hasVal) {
                    unset($rows[$rKey]);
                } else {
                    break;
                }
            }

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file contains no data.'
                ], 422);
            }

            // Inspect the first non-empty row to determine if it is a Header row
            $firstRowKey = array_key_first($rows);
            $firstRowCells = $rows[$firstRowKey];
            
            $headerMap = [];
            $isHeaderRow = false;

            foreach ($firstRowCells as $colKey => $cellText) {
                if ($cellText !== null && trim((string)$cellText) !== '') {
                    $cleanVal = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$cellText));
                    if (in_array($cleanVal, ['customername', 'customername', 'customer', 'companyname', 'company', 'openingbalance', 'mobile', 'shippingaddress', 'currency'])) {
                        $isHeaderRow = true;
                    }
                    $headerMap[$cleanVal] = $colKey;
                }
            }

            // If first row is indeed a header row, remove it from data rows
            if ($isHeaderRow) {
                unset($rows[$firstRowKey]);
            }

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file only contains header text. Please fill out customer data rows under the header before importing.'
                ], 422);
            }

            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];
            $seenCustomerNames = [];

            // Helper to fetch cell value by header keyword, column letter, or numeric index
            $getVal = function($row, array $headerKeywords, $letterKey, $numericIdx) use ($headerMap, $isHeaderRow) {
                if ($isHeaderRow) {
                    foreach ($headerKeywords as $kw) {
                        if (isset($headerMap[$kw])) {
                            $key = $headerMap[$kw];
                            if (isset($row[$key]) && trim((string)$row[$key]) !== '') {
                                return trim((string)$row[$key]);
                            }
                        }
                    }
                }
                if (isset($row[$letterKey]) && trim((string)$row[$letterKey]) !== '') {
                    return trim((string)$row[$letterKey]);
                }
                // Fallback to numeric key
                $values = array_values($row);
                if (isset($values[$numericIdx]) && trim((string)$values[$numericIdx]) !== '') {
                    return trim((string)$values[$numericIdx]);
                }
                return '';
            };

            $rowCounter = $isHeaderRow ? 1 : 0;

            foreach ($rows as $rowIndex => $row) {
                $rowCounter++;

                $custName = $getVal($row, ['customername', 'name', 'customer'], 'A', 0);

                if (empty($custName)) {
                    // Check if entire row is empty
                    $hasContent = false;
                    foreach ($row as $val) {
                        if ($val !== null && trim((string)$val) !== '') {
                            $hasContent = true;
                            break;
                        }
                    }
                    if (!$hasContent) {
                        continue; // Silently skip completely blank rows
                    }

                    $skippedCount++;
                    $errors[] = "Row #{$rowCounter}: Customer Name is missing or empty.";
                    continue;
                }

                $cleanNameLower = strtolower(trim($custName));
                // Duplicate check against database and within uploaded file batch
                if (in_array($cleanNameLower, $seenCustomerNames) || Customer::whereRaw('LOWER(customer_name) = ?', [$cleanNameLower])->exists()) {
                    $skippedCount++;
                    $errors[] = "Row #{$rowCounter} ({$custName}): Customer name already exists in the customer list.";
                    continue;
                }
                $seenCustomerNames[] = $cleanNameLower;

                $companyName = $getVal($row, ['companyname', 'company'], 'B', 1) ?: 'Individual';
                $openingBalStr = $getVal($row, ['openingbalance', 'balance'], 'C', 2);
                $openingBal = is_numeric($openingBalStr) ? (float)$openingBalStr : 0.0;

                $asOfDateRaw = $getVal($row, ['asofdateyyyymmdd', 'asofdate', 'asof', 'date'], 'D', 3);
                $asOfDate = null;
                if (!empty($asOfDateRaw)) {
                    if (is_numeric($asOfDateRaw)) {
                        try {
                            $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$asOfDateRaw);
                            $asOfDate = $dt->format('Y-m-d');
                        } catch (\Exception $e) {
                            $asOfDate = null;
                        }
                    } else {
                        $timestamp = strtotime($asOfDateRaw);
                        if ($timestamp !== false) {
                            $asOfDate = date('Y-m-d', $timestamp);
                        }
                    }
                }

                $currencyCode = strtoupper($getVal($row, ['currency', 'currencycode'], 'E', 4)) ?: 'PHP';
                if (!in_array($currencyCode, ['PHP', 'USD'])) {
                    $currencyCode = str_contains($currencyCode, 'USD') ? 'USD' : 'PHP';
                }

                $title = $getVal($row, ['title'], 'F', 5);
                $firstName = $getVal($row, ['firstname', 'first'], 'G', 6);
                $middleInitial = $getVal($row, ['middleinitial', 'mi', 'middle'], 'H', 7);
                $lastName = $getVal($row, ['lastname', 'last'], 'I', 8);
                $jobTitle = $getVal($row, ['jobtitle', 'job'], 'J', 9);
                $mainPhone = $getVal($row, ['mainphone', 'phone'], 'K', 10);
                $workPhone = $getVal($row, ['workphone'], 'L', 11);
                $mobile = $getVal($row, ['mobile', 'mobilephone', 'cellphone'], 'M', 12) ?: 'N/A';
                $fax = $getVal($row, ['fax'], 'N', 13);
                $mainEmail = $getVal($row, ['mainemail', 'email'], 'O', 14);
                $ccEmail = $getVal($row, ['ccemail'], 'P', 15);
                $website = $getVal($row, ['website', 'site'], 'Q', 16);
                $otherContact = $getVal($row, ['othercontact'], 'R', 17);
                // Read Form-Style Address Fields
                $bLine1 = $getVal($row, ['billingaddressline1', 'billingline1', 'billaddress1', 'billline1'], 'S', 18);
                $bLine2 = $getVal($row, ['billingaddressline2', 'billingline2', 'billaddress2', 'billline2'], 'T', 19);
                $bCity  = $getVal($row, ['billingtowncity', 'billingcity', 'billcity', 'towncity'], 'U', 20);
                $bProv  = $getVal($row, ['billingprovinceregion', 'billingprovince', 'billprovince', 'provinceregion'], 'V', 21);
                $bCountry = $getVal($row, ['billingcountry', 'billcountry'], 'W', 22) ?: 'Philippines';

                $bFullForm = implode(', ', array_filter([$bLine1, $bLine2, $bCity, $bProv, $bCountry]));
                $billingAddress = $bFullForm ?: ($getVal($row, ['invoicebillingaddress', 'billingaddress', 'invoiceaddress', 'billing'], 'S', 18) ?: 'N/A');

                $sLine1 = $getVal($row, ['shippingaddressline1', 'shippingline1', 'shipaddress1', 'shipline1'], 'X', 23);
                $sLine2 = $getVal($row, ['shippingaddressline2', 'shippingline2', 'shipaddress2', 'shipline2'], 'Y', 24);
                $sCity  = $getVal($row, ['shippingtowncity', 'shippingcity', 'shipcity'], 'Z', 25);
                $sProv  = $getVal($row, ['shippingprovinceregion', 'shippingprovince', 'shipprovince'], 'AA', 26);
                $sCountry = $getVal($row, ['shippingcountry', 'shipcountry'], 'AB', 27) ?: 'Philippines';

                $sFullForm = implode(', ', array_filter([$sLine1, $sLine2, $sCity, $sProv, $sCountry]));
                $shippingAddress = $sFullForm ?: ($getVal($row, ['shippingaddress', 'shipto', 'shipping'], 'T', 19) ?: ($billingAddress !== 'N/A' ? $billingAddress : 'N/A'));

                $paymentTerms = $getVal($row, ['paymentterms', 'terms'], 'AC', 28) ?: 'Net 15';
                $preferredDelivery = $getVal($row, ['preferreddeliverymethod', 'deliverymethod', 'delivery'], 'AD', 29) ?: 'Main Warehouse';
                $preferredPayment = $getVal($row, ['preferredpaymentmethod', 'paymentmethod'], 'AE', 30) ?: 'check';

                $creditLimitStr = $getVal($row, ['creditlimit'], 'AF', 31);
                $creditLimit = is_numeric($creditLimitStr) ? (float)$creditLimitStr : 0.0;

                $priceLevel = strtolower($getVal($row, ['pricelevel'], 'AG', 32)) ?: 'standard';
                $cardNumberLast4 = $getVal($row, ['cardnumberlast4'], 'AH', 33);
                $cardExpMonth = $getVal($row, ['cardexpmonth'], 'AI', 34);
                $cardExpYear = $getVal($row, ['cardexpyear'], 'AJ', 35);
                $cardName = $getVal($row, ['cardname'], 'AK', 36);
                $cardBillingAddr = $getVal($row, ['cardbillingaddress'], 'AL', 37);
                $cardZip = $getVal($row, ['cardzip'], 'AM', 38);
                $custType = $getVal($row, ['customertype', 'type'], 'AN', 39) ?: 'TEAM A';
                $rep = $getVal($row, ['rep'], 'AO', 40) ?: 'CLE';
                $class = $getVal($row, ['class'], 'AP', 41) ?: 'LAG';
                $contactPerson = $getVal($row, ['contactperson'], 'AQ', 42);
                $customCustField = $getVal($row, ['customcustomerfield', 'customfield'], 'AR', 43);
                
                $rawStatus = strtolower($getVal($row, ['statusgoodbad', 'status', 'manualstatus', 'clientstatus'], 'AS', 44));
                $manualStatus = in_array($rawStatus, ['bad', 'bad client']) ? 'bad' : 'good';

                $accountNo = 'CUST-' . strtoupper(uniqid());

                try {
                    Customer::create([
                        'customer_name' => $custName,
                        'company_name' => $companyName,
                        'account_number' => $accountNo,
                        'opening_balance' => $openingBal,
                        'opening_balance_date' => $asOfDate,
                        'currency_code' => $currencyCode,
                        'title' => $title ?: null,
                        'first_name' => $firstName ?: null,
                        'middle_initial' => $middleInitial ?: null,
                        'last_name' => $lastName ?: null,
                        'job_title' => $jobTitle ?: null,
                        'main_phone' => $mainPhone ?: null,
                        'work_phone' => $workPhone ?: null,
                        'mobile' => $mobile,
                        'fax' => $fax ?: null,
                        'main_email' => !empty($mainEmail) && filter_var($mainEmail, FILTER_VALIDATE_EMAIL) ? $mainEmail : null,
                        'cc_email' => !empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL) ? $ccEmail : null,
                        'website' => $website ?: null,
                        'other_contact' => $otherContact ?: null,
                        'billing_address' => $billingAddress,
                        'billing_address_line1' => $bLine1 ?: null,
                        'billing_address_line2' => $bLine2 ?: null,
                        'billing_city' => $bCity ?: null,
                        'billing_province' => $bProv ?: null,
                        'billing_country' => $bCountry ?: 'Philippines',
                        'shipping_address' => $shippingAddress,
                        'shipping_address_line1' => $sLine1 ?: null,
                        'shipping_address_line2' => $sLine2 ?: null,
                        'shipping_city' => $sCity ?: null,
                        'shipping_province' => $sProv ?: null,
                        'shipping_country' => $sCountry ?: 'Philippines',
                        'is_default_shipping' => 1,
                        'payment_terms' => in_array($paymentTerms, ['Net 15', 'Net 30', 'Net 60', 'Due on receipt']) ? $paymentTerms : 'Net 15',
                        'preferred_delivery_method' => in_array($preferredDelivery, ['Lazada', 'Shopee', 'Main Warehouse']) ? $preferredDelivery : 'Main Warehouse',
                        'preferred_payment_method' => in_array(strtolower($preferredPayment), ['check', 'cash']) ? strtolower($preferredPayment) : 'check',
                        'credit_limit' => $creditLimit,
                        'price_level' => in_array(strtolower($priceLevel), ['standard', 'wholesale']) ? strtolower($priceLevel) : 'standard',
                        'card_number_last4' => $cardNumberLast4 ?: null,
                        'card_exp_month' => $cardExpMonth ?: null,
                        'card_exp_year' => $cardExpYear ?: null,
                        'card_name' => $cardName ?: null,
                        'card_billing_address' => $cardBillingAddr ?: null,
                        'card_zip' => $cardZip ?: null,
                        'customer_type' => $custType,
                        'rep' => $rep,
                        'class' => $class,
                        'custom_contact_person' => $contactPerson ?: null,
                        'custom_customer_field' => $customCustField ?: null,
                        'is_inactive' => 0,
                        'manual_status' => $manualStatus,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $skippedCount++;
                    $errors[] = "Row #{$rowCounter} ({$custName}): " . $e->getMessage();
                }
            }

            if ($importedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customers were imported. Please make sure to fill out customer names in your file.',
                    'errors' => $errors
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} new customer(s)." . ($skippedCount > 0 ? " ({$skippedCount} skipped/failed)" : ""),
                'imported_count' => $importedCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }
}
