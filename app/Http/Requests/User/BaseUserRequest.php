<?php

namespace App\Http\Requests\User;

use App\Messages\User\UserEmailMessage;
use App\Messages\User\UserNameMessage;
use App\Messages\User\UserPasswordMessage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class BaseUserRequest extends FormRequest
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

    private function getUserNameMessages(): array
    {
        return [
            'name.required' => UserNameMessage::nameIsRequired(),
            'name.min' => UserNameMessage::nameMinLength(),
            'name.max' => UserNameMessage::nameMaxLength(),
            'name.string' => UserNameMessage::nameMustBeString(),
        ];
    }

    private function getUserEmailMessages(): array
    {
        return [
            'email.required' => UserEmailMessage::emailIsRequired(),
            'email.email' => UserEmailMessage::emailIsInvalid(),
            'email.unique' => UserEmailMessage::emailAlreadyExists(),
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

    public function messages(): array
    {
        return array_merge(
            $this->getUserNameMessages(),
            $this->getUserEmailMessages(),
            $this->getUserPasswordMessages()
        );
    }
}
