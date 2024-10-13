<?php

namespace App\Http\Requests\Auth;

use App\Enums\Auth\PasswordEnum;
use App\Messages\User\UserEmailMessage;
use App\Messages\User\UserPasswordMessage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Password;

class AuthenticateUserRequest extends FormRequest
{
    /**
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            (response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST))
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    private function getUserEmailMessages(): array
    {
        return [
            'email.required' => UserEmailMessage::emailIsRequired(),
            'email.email' => UserEmailMessage::emailIsInvalid(),
        ];
    }

    private function getUserPasswordMessages(): array
    {
        return [
            'password.required' => UserPasswordMessage::passwordIsRequired(),
            'password.min' => UserPasswordMessage::passwordMinLength(),
            'password.numbers' => UserPasswordMessage::passwordNumbers(),
            'password.symbols' => UserPasswordMessage::passwordSymbols(),
            'password.mixed' => UserPasswordMessage::passwordMixedCase(),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns',
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

    public function messages(): array
    {
        return array_merge($this->getUserEmailMessages(), $this->getUserPasswordMessages());
    }
}
