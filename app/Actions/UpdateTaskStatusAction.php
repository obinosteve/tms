<?php

namespace App\Actions;

use App\Models\Task;
use App\Enums\Status;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateTaskStatusAction
{
    public static function execute(string $status, Task $task): Task
    {
        throw_if(
            !$task,
            new ModelNotFoundException('Task not found', Response::HTTP_NOT_FOUND)
        );

        $task->update([
            'status' => $status,
            'completed_at' => $status === Status::COMPLETED->value ? now() : null,
        ]);

        return $task;
    }
}
