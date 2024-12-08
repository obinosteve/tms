<?php

namespace App\Actions;

use App\Models\Task;
use App\DataTransferObjects\TaskData;

class UpsertTaskAction
{
    public static function execute(Task $task, TaskData $taskData): Task
    {
        $task->user_id = $taskData->user->id;
        $task->title = $taskData->title;
        $task->description = $taskData->description;
        $task->due_date = $taskData->dueDate;
        $task->status = $taskData->status;

        $task->save();

        return $task;
    }
}
