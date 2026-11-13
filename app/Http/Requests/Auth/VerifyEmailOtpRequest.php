<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => $this->user() !== null
                ? ['nullable', 'string', 'email']
                : ['required', 'string', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'digits:6'],
        ];
    }

    /**
     * Get the target email for verification.
     */
    public function targetEmail(): string
    {
        return (string) ($this->user()?->email ?? $this->string('email'));
    }
}
