<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $projects = Project::all();

        return response()->json([
            'success' => true,
            'count' => count($projects),
            'data' => $projects
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        $project = Project::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $project = Project::with('tasks')->find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $project
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $project->name = $request->name;
        $project->description = $request->description;
        $project->due_date = $request->due_date;
        $project->save();

        return response()->json([
            'message' => 'Project updated successfully',
            'data' => $project
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ], Response::HTTP_NO_CONTENT);
    }
}
