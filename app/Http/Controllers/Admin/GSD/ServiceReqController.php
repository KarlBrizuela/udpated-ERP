<?php

namespace App\Http\Controllers\Admin\GSD;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MisServiceRequest;
use Illuminate\Http\Request;

class ServiceReqController extends Controller
{
    /**
     * Store a newly created service request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requestor_name' => 'required|string|max:255',
            'date' => 'required|date',
            'nature_of_request' => 'required|string',
        ]);

        // Default status for GSD service requests - starts as 'to submit'
        $validated['user_id'] = auth()->id();
        $validated['module'] = 'GSD';
        $validated['status'] = 'to submit';

        MisServiceRequest::create($validated);

        return redirect()->back()->with('success', 'Service Request created successfully. Submit for approval to add to approval queue.');
    }

    /**
     * Submit the specified service request for approval.
     * Transitions status from 'to submit' to 'pending'
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function submit($id)
    {
        $serviceRequest = MisServiceRequest::findOrFail($id);
        
        if ($serviceRequest->status !== 'to submit') {
            return redirect()->back()->with('warning', 'Only requests in "to submit" status can be submitted.');
        }
        
        $serviceRequest->update(['status' => 'pending']);
        
        return redirect()->back()->with('success', 'Service Request submitted for approval.');
    }

    /**
     * Update the specified service request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $serviceRequest = MisServiceRequest::findOrFail($id);
        
        $validated = $request->validate([
            'requestor_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'nature_of_request' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:to submit,pending,Pending HR approval,Pending Final Approval,on_hold,ongoing,completed,rejected',
        ]);

        $serviceRequest->update($validated);

        return redirect()->back()->with('success', 'Service Request updated successfully');
    }

    /**
     * Remove the specified service request from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $serviceRequest = MisServiceRequest::findOrFail($id);
        $serviceRequest->delete();

        return redirect()->back()->with('success', 'Service Request deleted successfully');
    }
}
