<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    // 
    public function index()
    {
        $suppliers = Supplier::withCount('layups')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $suppliers
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->query('search');

        $suppliers = Supplier::withCount('layups')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return response()->json([
            'data' => $suppliers
        ]);
    }

    public function show($id)
    {
        $supplier = Supplier::with([
            'layups.layers'
        ])->findOrFail($id);

        return response()->json($supplier);
    }

    // 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',

            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'message' => 'Supplier berhasil dibuat',
            'data' => $supplier
        ], 201);
    }

    // Update supplier
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,' . $id,

            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $supplier->update($validated);

        return response()->json([
            'message' => 'Supplier berhasil diupdate',
            'data' => $supplier
        ]);
    }

    // 
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier berhasil dihapus'
        ]);
    }

    //
    public function export(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:suppliers,id'
        ]);

        $suppliers = Supplier::with('layups.layers')
            ->whereIn('id', $request->ids)
            ->get();

        $data = $suppliers->map(function ($supplier) {
            return [
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'code' => $supplier->code,
                    'email' => $supplier->email,
                    'location' => $supplier->location,
                    'certifications' => $supplier->certifications,
                    'status' => $supplier->status,
                    'created_at' => $supplier->created_at,
                    'updated_at' => $supplier->updated_at,
                ],
                'layups' => $supplier->layups->map(function ($layup) {
                    return [
                        'id' => $layup->id,
                        'name' => $layup->name,
                        'code' => $layup->code,
                        'supplier_id' => $layup->supplier_id,
                        'grade' => $layup->grade,
                        'revision' => $layup->revision,
                        'status' => $layup->status,
                        'created_at' => $layup->created_at,
                        'updated_at' => $layup->updated_at,
                        'layers' => $layup->layers->map(function ($layer) {
                            return [
                                'id' => $layer->id,
                                'layup_id' => $layer->layup_id,
                                'layer_order' => $layer->layer_order,
                                'thickness' => $layer->thickness,
                                'width' => $layer->width,
                                'angle' => $layer->angle,
                                'grade' => $layer->grade,
                                'created_at' => $layer->created_at,
                                'updated_at' => $layer->updated_at,
                            ];
                        })
                    ];
                })
            ];
        });

        return response()->json($data);
    }
}