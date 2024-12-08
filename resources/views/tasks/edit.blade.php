<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <form method="post" action="{{ route('tasks.update', $task) }}" class="mt-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title', $task->title)" required autofocus autocomplete="title" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-area id="description" name="description" type="text"
                                class="mt-1 block w-full">{{ old('description', $task->description) }}</x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="datepicker" :value="__('Due Date')" />
                            <x-text-input id="datepicker" name="dueDate" type="text" class="mt-1 block w-full"
                                :value="old('dueDate', $task->formatted_due_date)" placeholder="Select a date" required />
                            <x-input-error class="mt-2" :messages="$errors->get('dueDate')" />
                        </div>
                        {{-- <div>

                            <x-input-label for="title" :value="__('Status')" />
                            <x-select-input id="status" name="status" class="mt-1 block w-full">
                                <option value="">--select--</option>
                                <option value="pending" @selected(old('status', $task->status == 'pending'))>Pending</option>
                                <option value="completed" @selected(old('status', $task->status == 'completed'))>Completed</option>
                            </x-select-input>
                        </div> --}}

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Submit') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
