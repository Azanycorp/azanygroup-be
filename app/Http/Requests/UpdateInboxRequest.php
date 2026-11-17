<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use App\Enum\InboxStatus;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class UpdateInboxRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
          return [
            'note' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255',Rule::in(InboxStatus::values())],
        ];
    }
}
