<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;

#[FailOnUnknownFields]
class NewCareerRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'team' => ['required', 'string', 'max:255'],
            'venture' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'work_mode' => ['required', 'string', 'max:255'],
            'description' => ['required'],
            'location' => ['required'],
            'closing_date' => ['required', 'date', 'after_or_equal:today'],
            'is_active' => ['required','boolean'],
            'is_published' => ['required','boolean']
        ];
    }
}
