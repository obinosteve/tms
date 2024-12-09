<?php

namespace App\Actions;

use App\Models\Task;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteTaskAction
{
    public static function execute(Task $task): void
    {
        throw_if(
            !$task instanceof Task,
            new ModelNotFoundException('Task not found', Response::HTTP_NOT_FOUND)
        );

        $task->delete();
    }
}
