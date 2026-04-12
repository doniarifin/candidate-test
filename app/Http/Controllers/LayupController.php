<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layup;
use App\Models\Supplier;

class LayupController extends Controller
{
    //
    public function index()
    {
        $layups = Layup::latest()->paginate(5);
        return view('layups.index', compact('layups'));
    }

    public function show($id)
    {
        $layup = Layup::with([
            'supplier',
            'layers'
        ])->findOrFail($id);

        return view('layups.show', compact('layup'));
    }

    // public function underSupplier(Request $request)
    // {
    //     $supplierId = $request->query('supplier_id');

    //     $supplier = Supplier::findOrFail($supplierId);

    //     $layups = Layup::with(['supplier', 'layers'])
    //         ->where('supplier_id', $supplierId)
    //         ->orderBy('id')
    //         ->paginate(5);

    //     return view('suppliers.show', compact('supplier', 'layups'));
    // }

    public function underSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplierId = $id;

        $layups = Layup::with(['supplier', 'layers'])
            ->where('supplier_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('suppliers.show', compact('supplier', 'supplierId', 'layups'));
    }
}
