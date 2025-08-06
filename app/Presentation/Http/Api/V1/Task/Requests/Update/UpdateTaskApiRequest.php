<?php

namespace App\Presentation\Http\Api\V1\Task\Requests\Update;

use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdateTaskApiRequest extends FormRequest
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
        if (! auth('api')->check()) {
            return false;
        }

        /** @var LaravelUserModel $user */
        $user = auth('api')->user();

        return ! empty($user);
    }

    private function patchRules(): array
    {
        return [
            'title' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'description' => [
                'sometimes',
                'string',
            ],
            'status' => [
                'sometimes',
                'string',
                'in:doing,done,cancelled',
            ],
        ];
    }

    private function putRules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'required',
                'string',
            ],
            'status' => [
                'required',
                'string',
                'in:todo,doing,done,cancelled',
            ],
        ];
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'PATCH' => $this->patchRules(),
            default => $this->putRules()
        };
    }
}
