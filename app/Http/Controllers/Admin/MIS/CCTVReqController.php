<?php

namespace App\Http\Controllers\Admin\MIS;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\CCTVReq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CCTVReqController extends Controller
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
            'department' => 'required|in:Admin,Marketing,Production',
            'requested_by' => 'required|string|max:255',
            'time_of_incident' => 'required',
            'date_of_incident' => 'required|date',
            'purpose' => 'required|string',
            'hardcopy' => 'nullable|boolean',
            'viewing' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = $request->routeIs('admin-finance.mis.cctv-requests.store') ? 'on_hold' : 'to submit';
        $validated['hardcopy'] = $request->boolean('hardcopy');
        $validated['viewing'] = $request->boolean('viewing');
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('cctv_requests', 'public');
        }

        CCTVReq::create($validated);

        return redirect()->back()->with('success', 'CCTV Request created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\MIS\CCTVReq  $cctvReq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cctvReq = CCTVReq::findOrFail($id);
        $user = auth()->user();

        $request->validate([
            'status' => 'required|string',
            'department' => 'sometimes|required|in:Admin,Marketing,Production',
            'requested_by' => 'sometimes|required|string|max:255',
            'time_of_incident' => 'sometimes|required',
            'date_of_incident' => 'sometimes|required|date',
            'purpose' => 'sometimes|required|string',
            'rejection_reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $newStatus = $request->status;
        $oldStatus = $cctvReq->status;

        // APPROVAL WORKFLOW LOGIC
        if ($newStatus !== 'rejected') {
            // 1. Check if user is authorized for current stage
            if ($oldStatus === 'to submit' && $cctvReq->user_id === $user->id) {
                // Authorized as owner to submit
            } else {
                if (!$cctvReq->canBeApprovedBy($user)) {
                    return redirect()->back()->with('error', 'You are not authorized to approve/update this request at its current stage.');
                }
            }

            // 2. Validate status transition (if changing)
            if ($newStatus !== $oldStatus) {
                $expectedNextStatus = $cctvReq->getNextApprovalStatus();
                // Allow direct completion for existing admin workflows.
                if ($newStatus !== $expectedNextStatus && $newStatus !== 'completed') {
                    return redirect()->back()->with('error', 'Invalid status transition for this request.');
                }

                // 3. Prepare approval data (track who approved and when)
                // We record the approver of the OLD status
                $approverField = $cctvReq->getApproverFieldForCurrentStatus();
                $timestampField = $cctvReq->getTimestampFieldForCurrentStatus();

                if ($approverField) $cctvReq->$approverField = $user->id;
                if ($timestampField) $cctvReq->$timestampField = now();
            }
            
            $cctvReq->status = $newStatus;
        } else {
            // REJECTION LOGIC
            $cctvReq->status = 'rejected';
            $cctvReq->rejected_by = $user->id;
            $cctvReq->rejected_at = now();
            if ($request->has('rejection_reason')) {
                $cctvReq->rejection_reason = $request->rejection_reason;
            }
        }

        // 4. Update other fields if present in request
        if ($request->has('department')) $cctvReq->department = $request->department;
        if ($request->has('requested_by')) $cctvReq->requested_by = $request->requested_by;
        if ($request->has('time_of_incident')) $cctvReq->time_of_incident = $request->time_of_incident;
        if ($request->has('date_of_incident')) $cctvReq->date_of_incident = $request->date_of_incident;
        if ($request->has('purpose')) {
            $cctvReq->purpose = $request->purpose;
            // Only update booleans if we are in a full edit context (indicated by presence of other fields like purpose)
            // This prevents status-only updates (like from approval queue) from resetting these to false
            $cctvReq->hardcopy = $request->boolean('hardcopy');
            $cctvReq->viewing = $request->boolean('viewing');
        }
        if ($request->hasFile('attachment')) {
            if ($cctvReq->attachment) {
                Storage::disk('public')->delete($cctvReq->attachment);
            }
            $cctvReq->attachment = $request->file('attachment')->store('cctv_requests', 'public');
        }

        $cctvReq->save();

        // Send Notification to Director if status is "Pending Final Approval"
        if ($newStatus === 'Pending Final Approval' && $oldStatus !== 'Pending Final Approval') {
            $director = \App\Models\User::where('position', 'Director')->first();
            if ($director) {
                try {
                    $director->notify(new \App\Notifications\DirectorApprovalRequested($cctvReq, 'CCTV'));
                } catch (\Exception $e) {
                    // Silently fail or log if mailer is not configured
                    \Log::error("Failed to send CCTV approval notification: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'CCTV Request ' . ($newStatus === 'rejected' ? 'rejected' : 'updated') . ' successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\MIS\CCTVReq  $cctvReq
     * @return \Illuminate\Http\Response
     */
    public function destroy(CCTVReq $cctvRequest)
    {
        $cctvRequest->delete();

        return redirect()->back()->with('success', 'CCTV Request deleted successfully');
    }
}
