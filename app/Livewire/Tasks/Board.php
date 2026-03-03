<?php

namespace App\Livewire\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class Board extends Component
{
    public Project $project;

    public ?int $taskId = null;
    public string $title = '';
    public ?string $description = '';
    public string $status = 'todo';
    public ?int $user_id = null;

    public array $statuses = [];

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->statuses = Task::STATUSES;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', array_keys(Task::STATUSES))],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function getTasksByStatusProperty()
    {
        return $this->project
            ->tasks()
            ->with('user')
            ->orderBy('position')
            ->orderBy('created_at')
            ->get()
            ->groupBy('status');
    }

    public function create(string $status = 'todo'): void
    {
        $this->reset(['taskId', 'title', 'description', 'user_id']);
        $this->status = $status;
    }

    public function edit(Task $task): void
    {
        $this->authorizeTask($task);

        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->status = $task->status;
        $this->user_id = $task->user_id;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->taskId) {
            $task = $this->project->tasks()->findOrFail($this->taskId);
            $task->update($data);
        } else {
            $maxPosition = $this->project->tasks()
                ->where('status', $data['status'])
                ->max('position') ?? 0;

            $data['position'] = $maxPosition + 1;

            $this->project->tasks()->create($data);
        }

        $this->dispatch('task-saved');

        $this->create($this->status);
    }

    public function moveTask(int $taskId, string $status): void
    {
        if (! array_key_exists($status, $this->statuses)) {
            abort(400);
        }

        $task = $this->project->tasks()->findOrFail($taskId);

        $this->authorizeTask($task);

        $maxPosition = $this->project->tasks()
            ->where('status', $status)
            ->max('position') ?? 0;

        $task->update([
            'status' => $status,
            'position' => $maxPosition + 1,
        ]);
    }

    public function delete(Task $task): void
    {
        $this->authorizeTask($task);

        $task->delete();

        if ($this->taskId === $task->id) {
            $this->create();
        }
    }

    private function authorizeTask(Task $task): void
    {
        abort_unless($task->project_id === $this->project->id, 403);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();

        return view('livewire.tasks.board', [
            'tasksByStatus' => $this->tasksByStatus,
            'users' => $users,
        ]);
    }
}