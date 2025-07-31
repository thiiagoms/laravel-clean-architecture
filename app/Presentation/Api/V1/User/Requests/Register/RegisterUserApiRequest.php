<?php

namespace App\Presentation\Api\V1\User\Requests\Register;

use App\Presentation\Api\V1\Common\Rules\EmailIsValidRule;
use App\Presentation\Api\V1\Common\Rules\NameIsValidRule;
use App\Presentation\Api\V1\Common\Rules\PasswordIsValidRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserApiRequest extends FormRequest
{
    /**
     * @throws HttpResponseException
     */
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
            'name' => [
                new NameIsValidRule,
            ],
            'email' => [
                'unique:users,email',
                new EmailIsValidRule,
            ],
            'password' => [
                new PasswordIsValidRule,
            ],
        ];
    }
}
