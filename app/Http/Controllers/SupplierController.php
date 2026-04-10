<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(5);
        return view('suppliers.index', compact('suppliers'));
    }

    public function show($id)
    {
        $supplier = Supplier::with([
            'layups.layers'
        ])->findOrFail($id);

        return view('suppliers.show', compact('supplier'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'message' => 'Supplier created',
            'data' => $supplier
        ]);
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->update($request->all());

        return redirect('/suppliers')->with('success', 'Updated!');
    }

    public function destroy($id)
    {
        Supplier::destroy($id);
        return back()->with('success', 'Deleted!');
    }
}
