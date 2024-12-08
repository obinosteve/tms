<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container-fluid mt-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-md-6 mb-4 mt-4">
                                <div class="card shadow h-100 py-2 bg-gray-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-l font-weight-bold text-danger text-uppercase mb-1">
                                                    Total Assigned Tasks</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ number_format($tasks->total) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4 mt-4">
                                <div class="card border-left-success shadow h-100 py-2 bg-gray-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-l font-weight-bold text-success text-uppercase mb-1">
                                                    Total Pending Tasks</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ number_format($tasks->total_pending) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4 mt-4">
                                <div class="card border-left-info shadow h-100 py-2 bg-gray-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-l font-weight-bold text-info text-uppercase mb-1">
                                                    Total Completed Tasks
                                                </div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                            {{ number_format($tasks->total_completed) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Top 5 Pending Tasks
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-stripped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Assigned To</th>
                                            <th>Title</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Completed At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($recent_tasks->count())
                                            @foreach ($recent_tasks as $index => $task)
                                                <tr>
                                                    <td>{{ $index + 1 }}.</td>
                                                    <td>{{ ucwords($task->assignedTo()) }}</td>
                                                    <td>{{ $task->title }}</td>
                                                    <td>
                                                        <strong>{{ $task->getDueDate() }}</strong>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $task->isCompleted() ? 'success' : 'warning' }}">
                                                            {{ ucfirst($task->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $task->getCompletedDate() }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('tasks.show', $task) }}"
                                                                class="btn btn-sm btn-primary">View</a>
                                                            <a href="{{ route('tasks.edit', $task) }}"
                                                                class="btn btn-sm btn-secondary">Edit</a>
                                                            <form method="POST"
                                                                action="{{ route('tasks.destroy', $task) }}"
                                                                onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    style="border-radius: 0;">Delete</button>
                                                            </form>
                                                            @if ($task->isCompleted())
                                                                <form method="POST"
                                                                    action="{{ route('tasks.status', $task) }}"
                                                                    onsubmit="return confirm('Are you sure you want to mark this task as pending?')">
                                                                    <input type="hidden" name="status"
                                                                        value="pending">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-success"
                                                                        style="border-top-left-radius:0;border-bottom-left-radius:0;width:113.98px;">Mark
                                                                        Pending</button>
                                                                </form>
                                                            @else
                                                                <form method="POST"
                                                                    action="{{ route('tasks.status', $task) }}"
                                                                    onsubmit="return confirm('Are you sure you want to mark this task as completed?')">
                                                                    <input type="hidden" name="status"
                                                                        value="completed">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-warning"
                                                                        style="border-top-left-radius:0;border-bottom-left-radius:0;width:113.98px;">Mark
                                                                        Complete</button>
                                                                </form>
                                                            @endif

                                                        </div>
                                                    </td>
                                                </tr>
                                                </tr>
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
