<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use Illuminate\Http\Response;
use App\Actions\ListTasksAction;
use App\Actions\CreateTaskAction;
use App\Actions\DeleteTaskAction;
use App\Actions\UpdateTaskAction;
use Illuminate\Contracts\View\View;
use App\DataTransferObjects\TaskData;
use Illuminate\Http\RedirectResponse;
use App\Actions\UpdateTaskStatusAction;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('tasks.index', [
            'tasks' => ListTasksAction::execute(request()->all())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskRequest $request): RedirectResponse
    {
        try {
            $data = TaskData::fromRequest($request->all());

            CreateTaskAction::execute($data);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return back()->with('success', 'Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        abort_if(!$task->owner(), Response::HTTP_FORBIDDEN, 'You can only see your task');

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        abort_if(!$task->owner(), Response::HTTP_FORBIDDEN, 'You can only edit your task');

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        abort_if(!$task->owner(), Response::HTTP_FORBIDDEN, 'You can only edit your task');

        try {
            $data = TaskData::fromRequest($request->all());

            UpdateTaskAction::execute($data, $task);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        abort_if(!$task->owner(), Response::HTTP_FORBIDDEN, 'You can only update your task status');

        try {
            UpdateTaskStatusAction::execute($request->status, $task);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('tasks.index')->with('success', 'Task status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        abort_if(!$task->owner(), Response::HTTP_FORBIDDEN, 'You can only delete your task');

        try {
            DeleteTaskAction::execute($task);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Task deleted!');
    }
}
