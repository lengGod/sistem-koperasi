<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'member_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('members', 'member_number')->ignore($this->route('member')),
            ],
            'work_unit' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'joined_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'employment_status' => ['required', 'string', 'max:255'],
        ];
    }
}
