<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminFinance\MaterialRequisition;
use App\Models\PurchaseOrder;
use App\Models\ReceivingReport;

class ProcurementController extends Controller
{
    /**
     * Display the Purchasing & Procurement dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $sidebar = 'admin-finance';
        $role = 'Finance Manager';

        // 1. Fetch paginated Material Requisitions (Purchase Requests)
        $requisitions = MaterialRequisition::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'requisitions_page')
            ->withQueryString();

        // 2. Fetch paginated Purchase Orders
        $purchaseOrders = PurchaseOrder::with(['supplier', 'preparedBy', 'approvedBy', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'po_page')
            ->withQueryString();

        // 3. Fetch paginated Receiving Reports
        $receivingReports = ReceivingReport::with(['supplier', 'purchaseOrder', 'receivedBy', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'rr_page')
            ->withQueryString();

        // Active tab detection helper
        $activeTab = 'requisitions';
        if ($request->has('po_page')) {
            $activeTab = 'purchase-orders';
        } elseif ($request->has('rr_page')) {
            $activeTab = 'receiving-reports';
        }

        // 4. Fetch all suppliers for the dropdown
        $suppliers = \App\Models\Supplier::orderBy('company_name')->get();

        return view('admin-finance.accounting.procurement', compact(
            'requisitions',
            'purchaseOrders',
            'receivingReports',
            'activeTab',
            'sidebar',
            'role',
            'suppliers'
        ));
    }
}
