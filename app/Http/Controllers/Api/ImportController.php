<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Layup;
use App\Models\Layer;

class ImportController extends Controller
{
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

            if (!$layup) continue;

            // get existing layer
            $layerOrders = array_column($row['layers'], 'layer_order');

            $existingLayers = $layup->layers()
                ->whereIn('layer_order', $layerOrders)
                ->get()
                ->keyBy('layer_order');

            // foreach ($row['layers'] as $layer) {

            //     $existingLayer = $existingLayers[$layer['layer_order']] ?? null;

            //     if ($existingLayer) {

            //         $diff = [];

            //         if ($existingLayer->thickness != $layer['thickness']) {
            //             $diff[] = 'thickness';
            //         }

            //         if ($existingLayer->width != $layer['width']) {
            //             $diff[] = 'width';
            //         }

            //         if ($existingLayer->angle != $layer['angle']) {
            //             $diff[] = 'angle';
            //         }

            //         // kalau diifernt -> conflict
            //         if (!empty($diff)) {
            //             $conflicts[] = [
            //                 'name' => $row['name'],
            //                 'layup_code' => $row['code'],
            //                 'layer_order' => $layer['layer_order'],
            //                 'diff_fields' => $diff,

            //                 'existing' => [
            //                     'thickness' => $existingLayer->thickness,
            //                     'width' => $existingLayer->width,
            //                     'angle' => $existingLayer->angle,
            //                 ],

            //                 'incoming' => [
            //                     'thickness' => $layer['thickness'],
            //                     'width' => $layer['width'],
            //                     'angle' => $layer['angle'],
            //                 ],
            //             ];
            //         }
            //     }
            // }

            $layerOrders = array_column($row['layers'], 'layer_order');

            $existingLayers = $layup->layers()
                ->whereIn('layer_order', $layerOrders)
                ->get()
                ->keyBy('layer_order');

            $existingArr = [];
            $incomingArr = [];
            $allDiffFields = [];
            $hasConflict = false;
            $conflictOrders = [];

            foreach ($row['layers'] as $layer) {

                $order = $layer['layer_order'];
                $existingLayer = $existingLayers[$layer['layer_order']] ?? null;

                
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

                    // cek 
                    // if (
                    //     (float)$existingLayer->thickness !== (float)$layer['thickness'] ||
                    //     (float)$existingLayer->width !== (float)$layer['width'] ||
                    //     (float)$existingLayer->angle !== (float)$layer['angle']
                    // ) {
                    //     $hasConflict = true;
                    // }

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

                // if (count($uniqueDiff) === 1) {
                //     $issue = ucfirst($uniqueDiff[0]) . ' mismatch';
                // } else {
                //     $issue = $difString . ' - mismatch';
                // }

                $conflicts[] = [
                    'id' => $layup->id,
                    'name' => $row['name'],
                    'code' => $row['code'],
                    'supplier_id' => $row['supplier_id'],
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
        DB::beginTransaction();

        try {

            if (!$request->has('data') || empty($request->data)) {
                return response()->json([
                    'message' => 'Template tidak sesuai / data tidak ditemukan'
                ], 422);
            }

            if ($request->action == "skip") {

                foreach ($request->data as $item) {

                    $layup = Layup::where('supplier_id', $item['supplier_id'])
                        ->where('name', $item['name'])
                        ->first();

                    if (!$layup) {
                        $layup = Layup::create([
                            'supplier_id' => $item['supplier_id'],
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
    
                    $layup = Layup::where('supplier_id', $item['supplier_id'])
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
                            'supplier_id' => $item['supplier_id'],
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
    
                    $layup = Layup::where('supplier_id', $item['supplier_id'])
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
                            'supplier_id' => $item['supplier_id'],
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
                        Layup::where('supplier_id', $item['supplier_id'])
                            ->where('name', $newName)
                            ->exists()
                    ) {
                        $newName = $baseName . " (imported $counter)";
                        $counter++;
                    }

                    $layup = Layup::create([
                        'supplier_id' => $item['supplier_id'],
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
    
                    $layup = Layup::where('supplier_id', $item['supplier_id'])
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
                            'supplier_id' => $item['supplier_id'],
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
