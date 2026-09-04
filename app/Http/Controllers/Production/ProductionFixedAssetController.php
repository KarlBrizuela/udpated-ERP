<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionFixedAsset;
use App\Models\AssetMaintenanceLog;
use App\Models\Supplier;

class ProductionFixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $categories = [
            'Digital Press',
            'RISO',
            'Vehicles',
            'Computers',
            'Furniture',
            'Buildings',
            'Other',
        ];

        $selectedCategory = $request->query('category', 'All');
        $search = $request->query('search');

        $query = ProductionFixedAsset::with('maintenanceLogs');

        if ($selectedCategory && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        $assets = $query->latest('purchase_date')->get();
        $suppliers = Supplier::orderBy('company_name')->get();

        // Recalculate dynamic depreciation and repair totals for each asset
        foreach ($assets as $ast) {
            $ast->calculateDepreciation();
            $ast->recalculateRepairCosts();
            $ast->save();
        }

        // Summary metrics
        $totalOriginalValue = $assets->sum('purchase_price');
        $totalAccumulatedDepreciation = $assets->sum('accumulated_depreciation');
        $totalNetBookValue = $assets->sum('current_value');
        $totalRepairCostAll = $assets->sum('total_repair_cost');
        $totalAssetsCount = $assets->count();

        return view('production.assets.index', [
            'title' => 'Production Fixed Assets',
            'role' => auth()->user() ? auth()->user()->position : 'Staff',
            'sidebar' => 'production',
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'assets' => $assets,
            'suppliers' => $suppliers,
            'metrics' => [
                'total_original_value' => $totalOriginalValue,
                'total_accumulated_depreciation' => $totalAccumulatedDepreciation,
                'total_net_book_value' => $totalNetBookValue,
                'total_repair_cost' => $totalRepairCostAll,
                'total_assets_count' => $totalAssetsCount,
            ],
        ]);
    }

    public function show($id)
    {
        $asset = ProductionFixedAsset::with('maintenanceLogs')->findOrFail($id);
        $asset->calculateDepreciation();
        $asset->recalculateRepairCosts();
        $asset->save();

        return view('production.assets.show', [
            'title' => 'Fixed Asset Profile: ' . $asset->name,
            'role' => auth()->user() ? auth()->user()->position : 'Staff',
            'sidebar' => 'production',
            'asset' => $asset,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'useful_life_years' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $assetCode = 'AST-' . strtoupper(substr(str_replace(' ', '', $request->category), 0, 3)) . '-' . rand(1000, 9999);

        $asset = new ProductionFixedAsset($request->all());
        $asset->asset_code = $assetCode;
        $asset->useful_life_years = $request->useful_life_years ?: 5;
        $asset->salvage_value = $request->salvage_value ?: 0.00;
        $asset->calculateDepreciation();
        $asset->save();

        return redirect()->route('production.assets.index')
            ->with('success', "Fixed Asset '{$asset->name}' registered successfully!");
    }

    public function storeMaintenanceLog(Request $request)
    {
        $request->validate([
            'production_fixed_asset_id' => 'required|exists:production_fixed_assets,id',
            'maintenance_date' => 'required|date',
            'title' => 'required|string|max:255',
            'technician' => 'nullable|string|max:255',
            'repair_cost' => 'required|numeric|min:0',
            'details' => 'nullable|string',
        ]);

        $log = AssetMaintenanceLog::create($request->all());

        $asset = ProductionFixedAsset::findOrFail($request->production_fixed_asset_id);
        $asset->recalculateRepairCosts();
        $asset->save();

        return redirect()->back()->with('success', 'Maintenance & Repair Log recorded successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'useful_life_years' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string',
        ]);

        $asset = ProductionFixedAsset::findOrFail($id);
        $asset->fill($request->all());
        $asset->calculateDepreciation();
        $asset->save();

        return redirect()->route('production.assets.index')
            ->with('success', "Fixed Asset '{$asset->name}' updated successfully!");
    }

    public function destroy($id)
    {
        $asset = ProductionFixedAsset::findOrFail($id);
        $asset->delete();

        return redirect()->route('production.assets.index')
            ->with('success', 'Fixed Asset deleted successfully!');
    }
}
