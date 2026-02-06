<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::with('project')->whereUserId(auth()->id())->get();

        return response()->json([
            'success' => true,
            'count' => count($tasks),
            'data' => $tasks
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'due_date' => $request->due_date,
            'user_id' => auth()->id(),
        ];
        $task = Task::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' =>  $task
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id): JsonResponse
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $task->project_id = $request->project_id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->user_id = auth()->id();
        $task->save();

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ], Response::HTTP_NO_CONTENT);
    }
}
