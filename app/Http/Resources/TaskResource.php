<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // return parent::toArray($request);
       
       return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'status' => $this->status,
        'due_date' => $this->due_date,
        'assigned_to' => new UserResource($this->whenLoaded('assignee')),
        'created_by' => new UserResource($this->whenLoaded('creator')),
        'dependencies' => TaskResource::collection($this->whenLoaded('dependencies')),
        'can_be_completed' => $this->canBeCompleted(),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];

    }
}
