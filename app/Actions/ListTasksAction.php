<?php

namespace App\Actions;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListTasksAction
{
    public static function execute(array $params): LengthAwarePaginator
    {
        $dueDate = isset($params['dueDate']) ? $params['dueDate'] : null;
        return Task::query()
            ->with('user')
            ->where('user_id', auth()->id())
            ->when(
                !empty($params['status']),
                fn($builder) => $builder->where('status', $params['status'])
            )
            ->when(
                $dueDate = GetDueDate($dueDate),
                fn($builder) => $builder->whereDate('due_date', $dueDate)
            )
            ->latest()
            ->paginate(25);
    }
}
