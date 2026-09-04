<?php

namespace App\Http\Controllers\Admin\MIS;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MisQbRequest;
use App\Models\Admin\MIS\MisQbItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QBReqController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_item_name' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.from' => 'nullable|string',
            'items.*.to' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $qbReq = MisQbRequest::create([
                'user_id' => auth()->id(),
                'customer_item_name' => $validated['customer_item_name'],
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                if (!empty($item['from']) || !empty($item['to'])) {
                    MisQbItem::create([
                        'qb_req_id' => $qbReq->qb_req_id,
                        'from_value' => $item['from'],
                        'to_value' => $item['to'],
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'QB Request submitted successfully');
    }

    public function update(Request $request, MisQbRequest $qbRequest)
    {
        $validated = $request->validate([
            'customer_item_name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:pending,approved,rejected,completed',
            'items' => 'sometimes|array',
            'items.*.id' => 'nullable|exists:mis_qb_items,id',
            'items.*.from' => 'nullable|string',
            'items.*.to' => 'nullable|string',
        ]);

        DB::transaction(function () use ($qbRequest, $validated) {
            $qbRequest->update([
                'customer_item_name' => $validated['customer_item_name'] ?? $qbRequest->customer_item_name,
                'status' => $validated['status'] ?? $qbRequest->status,
            ]);
            
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    if (!empty($itemData['from']) || !empty($itemData['to'])) {
                        if (!empty($itemData['id'])) {
                            MisQbItem::where('id', $itemData['id'])->update([
                                'from_value' => $itemData['from'],
                                'to_value' => $itemData['to'],
                            ]);
                        } else {
                            MisQbItem::create([
                                'qb_req_id' => $qbRequest->qb_req_id,
                                'from_value' => $itemData['from'],
                                'to_value' => $itemData['to'],
                            ]);
                        }
                    } elseif (!empty($itemData['id'])) {
                        MisQbItem::where('id', $itemData['id'])->delete();
                    }
                }
            }
        });

        $qbRequest->refresh();
        if ($qbRequest->status === 'Pending Final Approval') {
            $director = \App\Models\User::where('position', 'Director')->first();
            if ($director) {
                try {
                    $director->notify(new \App\Notifications\DirectorApprovalRequested($qbRequest, 'QB Change'));
                } catch (\Exception $e) {
                    \Log::error("Failed to send QB approval notification: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'QB Request updated successfully');
    }

    public function destroy(MisQbRequest $qbRequest)
    {
        $qbRequest->delete(); // Cascades on items

        return redirect()->back()->with('success', 'QB Request deleted successfully');
    }
}
