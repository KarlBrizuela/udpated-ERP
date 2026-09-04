<?php

namespace App\Http\Controllers\Admin\MIS;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MisUndertimeRequest;
use Illuminate\Http\Request;

class UndertimeReqController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'date' => 'required|date',
            'time_from' => 'required',
            'time_to' => 'required',
            'reason' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        MisUndertimeRequest::create($validated);

        return redirect()->back()->with('success', 'Undertime Request submitted successfully');
    }

    public function update(Request $request, MisUndertimeRequest $undertimeRequest)
    {
        $validated = $request->validate([
            'employee_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'time_from' => 'sometimes|required',
            'time_to' => 'sometimes|required',
            'reason' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pending,approved,rejected,completed',
        ]);

        $undertimeRequest->update($validated);

        if ($undertimeRequest->status === 'Pending Final Approval') {
            $director = \App\Models\User::where('position', 'Director')->first();
            if ($director) {
                try {
                    $director->notify(new \App\Notifications\DirectorApprovalRequested($undertimeRequest, 'Undertime'));
                } catch (\Exception $e) {
                    \Log::error("Failed to send Undertime approval notification: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'Undertime Request updated successfully');
    }

    public function destroy(MisUndertimeRequest $undertimeRequest)
    {
        $undertimeRequest->delete();

        return redirect()->back()->with('success', 'Undertime Request deleted successfully');
    }
}
