<?php

namespace App\Http\Controllers\Admin\GSD;

use App\Http\Controllers\Controller;
use App\Models\Admin\GSD\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Asset::query();

        $search = $request->query('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('property_code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('checked_by', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin-finance.gsd.asset-management', [
            'title' => 'Asset Management',
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'assets' => $assets,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_code' => 'nullable|string|max:20|unique:assets,property_code',
            'category' => 'required|string',
            'description' => 'required|string',
            'acquisition_date' => 'required|date',
            'department' => 'required|string',
            'checked_by' => 'required|string|max:255',
        ]);

        $validated['status'] = 'Active';

        Asset::create($validated);

        return redirect()->back()->with('success', 'Asset recorded successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\GSD\Asset  $asset_request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Asset $asset_request)
    {
        $validated = $request->validate([
            'property_code' => 'nullable|string|max:20|unique:assets,property_code,' . $asset_request->asset_id . ',asset_id',
            'category' => 'required|string',
            'description' => 'required|string',
            'acquisition_date' => 'required|date',
            'department' => 'required|string',
            'checked_by' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        $asset_request->update($validated);

        return redirect()->back()->with('success', 'Asset updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin\GSD\Asset  $asset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Asset $asset_request)
    {
        // Note: The parameter name should match the resource name used in routes if using route model binding
        // Route::resource('/asset-requests', ...) -> $asset_request
        $asset_request->delete();

        return redirect()->back()->with('success', 'Asset record deleted successfully!');
    }
}
