<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\TaskResource;


class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::query()->with(['assignee', 'creator', 'dependencies']);
        $tasks = $query->paginate(10);
       
        // Filter by assigned user (for managers)
        if ($request->has('assigned_to') && $user->hasRole('manager')) {
            $query->where('assigned_to', $request->assigned_to);
        } elseif (!$user->hasRole('manager')) {
            // Regular users can only see their assigned tasks
            $query->where('assigned_to', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by due date range
        if ($request->has('due_date_start') && $request->has('due_date_end')) {
            $query->whereBetween('due_date', [$request->due_date_start, $request->due_date_end]);
        } elseif ($request->has('due_date_start')) {
            $query->where('due_date', '>=', $request->due_date_start);
        } elseif ($request->has('due_date_end')) {
            $query->where('due_date', '<=', $request->due_date_end);
        }

      return response()->json([
            'success' => true,
            'data' => $tasks
        ]);

        
    }
  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
       // dd($user);
       // dd($user->getAllPermissions());
        //dd($user->hasPermissionTo('create-tasks', 'api'));
        
        // Check if user has permission to create tasks
        if (!$user->can('create-tasks')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create tasks'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed,canceled',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the task
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'created_by' => $user->id,
            'status' => $request->status ?? 'pending',
            'due_date' => $request->due_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $task = Task::with(['assignee', 'creator', 'dependencies'])->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        // Check if user is allowed to view this task
        if (!$user->hasRole('manager') && $task->assigned_to !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this task'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
      //  dd($user->getAllPermissions());
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        // If user is not a manager, they can only update the status
        if (!$user->hasRole('manager')) {
            if ($task->assigned_to !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this task'
                ], 403);
            }

            // For regular users, only validate and update status
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed,canceled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // If trying to mark as completed, check dependencies
            if ($request->status === 'completed' && !$task->canBeCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task cannot be completed until all dependencies are completed'
                ], 422);
            }

            $task->update(['status' => $request->status]);
        } else {
            // For managers, validate all fields
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'status' => 'sometimes|required|in:pending,in_progress,completed,canceled',
                'due_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // If trying to mark as completed, check dependencies
            if (isset($request->status) && $request->status === 'completed' && !$task->canBeCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task cannot be completed until all dependencies are completed'
                ], 422);
            }

            $task->update($request->only([
                'title', 'description', 'assigned_to', 'status', 'due_date'
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
     //   dd($user->hasPermissionTo('delete-tasks', 'api'));
        // Only managers can delete tasks
        if (!$user->can('delete-tasks')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete tasks'
            ], 403);
        }

        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }
    
     /**
     * Add task dependencies
     */
    public function addDependency(Request $request, $id)
    {
        $user = Auth::user();
        
        // Only managers can add dependencies
        if (!$user->hasRole('manager')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to add dependencies'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'dependency_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::find($id);
        $dependencyTask = Task::find($request->dependency_id);

        if (!$task || !$dependencyTask) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        // Check if dependency would create a circular reference
        if ($id == $request->dependency_id) {
            return response()->json([
                'success' => false,
                'message' => 'A task cannot depend on itself'
            ], 422);
        }

        // Attach dependency if not already attached
        if (!$task->dependencies()->where('dependency_id', $request->dependency_id)->exists()) {
            $task->dependencies()->attach($request->dependency_id);
            return response()->json([
                'success' => true,
                'message' => 'Dependency added successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Dependency already exists'
        ], 422);
    }

    /**
     * Remove task dependency
     */
    public function removeDependency(Request $request, $id)
    {
        $user = Auth::user();
        
        // Only managers can remove dependencies
        if (!$user->hasRole('manager')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to remove dependencies'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'dependency_id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $task->dependencies()->detach($request->dependency_id);

        return response()->json([
            'success' => true,
            'message' => 'Dependency removed successfully'
        ]);
    }
     /*** bador
     * Check if a task can be marked as completed based on its dependencies.
     */
    public function canBeCompleted(Task $task)
    {
        foreach ($task->dependencies as $dependency) {
            if (!$dependency->canBeCompleted()) {
                return false;
            }
        }

        return true;
    }



}
