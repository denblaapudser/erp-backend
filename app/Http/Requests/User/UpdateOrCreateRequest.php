<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateOrCreateRequest extends FormRequest
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
        return [
            'id' => 'sometimes|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => "nullable|email|max:255|unique:users,email,{$this->id}",
            'password' => 'nullable|string|min:8',
            'accesses' => 'nullable|array',
            'pin' => 'nullable|string|max:4',
            'username' => "nullable|string|max:255|unique:users,username,{$this->id}",
        ];
    }

    public function validatePIN(): void
    {
        if ($this->has('pin') && !is_numeric($this->input('pin'))) {
            throw ValidationException::withMessages(['pin' => 'PIN skal være et tal']);
        }
    }
}
