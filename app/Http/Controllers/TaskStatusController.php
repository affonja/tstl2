<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStatusRequest;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Nette\Schema\ValidationException;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taskStatuses = TaskStatus::all();
        return view('taskStatuses.index', compact('taskStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $taskStatus = new TaskStatus();
        return view('taskStatuses.create', compact('taskStatus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStatusRequest $request)
    {
        $taskStatus = new TaskStatus();
        $this->saveTaskStatus($taskStatus, $request);
        flash('Статус успешно создан')->success();
        return redirect()->route('task_statuses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskStatus $taskStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskStatus $taskStatus)
    {
        return view('taskStatuses.edit', compact('taskStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskStatusRequest $request, TaskStatus $taskStatus)
    {
        $this->saveTaskStatus($taskStatus, $request);
        flash('Статус успешно обновлен')->success();
        return redirect()->route('task_statuses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskStatus $taskStatus)
    {
        try {
            $taskStatus->delete();
            flash('Статус успешно удалён')->success();
            return redirect()->route('task_statuses.index');
        } catch (\Exception $e) {
            flash('Не удалось удалить статус')->error();
            return redirect()->back()->withInput();
        }
    }

    private function saveTaskStatus(TaskStatus $taskStatus, TaskStatusRequest $request)
    {
        $validated = $request->validated();
        $taskStatus->fill($validated);
        $taskStatus->save();
    }
}
