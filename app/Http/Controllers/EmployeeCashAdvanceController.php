<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCashAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AccountingService;

class EmployeeCashAdvanceController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }
    /**
     * Store a new cash advance request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string',
            'date_needed' => 'required|date|after_or_equal:today',
            'department' => 'required|in:Direct,MIS,GSD',
        ]);

        $user = Auth::user();
        $dept = $request->department;

        // Route status
        if ($dept === 'Direct') {
            $status = 'pending_supervisor_approval';
        } else {
            $status = 'forwarded to accounting';
        }

        \App\Models\Admin\MIS\MaterialReq::create([
            'user_id' => $user->id,
            'module' => $dept,
            'requested_by' => $user->name,
            'request_date' => $request->date_needed,
            'request_details' => $request->purpose,
            'amount' => $request->amount,
            'status' => $status,
        ]);

        return redirect()->back()->with('success', 'Request submitted successfully.');
    }

    /**
     * Update the cash advance request (Submit/Approve/Reject).
     */
    public function update(Request $request, $id)
    {
        $advance = EmployeeCashAdvance::findOrFail($id);
        $user = auth()->user();

        $request->validate([
            'status' => 'required|in:pending_supervisor_approval,pending_admin_approval,pending_director_approval,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $newStatus = $request->status;
        $oldStatus = $advance->status;

        // REJECTION LOGIC
        if ($newStatus === 'rejected') {
            $advance->status = 'rejected';
            $advance->rejected_by = $user->id;
            $advance->rejected_at = now();
            if ($request->has('rejection_reason')) {
                $advance->rejection_reason = $request->rejection_reason;
            }
            $advance->save();
            return redirect()->back()->with('success', 'Cash advance request rejected successfully.');
        }

        // APPROVAL / SUBMISSION LOGIC
        if ($newStatus !== $oldStatus) {
            // 1. Authorization Check
            if ($oldStatus === 'to submit' && $advance->user_id === $user->id) {
                // Owner is submitting
            } else {
                if (!$advance->canBeApprovedBy($user)) {
                    return redirect()->back()->with('error', 'You are not authorized to approve this request at its current stage.');
                }
            }

            // 2. Validate status transition
            $expectedNextStatus = $advance->getNextApprovalStatus();
            // Allow flexibility for intermediate steps if needed
            if ($newStatus !== $expectedNextStatus && !in_array($newStatus, ['forwarded to accounting', 'received'])) {
                return redirect()->back()->with('error', 'Invalid status transition.');
            }

            // 3. Track Approval Data based on CURRENT status (before change)
            $approverField = $advance->getApproverFieldForCurrentStatus();
            $timestampField = $advance->getTimestampFieldForCurrentStatus();

            if ($approverField) {
                $advance->{$approverField} = $user->id;
            }
            if ($timestampField) {
                $advance->{$timestampField} = now();
            }

            // Explicit fallback for Admin Manager stage if mapping failed
            if ($oldStatus === 'pending_admin_approval' && !$advance->approved_by_admin) {
                $advance->approved_by_admin = $user->id;
                $advance->admin_approved_at = now();
            }

            $advance->status = $newStatus;
            $advance->save();

            // Send Notification to Director if status is "pending_director_approval"
            if ($newStatus === 'pending_director_approval' && $oldStatus !== 'pending_director_approval') {
                $director = \App\Models\User::where('position', 'Director')->first();
                if ($director) {
                    try {
                        $director->notify(new \App\Notifications\DirectorApprovalRequested($advance, 'Cash Advance'));
                    } catch (\Exception $e) {
                        \Log::error("Failed to send Cash Advance approval notification: " . $e->getMessage());
                    }
                }
            }

            // --- ACCOUNTING INTEGRATION ---
            if ($newStatus === 'approved') {
                $this->accounting->postCashAdvanceDisbursement($advance);
            }

            return redirect()->back()->with('success', 'Cash advance status updated to ' . $newStatus . '.');
        }

        return redirect()->back()->with('info', 'No changes made to the request.');
    }
}
