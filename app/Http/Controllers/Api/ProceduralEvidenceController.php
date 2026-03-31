<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProceduralEvidence;

class ProceduralEvidenceController extends Controller
{
    public function index()
    {
        return ProceduralEvidence::latest()->paginate(10);
    }

    public function show($id)
    {
        return ProceduralEvidence::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|unique:procedural_evidences',
            'title' => 'required|string',
            'slug' => 'required|unique:procedural_evidences',
        ]);

        return ProceduralEvidence::create($data);
    }

    public function update(Request $request, $id)
    {
        $item = ProceduralEvidence::findOrFail($id);

        $item->update($request->all());

        return $item;
    }

    public function destroy($id)
    {
        ProceduralEvidence::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}