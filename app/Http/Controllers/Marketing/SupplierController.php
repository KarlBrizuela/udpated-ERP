<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->get();
        return view('marketing.suppliers', [
            'suppliers' => $suppliers,
            'title' => 'Supplier Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
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
            'supplier_code' => 'required|unique:suppliers,supplier_code',
            'company_name' => 'required',
            'contact_person' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        Supplier::create($validated);

        return response()->json(['message' => 'Supplier added successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|unique:suppliers,supplier_code,' . $supplier->id,
            'company_name' => 'required',
            'contact_person' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $supplier->update($validated);

        return response()->json(['message' => 'Supplier updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
