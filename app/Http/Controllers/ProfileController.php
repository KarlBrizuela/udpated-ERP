<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $realApprovals = [];
        $query = \App\Models\SalesOrder::query()->with('customer');

        // Logic based on position/role
        $isMktManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor');
        $isFinance = str_contains(strtolower($user->division), 'finance') || str_contains(strtolower($user->department), 'accounting') || str_contains(strtolower($user->position), 'finance');
        $isProduction = str_contains(strtolower($user->division), 'production') || str_contains(strtolower($user->department), 'logistic');

        if ($isMktManager && str_contains(strtolower($user->division), 'marketing')) {
            $query->where('status', 'pending_mkt_approval');
        } elseif ($isFinance) {
            $query->whereIn('status', ['pending_acct_approval', 'pending_si_approval']);
        } elseif ($isProduction) {
            // For production, "Approvals" usually means DR approval or SI preparation
            $query->whereIn('status', ['pending_dr_approval', 'pending_si_prep', 'pending_dr_prep']);
        } else {
            // If they are just regular staff, maybe they see nothing unless they are the preparer
            $query->where('prepared_by', $user->id)->whereIn('status', ['draft', 'pending_mkt_approval']);
        }

        $pendingOrders = $query->latest()->get();

        foreach ($pendingOrders as $so) {
            $realApprovals[] = [
                'id' => $so->id,
                'document_type' => 'Sales Order',
                'reference_no' => $so->so_number,
                'date' => $so->created_at->format('Y-m-d'),
                'status' => strtoupper(str_replace('_', ' ', $so->status)),
                'description' => ($so->customer->customer_name ?? 'Walk-in') . ' - ₱' . number_format($so->total_amount, 2),
                'link' => route('marketing.sales-orders.detail', $so->id)
            ];
        }

        // Determine sidebar based on user division
        $sidebar = 'super-admin';
        if ($user->position !== 'Super Admin') {
            if (str_contains(strtolower($user->division), 'marketing')) {
                $sidebar = 'marketing';
            } elseif (str_contains(strtolower($user->division), 'production')) {
                $sidebar = 'production';
            } elseif (str_contains(strtolower($user->division), 'finance') || str_contains(strtolower($user->division), 'admin')) {
                $sidebar = 'admin-finance';
            }
        }



        // Fetch pending job orders for Admin Manager or MIS Supervisor
        $pendingJobOrders = [];
        if ($user->position === 'Manager' || $user->position === 'MIS Supervisor' || $user->position === 'Super Admin') {
            // Fetch CCTV requests with pending approval status
            $cctvRequests = \App\Models\Admin\MIS\CCTVReq::whereIn('status', ['pending approval', 'Pending HR approval', 'Pending Final Approval'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function ($request) use ($user) {
                    return $request->canBeApprovedBy($user);
                });
            
            foreach ($cctvRequests as $req) {
                $pendingJobOrders[] = [
                    'type' => 'CCTV',
                    'id' => $req->cctv_req_id,
                    'date' => $req->created_at->format('m/d/Y'),
                    'requested_by' => $req->requested_by,
                    'details' => \Illuminate\Support\Str::limit($req->purpose, 50),
                    'original' => $req
                ];
            }

            // Fetch Material requests with pending approval status
            $materialRequests = \App\Models\Admin\MIS\MaterialReq::where('status', 'pending approval')
                ->orderBy('request_date', 'desc')
                ->get();
            
            foreach ($materialRequests as $req) {
                $pendingJobOrders[] = [
                    'type' => 'Material',
                    'id' => $req->material_req_id,
                    'date' => \Carbon\Carbon::parse($req->request_date)->format('m/d/Y'),
                    'requested_by' => $req->requested_by,
                    'details' => \Illuminate\Support\Str::limit($req->request_details, 50),
                    'original' => $req
                ];
            }

            // Fetch QB Requests with pending status
            $qbRequests = \App\Models\Admin\MIS\MisQbRequest::with(['user', 'items'])->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($qbRequests as $req) {
                $pendingJobOrders[] = [
                    'type' => 'QB',
                    'id' => $req->qb_req_id,
                    'date' => $req->created_at->format('m/d/Y'),
                    'requested_by' => $req->user->name ?? 'Unknown User',
                    'details' => \Illuminate\Support\Str::limit($req->customer_item_name, 50),
                    'original' => $req
                ];
            }

            // Fetch Service Requests with pending status
            $serviceRequests = \App\Models\Admin\MIS\MisServiceRequest::with('user')->where('status', 'pending')
                ->orderBy('date', 'desc')
                ->get();
            
            foreach ($serviceRequests as $req) {
                $pendingJobOrders[] = [
                    'type' => 'Service',
                    'id' => $req->service_req_id,
                    'date' => \Carbon\Carbon::parse($req->date)->format('m/d/Y'),
                    'requested_by' => $req->requestor_name,
                    'details' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
                    'original' => $req
                ];
            }

            // Fetch Undertime Requests with pending status
            $undertimeRequests = \App\Models\Admin\MIS\MisUndertimeRequest::with('user')->where('status', 'pending')
                ->orderBy('date', 'desc')
                ->get();
            
            foreach ($undertimeRequests as $req) {
                $pendingJobOrders[] = [
                    'type' => 'Undertime',
                    'id' => $req->undertime_req_id,
                    'date' => \Carbon\Carbon::parse($req->date)->format('m/d/Y'),
                    'requested_by' => $req->employee_name,
                    'details' => \Illuminate\Support\Str::limit($req->reason, 50),
                    'original' => $req
                ];
            }

            // Sort by date descending
            usort($pendingJobOrders, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return view('profile.index', [
            'approvals' => $realApprovals,
            'sidebar' => $sidebar,
            'pendingJobOrders' => $pendingJobOrders
        ]);
    }

    public function testEmail(Request $request)
    {
        $user = auth()->user();
        
        try {
            // Send a mock notification to the logged-in user
            $user->notify(new \App\Notifications\DirectorApprovalRequested((object)[
                'cctv_req_id' => 0,
                'requested_by' => 'System Test',
                'purpose' => 'This is a test of the email notification system from your profile page.'
            ], 'Test System'));
            
            return redirect()->back()->with('success', 'Test email sent successfully to ' . $user->email . '. Please check your inbox (or Mailtrap).');
        } catch (\Exception $e) {
            \Log::error("Email test failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        
        // Update plain_password if it exists on the model
        if (in_array('plain_password', $user->getFillable()) || \Illuminate\Support\Facades\Schema::hasColumn('users', 'plain_password')) {
            $user->plain_password = $request->new_password;
        }
        
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}
