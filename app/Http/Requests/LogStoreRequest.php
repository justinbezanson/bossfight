<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LogStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'kid_id' => ['required', 'integer', 'exists:kids,id'],
            'game_id' => ['nullable', 'integer', 'exists:games,id'],
            'message' => ['required', 'string', 'max:512'],
        ];
    }
}
