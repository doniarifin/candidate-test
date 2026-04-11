<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layup;

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
}
