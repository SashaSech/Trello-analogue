<section class="w-full" x-data="{}">
    <div class="flex items-center justify-between mb-2">
        <flux:heading size="xl">
            Task board: {{ $project->name }}
        </flux:heading>
    
        <div class="flex items-center gap-2">
            <flux:button
                variant="outline"
                size="sm"
                :href="route('projects.index')"
                wire:navigate
            >
                Back to projects
            </flux:button>
    
            <flux:button
                variant="primary"
                size="sm"
                wire:click="create"
            >
                Add task
            </flux:button>
        </div>
    </div>

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

    @if ($showForm)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 space-y-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <flux:heading size="md">
                        {{ $taskId ? 'Edit task' : 'New task' }}
                    </flux:heading>

                    <button
                        type="button"
                        class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                        wire:click="cancel"
                    >
                        ✕
                    </button>
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
                        <flux:button type="button" variant="ghost" wire:click="cancel">
                            Cancel
                        </flux:button>

                        <flux:button type="submit" variant="primary">
                            Save
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>