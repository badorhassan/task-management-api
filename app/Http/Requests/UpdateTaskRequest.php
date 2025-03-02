<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;


class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = Task::find($this->route('task'));
        $user = Auth::user();

        if (!$task) {
            return false;
        }

        // Manager can update all fields
        if ($user->hasRole('manager')) {
            return true;
        }

        // Regular user can only update status of assigned tasks
        return $task->assigned_to === $user->id && $user->can('update task status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
