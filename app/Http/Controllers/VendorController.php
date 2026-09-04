<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::orderBy('created_at', 'desc')->get();

        return view('vendor-management.index', [
            'title'   => 'Vendor Management',
            'role'    => auth()->user()->role ?? 'Staff',
            'sidebar' => 'supplier-management',
            'vendors' => $vendors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name'    => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'status'         => 'required|in:active,inactive',
        ]);

        $validated['vendor_code'] = Vendor::generateVendorCode();

        $vendor = Vendor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor created successfully.',
            'vendor'  => $vendor,
        ]);
    }

    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json($vendor);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'vendor_name'    => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'status'         => 'required|in:active,inactive',
        ]);

        $vendor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully.',
            'vendor'  => $vendor,
        ]);
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully.',
        ]);
    }
}
