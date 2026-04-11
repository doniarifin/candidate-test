<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layer;

class LayerController extends Controller
{
    //
    public function index()
    {
        $layers = layer::orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $layers
        ]);
    }

    public function show($id)
    {
        $layer = Layer::findOrFail($id);

        return response()->json($layer);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'layup_id' => 'required|exists:layups,id',
            'layer_order' => 'required|int',
            'thickness' => 'required|numeric',
            'width' => 'required|numeric',
            'angle' => 'required|numeric',

            'grade' => 'nullable|string|max:255',
        ]);

        $layer = Layer::create($validated);

        return response()->json([
            'message' => 'layer berhasil dibuat',
            'data' => $layer
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $layers = Layer::findOrFail($id);

        $validated = $request->validate([
            'layup_id' => 'required|exists:layups,id',
            // 'layer_order' => 'required|int',
            'thickness' => 'required|numeric',
            'width' => 'required|numeric',
            'angle' => 'required|numeric',

            'grade' => 'nullable|string|max:255',
        ]);

        $layers->update($validated);

        return response()->json([
            'message' => 'layer berhasil diupdate',
            'data' => $layers
        ]);
    }

    public function destroy($id)
    {
        $layers = Layer::findOrFail($id);
        $layers->delete();

        return response()->json([
            'message' => 'layers berhasil dihapus'
        ]);
    }

    //export
    public function export(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:layers,id'
        ]);

        $layers = Layer::whereIn('id', $request->ids)
            ->get();

        $data = $layers->map(function ($layer) {
            return [
                'id' => $layer->id,
                'layup_id' => $layer->layup_id,
                'layer_order' => $layer->layer_order,
                'thickness' => $layer->thickness,
                'width' => $layer->width,
                'angle' => $layer->angle,
                'grade' => $layer->grade,

                // layup info
                'layup' => [
                    'id' => $layer->layup?->id,
                    'name' => $layer->layup?->name,
                    'code' => $layer->layup?->code,
                ],

                //supplier info
                'supplier' => [
                    'id' => $layup->supplier?->id,
                    'name' => $layup->supplier?->name,
                    'code' => $layup->supplier?->code,
                ],
            ];
        });

        return response()->json($data);
    }
}
