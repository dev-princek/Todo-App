<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:tasks,name',
        ]);

        $task = Task::create([
            'name' => $request->name,
            'status' => false,
        ]);
        return response()->json($task);
    }

    public function complete($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->status = true;
            $task->save();
            return response()->json($task);
        }

        return response()->json(['error' => 'Task not found'], 404);
    }

    public function delete($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->delete();
            return response()->json(['success' => 'Task deleted']);
        }

        return response()->json(['error' => 'Task not found'], 404);
    }

    public function showAll()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
