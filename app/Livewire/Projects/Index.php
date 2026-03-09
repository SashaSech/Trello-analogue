<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public ?int $projectId = null;
    public string $name = '';
    public ?string $description = '';
    public $logo = null;

    public bool $showForm = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    protected function resetForm(): void
    {
        $this->projectId = null;
        $this->name = '';
        $this->description = '';
        $this->logo = null;
    }

    public function getProjectsProperty()
    {
        return Auth::user()
            ->projects()
            ->latest()
            ->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Project $project): void
    {
        $this->authorizeProject($project);

        $this->projectId = $project->id;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->logo = null;

        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->projectId) {
            $project = Project::where('user_id', Auth::id())->findOrFail($this->projectId);
        } else {
            $project = new Project();
            $project->user_id = Auth::id();
        }

        $project->name = $this->name;
        $project->description = $this->description;

        if ($this->logo) {

            if ($project->logo_path) {
                Storage::disk('public')->delete($project->logo_path);
            }

            $path = $this->logo->store('projects/logos', 'public');
            $project->logo_path = $path;
        }

        $project->save();

        $this->dispatch('project-saved');

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(Project $project): void
    {
        $this->authorizeProject($project);

        if ($project->logo_path) {
            Storage::disk('public')->delete($project->logo_path);
        }

        $project->delete();

        if ($this->projectId === $project->id) {
            $this->showForm = false;
            $this->resetForm();
        }
    }

    private function authorizeProject(Project $project): void
    {
        abort_unless($project->user_id === Auth::id(), 403);
    }

    public function render()
    {
        return view('livewire.projects.index', [
            'projects' => $this->projects,
        ]);
    }
}