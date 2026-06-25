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
            'member_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('members', 'member_number')->ignore($this->route('member')),
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                Rule::unique('members', 'nik')->ignore($this->route('member')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($this->route('member')),
            ],
            'address' => ['nullable', 'string', 'max:1000'],
            'joined_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
