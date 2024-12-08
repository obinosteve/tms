<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'tasks' => $this->getTaskStatistics(),
            'recent_tasks' => $this->getRecentPendingTasks(),
        ]);
    }

    protected function getTaskStatistics(): Object
    {
        return DB::table('tasks')
            ->selectRaw("count(*) as total")
            ->selectRaw("count(case when status = ? then 1 end) as total_pending", [Status::PENDING->value])
            ->selectRaw("count(case when status = ? then 1 end) as total_completed", [Status::COMPLETED->value])
            ->first();
    }

    protected function getRecentPendingTasks()
    {
        return Task::query()
            ->where('status', Status::PENDING->value)
            ->latest()
            ->take(5)
            ->get();
    }
}
