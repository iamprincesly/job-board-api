<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:250'],
            'description' => [$required, 'string'],
            'location' => [$required, 'string', 'max:100'],
            'salary_range' => [$required, 'numeric'],
            'is_remote' => [$required, 'boolean'],
            'published_at' => ['sometimes', 'date'],
        ];
    }
}
