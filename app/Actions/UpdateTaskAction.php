<?php

namespace App\Actions;

use App\Models\Task;
use Illuminate\Http\Response;
use App\DataTransferObjects\TaskData;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateTaskAction
{
    public static function execute(TaskData $taskData, Task $task): Task
    {
        throw_if(
            !$task instanceof Task,
            new ModelNotFoundException('Task not found', Response::HTTP_NOT_FOUND)
        );

        // Update the task
        $task = UpsertTaskAction::execute($task, $taskData);

        return $task;
    }
}
