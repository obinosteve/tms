<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-end">
                <a href="{{ route('tasks.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">New
                    Task</a>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="GET" action="{{ route('tasks.index') }}" class="flex gap-3 mb-6">
                    <div>
                        <x-input-label for="title" :value="__('Status')" />
                        <x-select-input id="status" name="status" class="mt-1 block w-full">
                            <option value="">--select--</option>
                            <option value="pending" @selected(request()->status == 'pending')>Pending</option>
                            <option value="completed" @selected(request()->status == 'completed')>Completed</option>
                        </x-select-input>
                    </div>
                    <div>
                        <x-input-label for="datepicker" :value="__('Due Date')" />
                        <x-text-input id="datepicker" name="dueDate" type="text" class="mt-1 block w-full"
                            :value="request()->dueDate ?? null" placeholder="--select--" />
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-secondary">Search</button>
                    </div>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Assigned To</th>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th class="text-center">Status</th>
                                <th>Completed At</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S/N</th>
                                <th>Assigned To</th>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th class="text-center">Status</th>
                                <th>Completed At</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($tasks as $index => $task)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ ucwords($task->assignedTo()) }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <strong>{{ $task->getDueDate() }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $task->isCompleted() ? 'success' : 'warning' }}">
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
                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    style="border-radius: 0;">Delete</button>
                                            </form>
                                            @if ($task->isCompleted())
                                                <form method="POST" action="{{ route('tasks.status', $task) }}"
                                                    onsubmit="return confirm('Are you sure you want to mark this task as pending?')">
                                                    <input type="hidden" name="status" value="pending">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        style="border-top-left-radius:0;border-bottom-left-radius:0;width:113.98px;">Mark
                                                        Pending</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('tasks.status', $task) }}"
                                                    onsubmit="return confirm('Are you sure you want to mark this task as completed?')">
                                                    <input type="hidden" name="status" value="completed">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-warning"
                                                        style="border-top-left-radius:0;border-bottom-left-radius:0;width:113.98px;">Mark
                                                        Complete</button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $tasks->links() }}
        </div>
    </div>

</x-app-layout>
