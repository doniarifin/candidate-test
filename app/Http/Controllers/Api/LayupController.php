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
            'supplier',
            'layers'
        ])->findOrFail($id);

        return response()->json($layup);
    }

    public function getLayups(Request $request, $supplierId)
    {
        $layups = Layup::with('layers')
            ->where('supplier_id', $supplierId)
            ->orderBy('id')
            ->paginate(5);

        return response()->json($layups);
    }

    // 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:layups,code',

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
        $layup = Layup::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            // 'code' => 'required|string|max:50|unique:layups,code,' . $id,

            'grade' => 'nullable|string|max:255',
            'revision' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',

            'layers' => 'nullable|array',
            'layers.*.id' => 'nullable|exists:layers,id',
            'layers.*.layer_order' => 'required|integer',
            'layers.*.thickness' => 'required|numeric',
            'layers.*.width' => 'required|numeric',
            'layers.*.angle' => 'required|numeric',
            'layers.*.grade' => 'nullable|string|max:255',
        ]);

        $layup->update($validated);

        if (isset($validated['layers'])) {

            $existingIds = $layup->layers()->pluck('id')->toArray();
            $incomingIds = collect($validated['layers'])
                ->pluck('id')
                ->filter()
                ->toArray();

            $toDelete = array_diff($existingIds, $incomingIds);

            if (!empty($toDelete)) {
                $layup->layers()->whereIn('id', $toDelete)->delete();
            }

            foreach ($validated['layers'] as $index => $layer) {

                $layup->layers()->updateOrCreate(
                    ['id' => $layer['id'] ?? null],
                    [
                        'layer_order' => $index + 1,
                        'thickness' => $layer['thickness'],
                        'width' => $layer['width'],
                        'angle' => $layer['angle'],
                        'grade' => $layer['grade'] ?? null,
                    ]
                );
            }
        }

        return response()->json([
            'message' => 'layup berhasil diupdate',
            'data' => $layup->load('layers')
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
                'supplier_id' => $layup->supplier_id,
                'name' => $layup->name,
                'code' => $layup->code,
                'grade' => $layup->grade,
                'revision' => $layup->revision,
                'status' => $layup->status,
                'created_at' => $layup->created_at,
                'updated_at' => $layup->updated_at,

                // supplier info
                'supplier' => [
                    'id' => $layup->supplier?->id,
                    'name' => $layup->supplier?->name,
                    'code' => $layup->supplier?->code,
                    'email' => $layup->supplier?->email,
                    'location' => $layup->supplier?->location,
                    'certifications' => $layup->supplier?->certifications,
                    'status' => $layup->supplier?->status,
                    'created_at' => $layup->supplier?->created_at,
                    'updated_at' => $layup->supplier?->updated_at,
                ],

                // layers
                'layers' => $layup->layers->map(function ($layer) {
                    return [
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
        });

        return response()->json($data);
    }
}
