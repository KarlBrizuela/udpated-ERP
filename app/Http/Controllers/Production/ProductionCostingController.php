<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionCosting;
use App\Models\Book;
use Illuminate\Support\Str;

class ProductionCostingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = ProductionCosting::with('book');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_number', 'like', "%{$search}%");
            });
        }

        $costings = $query->latest()->get();
        $books = Book::orderBy('name')->get();

        // Calculate summary metrics
        $totalCogsAll = $costings->sum('total_cogs');
        $avgUnitCogs = $costings->count() > 0 ? $costings->avg('unit_cogs') : 0.00;
        $totalQtyProduced = $costings->sum('quantity_produced');
        $activeJobsCount = $costings->count();

        return view('production.costing.index', [
            'title' => 'Production Costing',
            'role' => auth()->user() ? auth()->user()->position : 'Staff',
            'sidebar' => 'production',
            'costings' => $costings,
            'books' => $books,
            'metrics' => [
                'total_cogs' => $totalCogsAll,
                'avg_unit_cogs' => $avgUnitCogs,
                'total_qty_produced' => $totalQtyProduced,
                'active_jobs_count' => $activeJobsCount,
            ],
        ]);
    }

    public function show($id)
    {
        $costing = ProductionCosting::with('book')->findOrFail($id);

        return view('production.costing.show', [
            'title' => 'Production Costing Sheet: ' . $costing->job_title,
            'role' => auth()->user() ? auth()->user()->position : 'Staff',
            'sidebar' => 'production',
            'costing' => $costing,
        ]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'quantity_produced' => 'required|integer|min:1',
            'pages_count' => 'required|integer|min:1',
            'book_id' => 'nullable|exists:books,id',
            'binding_type' => 'nullable|string',
            'has_uv' => 'nullable|boolean',
            'has_shrink_wrap' => 'nullable|boolean',
        ]);

        $qty = (int) $request->quantity_produced;
        $pages = (int) $request->pages_count;

        // Automated Cost Calculation Engine (No manual accounting entry required)
        // 1. Paper: based on sheets/reams needed (approx 1 ream per 500 pages printed)
        $paperReams = ($pages / 16) * ($qty / 500);
        $paperCost = round(max(500, $paperReams * 320), 2);

        // 2. Ink: based on page signatures & quantity
        $inkCost = round(($pages / 8) * ($qty * 0.04), 2);

        // 3. Labor: Press & post-press operator run rate
        $laborHours = max(2, round(($qty / 1000) * 1.5, 2));
        $laborCost = round($laborHours * 180, 2);

        // 4. Electricity: Heavy press power consumption (kWh)
        $electricityCost = round($laborHours * 95, 2);

        // 5. Machine Cost: Hourly depreciation & wear allowance
        $machineCost = round($laborHours * 150, 2);

        // 6. Binding: Perfect binding / saddle stitch / hardbound rate
        $bindingRate = match($request->binding_type) {
            'Hardbound' => 45.00,
            'Saddle Stitch' => 3.50,
            default => 8.50 // Perfect Binding
        };
        $bindingCost = round($qty * $bindingRate, 2);

        // 7. UV Coating
        $uvCost = $request->has_uv ? round($qty * 2.50, 2) : 0.00;

        // 8. Shrink Wrap
        $shrinkWrapCost = $request->has_shrink_wrap ? round($qty * 1.20, 2) : 0.00;

        // 9. Packaging (Boxes, cartons, strapping)
        $boxesNeeded = ceil($qty / 40);
        $packagingCost = round($boxesNeeded * 35.00, 2);

        // 10. Freight (Transport allocation)
        $freightCost = round($qty * 1.50, 2);

        // 11. Warehouse (Storage & handling allocation)
        $warehouseCost = round($qty * 0.80, 2);

        // 12. Overhead (Indirect factory overhead allocation 5% of direct materials)
        $overheadCost = round(($paperCost + $inkCost + $bindingCost) * 0.05, 2);

        // Calculate COGS
        $totalCogs = $paperCost + $inkCost + $laborCost + $electricityCost + $machineCost
                   + $bindingCost + $uvCost + $shrinkWrapCost + $packagingCost
                   + $freightCost + $warehouseCost + $overheadCost;

        $unitCogs = round($totalCogs / $qty, 2);

        $jobNum = 'JOB-COST-' . date('Ym') . '-' . rand(1000, 9999);

        $costing = ProductionCosting::create([
            'job_number' => $jobNum,
            'book_id' => $validated['book_id'] ?? null,
            'job_title' => $validated['job_title'],
            'quantity_produced' => $qty,
            'pages_count' => $pages,
            'paper_cost' => $paperCost,
            'ink_cost' => $inkCost,
            'labor_cost' => $laborCost,
            'electricity_cost' => $electricityCost,
            'machine_cost' => $machineCost,
            'binding_cost' => $bindingCost,
            'uv_cost' => $uvCost,
            'shrink_wrap_cost' => $shrinkWrapCost,
            'packaging_cost' => $packagingCost,
            'freight_cost' => $freightCost,
            'warehouse_cost' => $warehouseCost,
            'overhead_cost' => $overheadCost,
            'total_cogs' => round($totalCogs, 2),
            'unit_cogs' => $unitCogs,
            'status' => 'calculated',
            'notes' => 'Automated calculation engine run from Production parameters.',
        ]);

        app(\App\Services\ProductionErpIntegrationService::class)->syncToExpenseTable($costing);

        return redirect()->route('production.costing.show', $costing->id)
            ->with('success', 'Production Costing automatically calculated and recorded in Expenses!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'quantity_produced' => 'required|integer|min:1',
            'paper_cost' => 'required|numeric|min:0',
            'ink_cost' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'electricity_cost' => 'required|numeric|min:0',
            'machine_cost' => 'required|numeric|min:0',
            'binding_cost' => 'required|numeric|min:0',
            'uv_cost' => 'required|numeric|min:0',
            'shrink_wrap_cost' => 'required|numeric|min:0',
            'packaging_cost' => 'required|numeric|min:0',
            'freight_cost' => 'required|numeric|min:0',
            'warehouse_cost' => 'required|numeric|min:0',
            'overhead_cost' => 'required|numeric|min:0',
        ]);

        $jobNum = 'JOB-COST-' . date('Ym') . '-' . rand(1000, 9999);

        $costing = new ProductionCosting($request->only([
            'book_id', 'job_title', 'quantity_produced', 'pages_count',
            'paper_cost', 'ink_cost', 'labor_cost', 'electricity_cost', 'machine_cost',
            'binding_cost', 'uv_cost', 'shrink_wrap_cost', 'packaging_cost', 'freight_cost',
            'warehouse_cost', 'overhead_cost', 'notes'
        ]));

        $costing->job_number = $jobNum;
        $costing->recalculateTotals();
        $costing->save();

        app(\App\Services\ProductionErpIntegrationService::class)->syncToExpenseTable($costing);

        return redirect()->route('production.costing.show', $costing->id)
            ->with('success', 'Production Costing profile saved and recorded in Expenses!');
    }

    public function syncFromProductionErp(\App\Services\ProductionErpIntegrationService $service)
    {
        $result = $service->syncCostings();

        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message']);
        }
    }
}
