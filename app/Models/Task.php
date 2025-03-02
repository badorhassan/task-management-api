<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'created_by',
        'status',
        'due_date',
    ];

    /**
     * Get the user that is assigned to this task
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user that created this task
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all dependencies of this task
     */
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id')
            ->withTimestamps();
    }

    /**
     * Get all tasks that depend on this task
     */
    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependency_id', 'task_id')
            ->withTimestamps();
    }

    /**
     * Check if the task can be completed
     */
    public function canBeCompleted()
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->status !== 'completed') {
                return false;
            }
        }
        return true;
    }
}