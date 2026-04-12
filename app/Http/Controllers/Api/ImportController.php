<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Layup;
use App\Models\Layer;

class ImportController extends Controller
{

    public function download()
    {
        $path = storage_path('app/public/json_template/template.json');

        return response()->download($path);
    }
    //
    public function preview(Request $request)
    {
        $file = $request->file('file');
        $supplierId = $request->supplier_id;

        $data = [];

        // csv
        if ($file->getClientOriginalExtension() === 'csv') {
            $rows = array_map('str_getcsv', file($file));

            $header = array_map('strtolower', $rows[0]);
            unset($rows[0]);

            foreach ($rows as $row) {
                $data[] = array_combine($header, $row);
            }
        }

        // json
        if ($file->getClientOriginalExtension() === 'json') {
            $data = json_decode(file_get_contents($file), true);
        }

        $conflicts = [];

        if (!is_array($data)) {
            return response()->json([
                'message' => 'Template tidak sesuai (data bukan array)'
            ], 422);
        }

        foreach ($data as $row) {

            if (!isset($row['layers']) || !is_array($row['layers'])) {
                return response()->json([
                    'message' => "Template tidak sesuai: layers harus array (row {$index})"
                ], 422);
            }

            if (!isset($row['name'], $row['code'])) {
                return response()->json([
                    'message' => "Template tidak sesuai: name/code missing (row {$index})"
                ], 422);
            }

            // get lyup
            // $layup = Layup::where('name', $row['name'], 'supplier', )->first();
            $layup = Layup::where('name', $row['name'])
                ->where('supplier_id', $supplierId)
                ->first();

            $existingLayers = collect();
            $layerOrders = array_column($row['layers'], 'layer_order');

            if ($layup) {

                $existingLayers = $layup->layers()
                    ->whereIn('layer_order', $layerOrders)
                    ->get()
                    ->keyBy('layer_order');
            };

            // get existing layer
            

            $existingArr = [];
            $incomingArr = [];
            $allDiffFields = [];
            $hasConflict = false;
            $conflictOrders = [];

            foreach ($row['layers'] as $layer) {

                $order = $layer['layer_order'];

                $existingLayer = $existingLayers[$order] ?? null;
                // $existingLayer = $existingLayers[$layer['layer_order']] ?? null;

                
                $incomingArr[] = [
                    'layer_order' => $order,
                    'thickness' => (float)$layer['thickness'],
                    'width' => (float)$layer['width'],
                    'angle' => (float)$layer['angle'],
                    'grade' => $layer['grade'],
                ];

                if ($existingLayer) {

                    $existingArr[] = [
                        'layer_order' => $order,
                        'thickness' => (float)$existingLayer->thickness,
                        'width' => (float)$existingLayer->width,
                        'angle' => (float)$existingLayer->angle,
                        'grade' => $existingLayer->angle,
                    ];

                    $diff = [];

                    if ((float)$existingLayer->thickness !== (float)$layer['thickness']) {
                        $diff[] = 'thickness';
                    }

                    if ((float)$existingLayer->width !== (float)$layer['width']) {
                        $diff[] = 'width';
                    }

                    if ((float)$existingLayer->angle !== (float)$layer['angle']) {
                        $diff[] = 'angle';
                    }

                    if (!empty($diff)) {
                        $hasConflict = true;
                        $allDiffFields = array_merge($allDiffFields, $diff);
                        $conflictOrders[] = $order;
                    }

                } else {
                    $existingArr[] = null;
                }
            }

            if ($hasConflict) {
                $uniqueDiff = array_unique($allDiffFields);
                $difString = implode(', ', $uniqueDiff);
                $uniqueOrders = array_unique($conflictOrders);

                 $fieldsText = collect($uniqueDiff)
                    ->map(fn($f) => ucfirst($f))
                    ->join(', ');

                $ordersText = implode(', ', $uniqueOrders);

                $issue = "{$fieldsText} mismatch on layer {$ordersText}";

                $conflicts[] = [
                    'id' => $layup->id,
                    'name' => $row['name'],
                    'code' => $row['code'],
                    'supplier_id' => $supplierId,
                    'issue' => $issue,

                    'existing' => $existingArr,
                    'importing' => $incomingArr,
                ];
            }
        }

        return response()->json([
            'data' => $data,
            'conflicts' => $conflicts,
            'existingLayers' => $existingLayers,
            'layerOrders' => $layerOrders,
            'layup' => $layup,
        ]);
    }

    public function import(Request $request)
    {

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::beginTransaction();

        try {

            if (!$request->has('data') || empty($request->data)) {
                return response()->json([
                    'message' => 'Template tidak sesuai / data tidak ditemukan'
                ], 422);
            }

            if ($request->action == "skip") {

                foreach ($request->data as $item) {

                    $layup = Layup::where('supplier_id', $request->supplier_id)
                        ->where('name', $item['name'])
                        ->first();

                    if (!$layup) {
                        $layup = Layup::create([
                            'supplier_id' => $request->supplier_id,
                            'name'        => $item['name'],
                            'code'        => $item['code'],
                            'grade'       => $item['grade'] ?? null,
                            'revision'    => $item['revision'] ?? null,
                            'status'      => $item['status'] ?? 'draft',
                        ]);
                    }

                    foreach ($item['layers'] as $layer) {

                        $existingLayer = Layer::where('layup_id', $layup->id)
                            ->where('layer_order', $layer['layer_order'])
                            ->first();

                        if (!$existingLayer) {
                            Layer::create([
                                'layup_id'    => $layup->id,
                                'layer_order' => $layer['layer_order'],
                                'thickness'   => $layer['thickness'],
                                'width'       => $layer['width'],
                                'angle'       => $layer['angle'],
                                'grade'       => $layer['grade'] ?? null,
                            ]);
                        }
                    }
                }
            }

        
            if ($request->action == "overwrite") {
                foreach ($request->data as $item) {
    
                    $layup = Layup::where('supplier_id', $request->supplier_id)
                        ->where('name', $item['name'])
                        ->first();
    
                    if ($layup) {
                        $layup->update([
                            'code'     => $item['code'],
                            'grade'    => $item['grade'] ?? null,
                            'revision' => $item['revision'] ?? null,
                            'status'   => $item['status'] ?? 'draft',
                        ]);
                    } else {
                        $layup = Layup::create([
                            'supplier_id' => $request->supplier_id,
                            'name'        => $item['name'],
                            'code'        => $item['code'],
                            'grade'       => $item['grade'] ?? null,
                            'revision'    => $item['revision'] ?? null,
                            'status'      => $item['status'] ?? 'draft',
                        ]);
                    }
    
                    foreach ($item['layers'] as $layer) {
    
                        $existingLayer = Layer::where('layup_id', $layup->id)
                            ->where('layer_order', $layer['layer_order'])
                            ->first();
    
                        if ($existingLayer) {
                            $existingLayer->update([
                                'thickness' => $layer['thickness'],
                                'width'     => $layer['width'],
                                'angle'     => $layer['angle'],
                                'grade'     => $layer['grade'] ?? null,
                            ]);
                        } else {
                            Layer::create([
                                'layup_id'    => $layup->id,
                                'layer_order' => $layer['layer_order'],
                                'thickness'   => $layer['thickness'],
                                'width'       => $layer['width'],
                                'angle'       => $layer['angle'],
                                'grade'       => $layer['grade'] ?? null,
                            ]);
                        }
                    }
                }
            }

            if ($request->action == "overwrite") {
                foreach ($request->data as $item) {
    
                    $layup = Layup::where('supplier_id', $request->supplier_id)
                        ->where('name', $item['name'])
                        ->first();
    
                    if ($layup) {
                        $layup->update([
                            'code'     => $item['code'],
                            'grade'    => $item['grade'] ?? null,
                            'revision' => $item['revision'] ?? null,
                            'status'   => $item['status'] ?? 'draft',
                        ]);
                    } else {
                        $layup = Layup::create([
                            'supplier_id' => $request->supplier_id,
                            'name'        => $item['name'],
                            'code'        => $item['code'],
                            'grade'       => $item['grade'] ?? null,
                            'revision'    => $item['revision'] ?? null,
                            'status'      => $item['status'] ?? 'draft',
                        ]);
                    }
    
                    foreach ($item['layers'] as $layer) {
    
                        $existingLayer = Layer::where('layup_id', $layup->id)
                            ->where('layer_order', $layer['layer_order'])
                            ->first();
    
                        if ($existingLayer) {
                            $existingLayer->update([
                                'thickness' => $layer['thickness'],
                                'width'     => $layer['width'],
                                'angle'     => $layer['angle'],
                                'grade'     => $layer['grade'] ?? null,
                            ]);
                        } else {
                            Layer::create([
                                'layup_id'    => $layup->id,
                                'layer_order' => $layer['layer_order'],
                                'thickness'   => $layer['thickness'],
                                'width'       => $layer['width'],
                                'angle'       => $layer['angle'],
                                'grade'       => $layer['grade'] ?? null,
                            ]);
                        }
                    }
                }
            }

            if ($request->action == "duplicate") {

                foreach ($request->data as $item) {
                    $baseName = preg_replace('/\s\(imported(?:\s\d+)?\)$/', '', $item['name']);

                    $newName = $baseName . ' (imported)';
                    $counter = 1;

                    while (
                        Layup::where('supplier_id', $request->supplier_id)
                            ->where('name', $newName)
                            ->exists()
                    ) {
                        $newName = $baseName . " (imported $counter)";
                        $counter++;
                    }

                    $layup = Layup::create([
                        'supplier_id' => $request->supplier_id,
                        'name'        => $newName,
                        'code'        => $item['code'],
                        'grade'       => $item['grade'] ?? null,
                        'revision'    => $item['revision'] ?? null,
                        'status'      => $item['status'] ?? 'draft',
                    ]);

                    //copy
                    foreach ($item['layers'] as $layer) {
                        Layer::create([
                            'layup_id'    => $layup->id,
                            'layer_order' => $layer['layer_order'],
                            'thickness'   => $layer['thickness'],
                            'width'       => $layer['width'],
                            'angle'       => $layer['angle'],
                            'grade'       => $layer['grade'] ?? null,
                        ]);
                    }
                }
            }

            if ($request->action == "reject") {

                foreach ($request->newData as $item) {
    
                    $layup = Layup::where('supplier_id', $request->supplier_id)
                        ->where('name', $item['name'])
                        ->first();
    
                    if ($layup) {
                        $layup->update([
                            'code'     => $item['code'],
                            'grade'    => $item['grade'] ?? null,
                            'revision' => $item['revision'] ?? null,
                            'status'   => $item['status'] ?? 'draft',
                        ]);
                    } else {
                        $layup = Layup::create([
                            'supplier_id' => $request->supplier_id,
                            'name'        => $item['name'],
                            'code'        => $item['code'],
                            'grade'       => $item['grade'] ?? null,
                            'revision'    => $item['revision'] ?? null,
                            'status'      => $item['status'] ?? 'draft',
                        ]);
                    }
    
                    foreach ($item['layers'] as $layer) {
    
                        $existingLayer = Layer::where('layup_id', $layup->id)
                            ->where('layer_order', $layer['layer_order'])
                            ->first();
    
                        if ($existingLayer) {
                            $existingLayer->update([
                                'thickness' => $layer['thickness'],
                                'width'     => $layer['width'],
                                'angle'     => $layer['angle'],
                                'grade'     => $layer['grade'] ?? null,
                            ]);
                        } else {
                            Layer::create([
                                'layup_id'    => $layup->id,
                                'layer_order' => $layer['layer_order'],
                                'thickness'   => $layer['thickness'],
                                'width'       => $layer['width'],
                                'angle'       => $layer['angle'],
                                'grade'       => $layer['grade'] ?? null,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Success']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

        // return response()->json(['message' => 'Import success']);
    }
}
