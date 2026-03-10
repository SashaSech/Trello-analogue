<section class="w-full">
    <div class="flex items-center justify-between mb-4">
        <flux:heading size="xl">
            My projects
        </flux:heading>

        <flux:button
            variant="primary"
            size="sm"
            wire:click="create"
        >
            Add project
        </flux:button>
    </div>

        <div class="space-y-3">
            @forelse ($projects as $project)
                <div class="flex items-center justify-between p-3 border rounded-md dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        @if ($project->logo_path)
                            <img
                                src="{{ asset('storage/' . $project->logo_path) }}"
                                alt="{{ $project->name }}"
                                class="w-10 h-10 rounded object-cover"
                            >
                        @else
                            <div class="w-10 h-10 rounded bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs">
                                {{ \Illuminate\Support\Str::limit($project->name, 2, '') }}
                            </div>
                        @endif

                        <div>
                            <div class="font-semibold">
                                {{ $project->name }}
                            </div>
                            @if ($project->description)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ \Illuminate\Support\Str::limit($project->description, 80) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <flux:button
                            size="xs"
                            variant="ghost"
                            :href="route('projects.board', $project)"
                            wire:navigate
                        >
                            Board
                        </flux:button>

                        <flux:button
                            size="xs"
                            variant="ghost"
                            wire:click="edit({{ $project->id }})"
                        >
                            Edit
                        </flux:button>

                        <flux:button
                            size="xs"
                            variant="ghost"
                            color="danger"
                            wire:click="delete({{ $project->id }})"
                        >
                            Delete
                        </flux:button>
                    </div>
                </div>
            @empty
                <flux:text class="text-sm text-zinc-500">
                    No projects yet. Create your first project.
                </flux:text>
            @endforelse
        </div>

    @if ($showForm)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 space-y-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <flux:heading size="md">
                        {{ $projectId ? 'Edit project' : 'New project' }}
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
                        wire:model="name"
                        label="Name"
                        required
                    />

                    <flux:textarea
                        wire:model="description"
                        label="Description"
                        rows="4"
                    />

                    <flux:input
                        wire:model="logo"
                        type="file"
                        label="Logo"
                        accept="image/*"
                    />

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