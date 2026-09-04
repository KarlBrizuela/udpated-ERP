<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\OfficeSupply;
use App\Models\OfficeSupplyLog;
use App\Models\Supplier;
use Illuminate\Http\Request;

class OfficeSupplyController extends Controller
{
    /**
     * Display a listing of the office supplies.
     */
    public function index(Request $request)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');

        $supplies = OfficeSupply::when($search, function ($query, $search) {
            return $query->where('item_name', 'like', '%' . $search . '%');
        })
        ->orderBy('item_name', 'asc')
        ->paginate(15);

        // Fetch suppliers for dropdown
        $suppliers = Supplier::orderBy('company_name', 'asc')->get();

        // Fetch paginated transaction logs with filtering
        // Auto-sync initial beginning quantity logs for items without log history
        $suppliesWithoutLogs = OfficeSupply::whereNotIn('id', OfficeSupplyLog::whereNotNull('office_supply_id')->pluck('office_supply_id')->unique()->toArray())->get();
        foreach ($suppliesWithoutLogs as $sup) {
            OfficeSupplyLog::create([
                'office_supply_id' => $sup->id,
                'item_name' => $sup->item_name,
                'supplier_id' => null,
                'added_by' => $user->id,
                'quantity' => $sup->items_stock,
                'unit_price' => $sup->item_price,
                'previous_stock' => 0,
                'new_stock' => $sup->items_stock,
                'notes' => 'Beginning Quantity',
                'created_at' => $sup->created_at ?: now(),
            ]);
        }

        // Backfill unit_price for existing logs if 0
        $logsWithoutPrice = OfficeSupplyLog::where('unit_price', 0)->whereNotNull('office_supply_id')->with('officeSupply')->get();
        foreach ($logsWithoutPrice as $l) {
            if ($l->officeSupply && $l->officeSupply->item_price > 0) {
                $l->update(['unit_price' => $l->officeSupply->item_price]);
            }
        }

        $logSearch = $request->input('log_search');
        $logStartDate = $request->input('log_start_date');
        $logEndDate = $request->input('log_end_date');

        $logsQuery = OfficeSupplyLog::with(['officeSupply', 'supplier', 'addedBy'])
            ->latest();

        if ($logSearch) {
            $logsQuery->where(function($q) use ($logSearch) {
                $q->where('item_name', 'like', '%' . $logSearch . '%')
                  ->orWhereHas('officeSupply', function ($query) use ($logSearch) {
                      $query->where('item_name', 'like', '%' . $logSearch . '%');
                  });
            });
        }

        if ($logStartDate) {
            $logsQuery->whereDate('created_at', '>=', $logStartDate);
        }

        if ($logEndDate) {
            $logsQuery->whereDate('created_at', '<=', $logEndDate);
        }

        $logs = $logsQuery->paginate(10, ['*'], 'logs_page');

        return view('admin-finance.accounting.office-supplies.index', [
            'title' => 'Office Supplies Inventory',
            'role' => $user->position,
            'sidebar' => 'admin-finance',
            'supplies' => $supplies,
            'search' => $search,
            'suppliers' => $suppliers,
            'logs' => $logs,
            'log_search' => $logSearch,
            'log_start_date' => $logStartDate,
            'log_end_date' => $logEndDate
        ]);
    }

    /**
     * Store a newly created office supply in storage.
     */
    public function store(Request $request)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_price' => 'required|numeric|min:0',
            'items_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $supply = OfficeSupply::create([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'items_stock' => $request->items_stock,
            'unit' => $request->input('unit', 'pcs'),
        ]);

        // Create initial Beginning Quantity stock log
        OfficeSupplyLog::create([
            'office_supply_id' => $supply->id,
            'item_name' => $supply->item_name,
            'supplier_id' => null,
            'added_by' => $user->id,
            'quantity' => $request->items_stock,
            'unit_price' => $request->item_price,
            'previous_stock' => 0,
            'new_stock' => $request->items_stock,
            'notes' => 'Beginning Quantity',
        ]);

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Office supply item created successfully.');
    }

    /**
     * Update the specified office supply in storage.
     */
    public function update(Request $request, $id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $supply = OfficeSupply::findOrFail($id);
        $supply->update([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'unit' => $request->input('unit', 'pcs'),
        ]);

        // Update item_name on logs associated with this supply
        OfficeSupplyLog::where('office_supply_id', $supply->id)->update([
            'item_name' => $supply->item_name,
        ]);

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Office supply item updated successfully.');
    }

    /**
     * Remove the specified office supply from storage.
     */
    public function destroy($id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $supply = OfficeSupply::findOrFail($id);

        // Preserve stock history logs before removing the item entity
        OfficeSupplyLog::where('office_supply_id', $supply->id)->update([
            'item_name' => $supply->item_name,
            'office_supply_id' => null,
        ]);

        $supply->delete();

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Office supply item deleted successfully.');
    }

    /**
     * Add stock to an office supply item.
     */
    public function addStock(Request $request, $id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.office_supplies')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $supply = OfficeSupply::findOrFail($id);

        $previousStock = $supply->items_stock;
        $quantity = (int) $request->quantity;
        $newStock = $previousStock + $quantity;

        // Update supply stock
        $supply->update([
            'items_stock' => $newStock,
        ]);

        // Create log entry with item_name snapshot
        OfficeSupplyLog::create([
            'office_supply_id' => $supply->id,
            'item_name' => $supply->item_name,
            'supplier_id' => $request->supplier_id,
            'added_by' => $user->id,
            'quantity' => $quantity,
            'unit_price' => $supply->item_price,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin-finance.accounting.office-supplies.index')
            ->with('success', 'Stock of ' . $quantity . ' added successfully to ' . $supply->item_name . '.');
    }
}
