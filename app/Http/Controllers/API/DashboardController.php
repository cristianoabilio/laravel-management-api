<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $userId = auth()->id();

        return response()->json([
            'success' => true,
            'projects' => Project::whereUserId($userId)->count(),
            'tasks' => Task::whereUserId($userId)->count(),
        ], Response::HTTP_OK);
    }
}
