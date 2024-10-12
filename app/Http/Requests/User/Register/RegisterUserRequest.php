<?php

namespace App\Http\Requests\User\Register;

use App\Enums\Auth\PasswordEnum;
use App\Enums\User\UserNameEnum;
use App\Http\Requests\User\BaseUserRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends BaseUserRequest
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
            'name' => [
                'required',
                'min:'.UserNameEnum::MIN_LENGTH->value,
                'max:'.UserNameEnum::MAX_LENGTH->value,
                'string',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
                'string',
            ],
            'password' => [
                'required',
                Password::min(PasswordEnum::MIN_LENGTH->value)
                    ->numbers()
                    ->symbols()
                    ->mixedCase(),
            ],
        ];
    }
}
