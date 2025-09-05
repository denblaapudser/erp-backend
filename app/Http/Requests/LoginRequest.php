<?php

namespace App\Http\Requests;

use App\Utils\Helpers;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Fortify;

class LoginRequest extends FortifyLoginRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (Helpers::isRequestFrom('adminApp')) {
            return [
                Fortify::username() => 'required|string',
                'password' => 'required|string',
            ];
        } elseif (Helpers::isRequestFrom('employeeApp')) {
            return [
                'pin' => 'required|string',
            ];
        }
    }
}