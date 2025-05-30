<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Project::query();

        if($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if($request->has('start_date', 'end_date')) {
            $query->whereBetween('created_at', [$request->start_date , $request->end_date]);
        }
        $projects = $query->paginate(10);
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,completed',
        ]);
        $project = Project::create($request->all());
        return response()->json($project ,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);
        $validator = $request->validate([
            'name' => 'sometimes|required|string|max:255|min:3|max:100|unique:projects,name'.$id,
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
        ]);
        $project->update($request->all());
        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }
}
