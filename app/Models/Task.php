<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public const STATUSES = [
        'todo' => 'To do',
        'in_progress' => 'In progress',
        'done' => 'Done',
    ];

    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'description',
        'status',
        'position',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}