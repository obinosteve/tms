<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="table-responsive">
                    <table class="table table-stripped">
                        <tbody>
                            <tr>
                                <th>Assigned To</th>
                                <th>{{ ucwords($task->assignedTo()) }}</th>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <th>{{ ucwords($task->title) }}</th>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <th>{{ $task->description }}</th>
                            </tr>
                            <tr>
                                <th>Due Date</th>
                                <th>{{ $task->getDueDate() }}</th>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <th>
                                    <span class="badge bg-{{ $task->isCompleted() ? 'success' : 'warning' }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </th>
                            </tr>
                            <tr>
                                <th>Completed Date</th>
                                <th>{{ $task->getCompletedDate() }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
