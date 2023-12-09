<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'created_by' => new UserResource($this->whenLoaded('user')), // include user data
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}