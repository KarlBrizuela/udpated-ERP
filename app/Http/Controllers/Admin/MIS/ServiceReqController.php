<?php

namespace App\Http\Controllers\Admin\MIS;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MisServiceRequest;
use Illuminate\Http\Request;

class ServiceReqController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requestor_name' => 'required|string|max:255',
            'date' => 'required|date',
            'nature_of_request' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        MisServiceRequest::create($validated);

        return redirect()->back()->with('success', 'Service Request submitted successfully');
    }

    public function update(Request $request, MisServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'requestor_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'nature_of_request' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:to submit,pending,pending approval,Pending HR approval,Pending Final Approval,approved,on_hold,ongoing,rejected,completed',
        ]);

        $serviceRequest->update($validated);

        if ($serviceRequest->status === 'Pending Final Approval') {
            $director = \App\Models\User::where('position', 'Director')->first();
            if ($director) {
                try {
                    $director->notify(new \App\Notifications\DirectorApprovalRequested($serviceRequest, 'Service'));
                } catch (\Exception $e) {
                    \Log::error("Failed to send Service approval notification: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'Service Request updated successfully');
    }

    public function destroy(MisServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();

        return redirect()->back()->with('success', 'Service Request deleted successfully');
    }
}
