<?php

namespace App\Http\Requests\Task\Update;

use App\Enums\Task\TaskStatusEnum;
use App\Enums\Task\TaskTitleEnum;
use App\Http\Requests\Task\BaseTaskRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends BaseTaskRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    private function patchRules(): array
    {
        return [
            'title' => [
                'sometimes',
                'max:'.TaskTitleEnum::MAX_LENGTH->value,
                'string',
            ],
            'description' => [
                'sometimes',
                'string',
            ],
            'status' => [
                'sometimes',
                new Enum(TaskStatusEnum::class),
            ],
        ];
    }

    private function putRules(): array
    {
        return [
            'title' => [
                'required',
                'max:'.TaskTitleEnum::MAX_LENGTH->value,
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'status' => [
                'required',
                new Enum(TaskStatusEnum::class),
            ],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'PATCH' => $this->patchRules(),
            default => $this->putRules(),
        };
    }
}
