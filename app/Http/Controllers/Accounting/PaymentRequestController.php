<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentRequestController extends Controller
{
    /**
     * Display a listing of approved payment requests for Accounting.
     */
    public function index()
    {
        // Only show requests that are approved, scheduled, or paid
        $requests = PaymentRequest::with(['requester', 'directorApprovedBy', 'adminApprovedBy', 'financeApprovedBy'])
            ->whereIn('status', ['approved', 'scheduled', 'paid'])
            ->latest()
            ->get();

        return view('admin-finance.accounting.payment-requests.index', [
            'title' => 'Payment Requests Processing',
            'role' => auth()->user()->position,
            'sidebar' => 'admin-finance',
            'requests' => $requests
        ]);
    }

    /**
     * Store a newly created payment request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'payment_to' => 'required|string|max:255',
            'payment_for' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'po_number' => 'nullable|string|max:255',
            'item_receipt' => 'nullable|string|max:255',
            'attachment_file' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg',
            'particulars' => 'required|array|min:1',
            'particulars.*' => 'required|string',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0',
        ]);

        $totalAmount = array_sum($request->amount);

        // Upload attachment if any
        $attachmentPath = null;
        if ($request->hasFile('attachment_file')) {
            $file = $request->file('attachment_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('payment_requests', $filename, 'public');
        }

        // Create Header
        $paymentRequest = PaymentRequest::create([
            'requester_id' => Auth::id(),
            'date' => $request->date,
            'payment_to' => $request->payment_to,
            'payment_for' => $request->payment_for,
            'due_date' => $request->due_date,
            'po_number' => $request->po_number,
            'item_receipt' => $request->item_receipt,
            'total_amount' => $totalAmount,
            'attachment_path' => $attachmentPath,
            'status' => 'pending_director_approval', // First stage
        ]);

        // Create Items
        foreach ($request->particulars as $index => $particularsVal) {
            if (!empty($particularsVal)) {
                PaymentRequestItem::create([
                    'payment_request_id' => $paymentRequest->id,
                    'item_date' => isset($request->payment_date[$index]) ? $request->payment_date[$index] : null,
                    'ref_no' => isset($request->ref_no[$index]) ? $request->ref_no[$index] : null,
                    'particulars' => $particularsVal,
                    'amount' => $request->amount[$index],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Payment request #' . $paymentRequest->id . ' has been created and submitted to the Director for approval.');
    }

    /**
     * Display the specified payment request details.
     */
    public function show($id)
    {
        $request = PaymentRequest::with(['items', 'requester', 'directorApprovedBy', 'adminApprovedBy', 'financeApprovedBy', 'rejector'])->findOrFail($id);
        $user = auth()->user();

        // Determine if current user can approve/reject
        $canApprove = $request->canBeApprovedBy($user);
        $canApproveAsAdmin = $request->canApproveAsAdmin($user);
        $canApproveAsFinance = $request->canApproveAsFinance($user);

        // Sidebar choice
        $sidebar = 'production';
        if (str_contains(strtolower($user->division), 'admin') || str_contains(strtolower($user->division), 'finance') || $user->isSuperAdmin()) {
            $sidebar = 'admin-finance';
        }
        if ($user->position === 'Director') {
            $sidebar = 'director';
        }

        return view('production.ford.payment-requests.show', [
            'title' => 'Payment Request #' . str_pad($request->id, 5, '0', STR_PAD_LEFT),
            'role' => $user->position,
            'sidebar' => $sidebar,
            'paymentRequest' => $request,
            'canApprove' => $canApprove,
            'canApproveAsAdmin' => $canApproveAsAdmin,
            'canApproveAsFinance' => $canApproveAsFinance
        ]);
    }

    /**
     * Approve the payment request for the current stage.
     */
    public function approve(Request $request, $id)
    {
        $paymentRequest = PaymentRequest::findOrFail($id);
        $user = auth()->user();

        if (!$paymentRequest->canBeApprovedBy($user)) {
            return redirect()->back()->with('error', 'You are not authorized to approve this request at this stage.');
        }

        $type = $request->input('approval_type'); // 'director', 'admin', 'finance'
        
        if ($paymentRequest->status === 'pending_director_approval') {
            $paymentRequest->director_approved_by = $user->id;
            $paymentRequest->director_approved_at = now();
            $paymentRequest->status = 'pending_admin_finance_approval';
            $paymentRequest->save();
            
            return redirect()->back()->with('success', 'Payment request approved by Director and forwarded to Admin & Finance Managers.');
        }

        if ($paymentRequest->status === 'pending_admin_finance_approval') {
            if ($type === 'both' && $paymentRequest->canApproveAsAdmin($user) && $paymentRequest->canApproveAsFinance($user)) {
                $paymentRequest->admin_approved_by = $user->id;
                $paymentRequest->admin_approved_at = now();
                $paymentRequest->finance_approved_by = $user->id;
                $paymentRequest->finance_approved_at = now();
            } elseif ($type === 'admin' && $paymentRequest->canApproveAsAdmin($user)) {
                $paymentRequest->admin_approved_by = $user->id;
                $paymentRequest->admin_approved_at = now();
            } elseif ($type === 'finance' && $paymentRequest->canApproveAsFinance($user)) {
                $paymentRequest->finance_approved_by = $user->id;
                $paymentRequest->finance_approved_at = now();
            } else {
                return redirect()->back()->with('error', 'Invalid approval type or unauthorized.');
            }

            // If both are approved, move to approved status
            if ($paymentRequest->admin_approved_by && $paymentRequest->finance_approved_by) {
                $paymentRequest->status = 'approved';
            }
            $paymentRequest->save();

            return redirect()->back()->with('success', 'Approval recorded successfully.');
        }

        return redirect()->back()->with('error', 'Invalid request status.');
    }

    /**
     * Reject the payment request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $paymentRequest = PaymentRequest::findOrFail($id);
        $user = auth()->user();

        if (!$paymentRequest->canBeApprovedBy($user)) {
            return redirect()->back()->with('error', 'You are not authorized to reject this request.');
        }

        $paymentRequest->status = 'rejected';
        $paymentRequest->rejected_by = $user->id;
        $paymentRequest->rejected_at = now();
        $paymentRequest->rejection_reason = $request->rejection_reason;
        $paymentRequest->save();

        return redirect()->back()->with('warning', 'Payment request has been rejected.');
    }

    /**
     * Schedule payment for an approved request (Accounting).
     */
    public function schedule(Request $request, $id)
    {
        $request->validate([
            'scheduled_payment_date' => 'required|date|after_or_equal:today',
            'payment_method' => 'required|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500'
        ]);

        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Only fully approved payment requests can be scheduled.');
        }

        $paymentRequest->scheduled_payment_date = $request->scheduled_payment_date;
        $paymentRequest->payment_method = $request->payment_method;
        $paymentRequest->payment_reference = $request->payment_reference;
        $paymentRequest->remarks = $request->remarks;
        $paymentRequest->status = 'scheduled';
        $paymentRequest->save();

        return redirect()->back()->with('success', 'Payment request has been scheduled for payment on ' . $request->scheduled_payment_date);
    }

    /**
     * Mark a scheduled payment as completed/paid (Accounting).
     */
    public function markAsPaid($id)
    {
        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled payment requests can be marked as paid.');
        }

        $paymentRequest->status = 'paid';
        $paymentRequest->save();

        return redirect()->back()->with('success', 'Payment request #' . $paymentRequest->id . ' has been marked as PAID.');
    }
}
