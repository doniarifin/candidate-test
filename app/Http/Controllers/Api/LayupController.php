<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layup;

class LayupController extends Controller
{
    // 
    public function index()
    {
        $layups = Layup::withCount('layers')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $layups
        ]);
    }

    public function show($id)
    {
        $layup = Layup::with([
            'layers'
        ])->findOrFail($id);

        return response()->json($layup);
    }

    // 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',

            'grade' => 'nullable|string|max:255',
            'revision' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',
        ]);

        $layup = Layup::create($validated);

        return response()->json([
            'message' => 'Layup berhasil dibuat',
            'data' => $layup
        ], 201);
    }

    //
    public function update(Request $request, $id)
    {
        $layups = Layup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:ss,code,' . $id,

            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $layups->update($validated);

        return response()->json([
            'message' => 'layup berhasil diupdate',
            'data' => $layups
        ]);
    }

    // 
    public function destroy($id)
    {
        $layups = Layup::findOrFail($id);
        $layups->delete();

        return response()->json([
            'message' => 'layups berhasil dihapus'
        ]);
    }

    //
    public function export(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:layups,id'
        ]);

        $layups = Layup::with('layers')
            ->whereIn('id', $request->ids)
            ->get();

        $data = $layups->map(function ($supplier) {
            return [
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'code' => $supplier->code,
                    'email' => $supplier->email,
                    'location' => $supplier->location,
                    'certifications' => $supplier->certifications,
                    'status' => $supplier->status,
                ],
                'layups' => $supplier->layups->map(function ($layup) {
                    return [
                        'id' => $layup->id,
                        'name' => $layup->name,
                        'code' => $layup->code,
                        'layers' => $layup->layers->map(function ($layer) {
                            return [
                                'layer_order' => $layer->layer_order,
                                'thickness' => $layer->thickness,
                                'width' => $layer->width,
                                // 'angle' => $layer->angle,
                            ];
                        })
                    ];
                })
            ];
        });

        return response()->json($data);
    }
}
