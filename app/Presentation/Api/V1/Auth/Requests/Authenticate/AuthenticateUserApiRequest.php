<?php

namespace App\Presentation\Api\V1\Auth\Requests\Authenticate;

use App\Presentation\Api\V1\Common\Rules\EmailIsValidRule;
use App\Presentation\Api\V1\Common\Rules\PasswordIsValidRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AuthenticateUserApiRequest extends FormRequest
{
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST)
        );
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                new EmailIsValidRule,
            ],
            'password' => [
                new PasswordIsValidRule,
            ],
        ];
    }
}
