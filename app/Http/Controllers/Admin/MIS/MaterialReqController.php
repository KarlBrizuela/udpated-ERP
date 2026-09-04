<?php

namespace App\Http\Controllers\Admin\MIS;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MaterialReq;
use Illuminate\Http\Request;

class MaterialReqController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'request_date' => 'required|date',
            'request_details' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        // Default status and module for MIS material requests
        $validated['user_id'] = auth()->id();
        $validated['module'] = 'MIS';
        $validated['status'] = 'forwarded to accounting';

        MaterialReq::create($validated);

        return redirect()->back()->with('success', 'Material Request created and forwarded to Accounting successfully');
    }

    public function update(Request $request, $id)
    {
        $materialRequest = MaterialReq::findOrFail($id);
        $user = auth()->user();
        
        $request->validate([
            'status' => 'required|in:to submit,pending approval,Pending Final Approval,forwarded to accounting,received,rejected,pending_supervisor_approval,pending_admin_approval,pending_director_approval',
            'requested_by' => 'sometimes|required|string|max:255',
            'request_date' => 'sometimes|required|date',
            'request_details' => 'sometimes|required|string',
            'amount' => 'sometimes|nullable|numeric|min:0',
            'rejection_reason' => 'nullable|string',
        ]);

        $newStatus = $request->status;
        $oldStatus = $materialRequest->status;

        // APPROVAL WORKFLOW LOGIC
        if ($newStatus !== 'rejected') {
            // 1. Check if user is authorized for current stage
            if ($oldStatus === 'to submit' && $materialRequest->user_id === $user->id) {
                // Authorized as owner to submit
            } else {
                if (!$materialRequest->canBeApprovedBy($user)) {
                    return redirect()->back()->with('error', 'You are not authorized to approve/update this request at its current stage.');
                }
            }

            // 2. Validate status transition (if changing)
            if ($newStatus !== $oldStatus) {
                $expectedNextStatus = $materialRequest->getNextApprovalStatus();
                // Allow direct move to 'completed' or 'received' for flexibility
                if ($newStatus !== $expectedNextStatus && !in_array($newStatus, ['forwarded to accounting', 'received'])) {
                    return redirect()->back()->with('error', 'Invalid status transition for this request.');
                }

                // 3. Prepare approval data (track who approved and when)
                // We record the approver of the OLD status
                $approverField = $materialRequest->getApproverFieldForCurrentStatus();
                $timestampField = $materialRequest->getTimestampFieldForCurrentStatus();

                if ($approverField) $materialRequest->$approverField = $user->id;
                if ($timestampField) $materialRequest->$timestampField = now();
            }
            
            $materialRequest->status = $newStatus;
        } else {
            // REJECTION LOGIC
            $materialRequest->status = 'rejected';
            $materialRequest->rejected_by = $user->id;
            $materialRequest->rejected_at = now();
            if ($request->has('rejection_reason')) {
                $materialRequest->rejection_reason = $request->rejection_reason;
            }
        }

        // 4. Update other fields if present
        if ($request->has('requested_by')) $materialRequest->requested_by = $request->requested_by;
        if ($request->has('request_date')) $materialRequest->request_date = $request->request_date;
        if ($request->has('request_details')) $materialRequest->request_details = $request->request_details;
        if ($request->has('amount')) $materialRequest->amount = $request->amount;

        $materialRequest->save();

        // Send Notification to Director if status is "Pending Final Approval"
        if ($newStatus === 'Pending Final Approval' && $oldStatus !== 'Pending Final Approval') {
            $director = \App\Models\User::where('position', 'Director')->first();
            if ($director) {
                try {
                    $director->notify(new \App\Notifications\DirectorApprovalRequested($materialRequest, 'Material'));
                } catch (\Exception $e) {
                    \Log::error("Failed to send Material approval notification: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'Material Request ' . ($newStatus === 'rejected' ? 'rejected' : 'updated') . ' successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\MIS\MaterialReq  $materialReq
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialReq $materialRequest)
    {
        $materialRequest->delete();

        return redirect()->back()->with('success', 'Material Request deleted successfully');
    }
}
