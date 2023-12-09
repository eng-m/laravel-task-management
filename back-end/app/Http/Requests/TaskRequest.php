<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest {
    public function authorize() {
        $user = auth()->user();

        // Check if the request is for creating or updating a task
        if($this->isMethod('post') || $this->isMethod('put')) {
            // Allow only employers to create or update tasks
            return $user && $user->role === 'employer';
        }


        return true;
    }

    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000', // Assuming a max length for description
            'due_date' => 'required|date|after_or_equal:today', // Due date should be today or in the future
            'status' => 'required|in:in progress,partial,finished', // Ensures status is one of the specified values
        ];
    }
}