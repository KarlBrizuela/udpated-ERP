<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MisServiceRequest;
use Illuminate\Http\Request;

class DTOController extends Controller
{
    public function jobRequestForm()
    {
        $departments = \App\Models\Department::all();
        $jobRequests = \App\Models\JobRequest::with('department')->orderBy('created_at', 'desc')->get();
        $serviceRequests = MisServiceRequest::where('department', 'DTO')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('production.dto.job-request-form', compact('departments', 'jobRequests', 'serviceRequests'));
    }

    public function storeJobRequest(Request $request)
    {
        $validated = $request->validate([
            'job_no' => 'nullable|string|max:255',
            'project_title' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'department' => 'nullable|exists:departments,dept_id',
        ]);

        \App\Models\JobRequest::create([
            'job_no' => $validated['job_no'],
            'project_title' => $validated['project_title'],
            'specifications' => $validated['specifications'],
            'due_date' => $validated['due_date'],
            'date' => $validated['date'],
            'department_id' => $validated['department'],
            'status' => 'Pending'
        ]);

        return redirect()->back()->with('success', 'Job Request submitted successfully.');
    }

    public function updateJobRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'job_no' => 'nullable|string|max:255',
            'project_title' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'department' => 'nullable|exists:departments,dept_id',
            'status' => 'required|string'
        ]);

        $jobRequest = \App\Models\JobRequest::findOrFail($id);
        $jobRequest->update([
            'job_no' => $validated['job_no'],
            'project_title' => $validated['project_title'],
            'specifications' => $validated['specifications'],
            'due_date' => $validated['due_date'],
            'date' => $validated['date'],
            'department_id' => $validated['department'],
            'status' => $validated['status']
        ]);

        return redirect()->back()->with('success', 'Job Request updated successfully.');
    }

    public function destroyJobRequest($id)
    {
        $jobRequest = \App\Models\JobRequest::findOrFail($id);
        $jobRequest->delete();

        return redirect()->back()->with('success', 'Job Request deleted successfully.');
    }
}
