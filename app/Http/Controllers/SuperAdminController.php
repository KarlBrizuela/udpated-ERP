<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    private $mainDivisions = [
        'Marketing Division',
        'Production Division',
        'Admin & Finance Division'
    ];

    private $allPositions = [
        'Super Admin',
        'Director',
        'Manager',
        'Senior Accounting Staff',
        'Accounting Staff',
        'Inventory Staff',
        'Cashier',
        'Credit and Collection Staff',
        'Senior Inventory and Collection Staff',
        'Inventory and Collection Staff',
        'Billing Staff',
        'MIS Supervisor',
        'MIS Staff',
        'GSD Supervisor',
        'HR Staff',
        'Senior Supervisor',
        'Account Executive',
        'Marketing Coordinator',
        'Bookstore Staff',
        'Bookschain Staff',
        'Booksales Staff',
        'Marketing Staff',
        'DTO Supervisor',
        'Senior Ford Staff',
        'Ford Staff',
        'Senior Logistics Staff',
        'Logistics Staff',
        'Driver',
        'Printing Services Staff'
    ];

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        return view('super-admin.users', [
            'title' => 'User Management',
            'role' => 'Super Admin',
            'sidebar' => $sidebar,
            'users' => $users,
            'positions' => $this->allPositions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => 'required|string|unique:users,employee_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'division' => 'required|string',
            'department' => 'nullable|string',
            'position' => 'required|string',
            'status' => 'boolean',
        ]);

        $validated['plain_password'] = $validated['password'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $request->has('status') ? true : false;
        
        $user = User::create($validated);
        
        // Also add to division_user table for access control
        // Auto-assign all three divisions for Director accounts
        if ($user->position === 'Director') {
            foreach ($this->mainDivisions as $div) {
                \App\Models\UserDivision::create([
                    'user_id' => $user->id,
                    'division' => $div
                ]);
            }
        } else {
            \App\Models\UserDivision::create([
                'user_id' => $user->id,
                'division' => $user->division
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'employee_number' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'division' => 'required|string',
            'department' => 'nullable|string',
            'position' => 'required|string',
            'status' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['plain_password'] = $validated['password'];
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['status'] = $request->has('status') ? true : false;

        $user->update($validated);

        // Auto-sync divisions if updated to Director
        if ($user->position === 'Director') {
            $user->divisions()->delete();
            foreach ($this->mainDivisions as $div) {
                \App\Models\UserDivision::create([
                    'user_id' => $user->id,
                    'division' => $div
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function roles()
    {
        // 1. Auto-Sync: Check for positions in Users table that don't exist in Roles table
        $distinctUserRoles = User::distinct()->pluck('position');
        
        foreach ($distinctUserRoles as $userRoleName) {
            if ($userRoleName && !\App\Models\Role::where('name', $userRoleName)->exists()) {
                
                // Find a user with this position to get their division
                $sampleUser = User::where('position', $userRoleName)->first();
                $division = $sampleUser ? $sampleUser->division : 'All Divisions';

                // Create the missing role automatically
                \App\Models\Role::create([
                    'name' => $userRoleName,
                    'division' => $division, // Use the user's division
                    'permission_level' => 'VIEW', // Default safe permission
                    'access_scope' => 'OWN_DEPARTMENT', // Default safe scope
                    'description' => 'Automatically created from user assignment',
                    'is_active' => true
                ]);
            }
        }

        // Also ensure all master positions exist as roles
        foreach ($this->allPositions as $posName) {
            if (!\App\Models\Role::where('name', $posName)->exists()) {
                \App\Models\Role::create([
                    'name' => $posName,
                    'is_active' => true,
                    'description' => 'Master Position Role'
                ]);
            }
        }

        // 1b. Ensure all users have their 'position' column set if empty (usually not needed if created via UI)
        User::whereNull('position')->orWhere('position', '')->each(function ($user) {
            // Find a valid position or leave as N/A
        });

        // 2. Fetch users and filtered roles
        $users = User::with('divisions')->orderBy('created_at', 'desc')->get();
        $roles = \App\Models\Role::whereIn('name', $this->allPositions)
            ->orderBy('name', 'asc')
            ->get();

        // Define all available permission keys for the UI
        $allAvailablePermissions = [
            'Marketing' => [
                'marketing.dashboard' => 'Dashboard',
                'marketing.customers' => 'Customer Management',
                'marketing.area_sales' => 'Area Sales',
                'marketing.area_sales.sales_orders' => '— Sales Orders List',
                'marketing.area_sales.freight_quotations' => '— Freight Quotations',
                'marketing.area_sales.direct_invoice_website' => '— Direct Invoice (Website)',
                'marketing.area_sales.direct_invoice_ecom' => '— Direct Invoice (E-com)',
                'marketing.area_sales.acknowledgement_receipt' => '— Acknowledgement Receipt',
                'marketing.area_sales.credit_memo' => '— Credit Memo Form',
                'marketing.area_sales.proof_of_payment' => '— Proof of Payment',
                'marketing.area_sales.team_stocks' => '— Team Stocks',
                'marketing.direct_sales' => 'Direct Sales',
                'marketing.direct_sales.pos' => '— POS System',
                'marketing.direct_sales.products' => '— POS Products',
                'marketing.direct_sales.nbs_import' => '— NBS PO Import',
                'marketing.ads_promo' => 'Ads and Promo',
                'marketing.ads_promo.marketing_plan' => '— Marketing Plan Itinerary Budget',
                'marketing.ads_promo.sponsors' => '— List of Sponsors',
                'marketing.ecom' => 'E-Com',
                'marketing.ecom.pos' => '— MIBF POS',
                'marketing.book_mgmt' => 'Book Management',
                'marketing.book_mgmt.book_list' => '— Book List (Master)',
                'marketing.book_mgmt.consignment' => '— Consignment Management',
                'marketing.supplier_mgmt' => 'Supplier Management',
                'marketing.approval_queue' => 'Approval Queue',
                'marketing.my_requests' => 'My Requests',
            ],
            'Production' => [
                'production.dashboard' => 'Dashboard',
                'production.inventory' => 'Inventory Management',
                'production.inventory.overview' => '— Inventory Overview',
                'production.inventory.add_stock' => '— Add Stock',
                'production.inventory.received' => '— Received Items',
                'production.logistic' => 'Logistics',
                'production.logistic.pick_lists' => '— Pick Lists',
                'production.logistic.packing' => '— Packing',
                'production.logistic.delivery_scheduling' => '— Delivery Scheduling',
                'production.logistic.delivery_receipts' => '— Delivery Receipts',
                'production.logistic.driver_dashboard' => '— Driver Dashboard',
                'production.logistic.delivery_tracking' => '— Delivery Tracking',
                'production.logistic.purchase_orders' => '— Purchase Orders',
                'production.logistic.receiving_reports' => '— Receiving Reports',
                'production.logistic.freight_quotation_review' => '— Freight Quotation (Review)',
                'production.logistic.rider_collections' => '— Rider Collections',
                'production.logistic.acknowledgement_receipt' => '— Acknowledgement Receipt (Area Sales Consignment)',
                'production.logistic.area_consignment' => '— Consignment Receipt',
                'production.dto' => 'DTO',
                'production.dto.job_request_form' => '— Job Request Form',
                'production.ford' => 'FORD',
                'production.ford.auto_debit' => '— Auto Debit',
                'production.ford.client_payment_posting' => '— Client Payment for Posting',
                'production.ford.eford_payout' => '— E-FORD Payout',
                'production.ford.payment_request' => '— Payment Request',
                'production.ford.purchase_order' => '— Purchase Order',
                'production.ford.request_for_quotation' => '— Request for Quotation',
                'production.ford.sales_order' => '— Sales Order',
                'production.ford.transmittal' => '— Transmittal',
                'production.printing' => 'Printing Services',
                'production.printing.request_payment_to_printer' => '— Request Payment to Printer',
                'production.approval_queue' => 'Approval Queue',
                'production.my_requests' => 'My Requests',
            ],
            'Admin & Finance' => [
                'admin_finance.dashboard' => 'Dashboard',
                'admin_finance.accounting' => 'Accounting',
                'admin_finance.accounting.general_journal' => '— General Journal',
                'admin_finance.accounting.sales_invoice' => '— Sales Invoice',
                'admin_finance.accounting.check_voucher' => '— Check Voucher',
                'admin_finance.accounting.materials_requisition' => '— Materials/Supplies Requisition',
                'admin_finance.accounting.material_requests' => '— Material Requests',
                'admin_finance.accounting.cash_advance_liquidation' => '— Cash Advance Liquidation',
                'admin_finance.accounting.cod_collections' => '— COD Collections Verification',
                'admin_finance.accounting.office_supplies' => '— Office Supplies',
                'admin_finance.accounting.expenses' => '— Expenses',
                'admin_finance.accounting.chart_of_accounts' => '— Chart of Accounts',
                'admin_finance.credit_collection' => 'Credit and Collection',
                'admin_finance.credit_collection.billing' => '— Billing',
                'admin_finance.credit_collection.reports' => '— Reports',
                'admin_finance.credit_collection.invoice' => '— Invoice',
                'admin_finance.gsd' => 'GSD',
                'admin_finance.gsd.asset_management' => '— Asset Management',
                'admin_finance.gsd.job_orders' => '— Job Orders',
                'admin_finance.hr' => 'HR',
                'admin_finance.hr.job_orders' => '— Job Orders',
                'admin_finance.mis' => 'MIS',
                'admin_finance.mis.job_orders' => '— Job Orders',
                'admin_finance.service_requests' => 'Create Service Request',
                'admin_finance.approval_queue' => 'Approval Queue',
                'admin_finance.my_requests' => 'My Requests',
            ],
            'Finance' => [
                'admin_finance.petty_cash_voucher' => 'Petty Cash Voucher',
                'admin_finance.freight_voucher' => 'Freight Voucher',
            ]
        ];

        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        // Build role permissions map for JavaScript
        $rolePermissionsMap = [];
        foreach ($roles as $roleItem) {
            $rolePermissionsMap[$roleItem->name] = $roleItem->permissions ?? [];
        }

        return view('super-admin.roles', [
            'title' => 'Roles & Permissions',
            'role' => 'Super Admin',
            'sidebar' => $sidebar,
            'users' => $users,
            'roles' => $roles,
            'positions' => $this->allPositions,
            'availablePermissions' => $allAvailablePermissions,
            'rolePermissionsMap' => $rolePermissionsMap
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // 1. Sync Divisions
        // Remove existing divisions
        $user->divisions()->delete();

        // Add selected divisions
        $divisions = $request->input('divisions', []);
        foreach ($divisions as $division) {
            \App\Models\UserDivision::create([
                'user_id' => $user->id,
                'division' => $division
            ]);
        }
        
        // Update main division field to the first selected one (for backward compatibility)
        if (!empty($divisions)) {
            $user->division = $divisions[0];
        }

        // Update position if provided
        if ($request->has('position')) {
            $user->position = $request->input('position');
        }

        // Update sales_team if provided
        if ($request->has('sales_team')) {
            $user->sales_team = $request->input('sales_team') ?: null;
        }

        // Update User-Level Permissions (Manual Override)
        // If no permissions are explicitly set, use NULL to fall back to role permissions
        $permissions = $request->input('permissions', []);
        $user->permissions = empty($permissions) ? null : $permissions;
        
        $user->save();

        return redirect()->route('roles.index')->with('success', 'User access updated successfully.');
    }

    public function updateRolePermissions(Request $request, $id)
    {
        $role = \App\Models\Role::findOrFail($id);
        
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $role->update([
            'permissions' => $request->input('permissions', []),
            'description' => $request->input('description'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('roles.index')->with('success', 'Role permissions updated successfully.');
    }

    public function divisions()
    {
        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        return view('super-admin.divisions', [
            'title' => 'Divisions',
            'role' => 'Super Admin',
            'sidebar' => $sidebar
        ]);
    }

    public function settings()
    {
        $currentUser = auth()->user();
        $sidebar = $currentUser->division === 'All Divisions' ? 'director' : 'super-admin';

        // Load payment settings
        $paymentSettings = \App\Models\PaymentSetting::getAllSettings();

        return view('super-admin.settings', [
            'title' => 'System Settings',
            'role' => 'Super Admin',
            'sidebar' => $sidebar,
            'paymentSettings' => $paymentSettings
        ]);
    }
}
