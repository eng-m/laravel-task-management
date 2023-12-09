<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class TaskController extends Controller
{
    // Display a listing of tasks
    public function index()
    {
        try {

            $tasks = Task::with('user')->select(["id", 'title', 'description', 'due_date', 'status'])->get();

            return TaskResource::collection($tasks);
        } catch (Exception $e) {
            \Log::error('Error retrieving tasks: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving tasks'], 500);
        }
    }

    // Store a newly created task
    public function store(TaskRequest $request)
    {
        try {
            $task = $request->user()->tasks()->create($request->validated());
            return new TaskResource($task);
        } catch (Exception $e) {
            \Log::error('Error creating task: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating task'], 500);
        }
    }

    // Display the specified task
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    // Update the specified task
    public function update(TaskRequest $request, Task $task)
    {
        try {
            if ($request->user()->id !== $task->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $task->update($request->validated());
            return new TaskResource($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found'], 404);
        } catch (Exception $e) {
            \Log::error('Error updating task: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating task'], 500);
        }
    }

    // Remove the specified task
    public function destroy(Request $request, Task $task)
    {
        try {
            if ($request->user()->id !== $task->user_id && $request->user()->role !== 'employer') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $task->delete();
            return response()->json(['message' => 'Task deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found'], 404);
        } catch (Exception $e) {
            \Log::error('Error deleting task: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting task'], 500);
        }
    }
}
