<?php

namespace App\Http\Requests\User;

use App\Utils\Helpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ChangePinAsAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return Helpers::isRequestFrom('adminApp') && $user->hasAccess('adminAccess') && $user->hasAccess('editUsers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pin' => 'required|string|max:4',
        ];
    }

    public function validatePIN(): void
    {
        if ($this->has('pin') && $this->input('pin') !== null && !is_numeric($this->input('pin'))) {
            throw ValidationException::withMessages(['pin' => 'PIN skal være et tal']);
        }
    }
}
