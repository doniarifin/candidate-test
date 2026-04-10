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
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50' . $id,

            'grade' => 'nullable|string|max:255',
            'revision' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',
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

        $layups = Layup::with(['layers', 'supplier'])
            ->whereIn('id', $request->ids)
            ->get();

        $data = $layups->map(function ($layup) {
            return [
                'id' => $layup->id,
                'name' => $layup->name,
                'code' => $layup->code,
                'grade' => $layup->grade,
                'revision' => $layup->revision,
                'status' => $layup->status,

                // supplier info
                'supplier' => [
                    'id' => $layup->supplier?->id,
                    'name' => $layup->supplier?->name,
                    'code' => $layup->supplier?->code,
                ],

                // layers
                'layers' => $layup->layers->map(function ($layer) {
                    return [
                        'layer_order' => $layer->layer_order,
                        'thickness' => $layer->thickness,
                        'width' => $layer->width,
                    ];
                })
            ];
        });

        return response()->json($data);
    }
}
