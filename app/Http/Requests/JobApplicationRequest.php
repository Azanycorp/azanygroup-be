<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class JobApplicationRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'job_id' => ['required', 'integer', 'exists:careers,id'],
            'location' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'social_media_url' => ['nullable', 'string', 'max:255', 'url'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'message' => ['required', 'string', 'max:255'],
        ];
    }
}
