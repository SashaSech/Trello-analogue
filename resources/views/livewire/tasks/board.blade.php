<section class="w-full" x-data="{}">
    <flux:heading size="xl" class="mb-2">
        Task board: {{ $project->name }}
    </flux:heading>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($statuses as $code => $label)
            <div
                class="flex flex-col gap-3 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md border border-zinc-200 dark:border-zinc-700"
                @dragover.prevent
                @drop.prevent="$wire.moveTask($event.dataTransfer.getData('task-id'), '{{ $code }}')"
            >
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">{{ $label }}</flux:heading>
                </div>
            
                <div class="space-y-2">
                    @php
                        $columnTasks = $tasksByStatus->get($code, collect());
                    @endphp

                    @forelse ($columnTasks as $task)
                        <div
                            class="p-3 bg-white dark:bg-zinc-800 rounded-md border border-zinc-200 dark:border-zinc-700"
                            draggable="true"
                            @dragstart="$event.dataTransfer.setData('task-id', '{{ $task->id }}')"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-semibold text-sm">
                                        {{ $task->title }}
                                    </div>

                                    @if ($task->user)
                                        <div class="text-xs text-zinc-500">
                                            Assignee: {{ $task->user->name }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-1 text-xs">
                                    <flux:button
                                        size="xs"
                                        variant="ghost"
                                        wire:click="edit({{ $task->id }})"
                                    >
                                        Edit
                                    </flux:button>

                                    <flux:button
                                        size="xs"
                                        variant="ghost"
                                        color="danger"
                                        wire:click="delete({{ $task->id }})"
                                    >
                                        Delete
                                    </flux:button>
                                </div>
                            </div>

                            @if ($task->description)
                                <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-300">
                                    {{ \Illuminate\Support\Str::limit($task->description, 120) }}
                                </div>
                            @endif

                            
                        </div>
                    @empty
                        <flux:text class="text-xs text-zinc-500">
                            No tasks in this column.
                        </flux:text>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 space-y-4">
            <div>
                <flux:heading size="md">
                    {{ $taskId ? 'Edit task' : 'New task' }}
                </flux:heading>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <flux:input
                    wire:model="title"
                    label="Title"
                    required
                />

                <flux:textarea
                    wire:model="description"
                    label="Description"
                    rows="4"
                />

                <flux:select
                    wire:model="user_id"
                    label="Assignee"
                >
                    <option value="">Unassigned</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select
                    wire:model="status"
                    label="Status"
                >
                    @foreach ($statuses as $code => $label)
                        <option value="{{ $code }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <div class="flex justify-end gap-2">
                    @if ($taskId)
                        <flux:button type="button" variant="ghost" wire:click="create()">
                            Cancel
                        </flux:button>
                    @endif

                    <flux:button type="submit" variant="primary">
                        Save
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</section>