<?php

namespace App\Actions;

use App\Models\Task;
use App\DataTransferObjects\TaskData;

class CreateTaskAction
{
    public static function execute(TaskData $taskData): Task
    {
        return UpsertTaskAction::execute(new Task, $taskData);
    }
}
