<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'processes' => ['required'],
        ];
    }

    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $processes = $this->input('processes');

            $items = is_array($processes) ? $processes : json_decode($processes, true);

            if (! is_array($items)) {
                $validator->errors()->add('processes', 'The processes field must be an array.');

                return;
            }

            if (count($items) < 1) {
                $validator->errors()->add('processes', 'The processes field must have at least 1 item.');

                return;
            }

            foreach ($items as $index => $item) {
                if (! is_string($item)) {
                    $validator->errors()->add("processes.{$index}", 'Each process must be a string.');

                    return;
                }

                if (strlen($item) > 64) {
                    $validator->errors()->add("processes.{$index}", "Process \"{$item}\" exceeds 64 characters.");

                    return;
                }
            }
        });
    }
}
