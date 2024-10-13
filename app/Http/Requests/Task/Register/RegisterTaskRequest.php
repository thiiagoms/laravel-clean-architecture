<?php

namespace App\Http\Requests\Task\Register;

use App\Enums\Task\TaskStatusEnum;
use App\Enums\Task\TaskTitleEnum;
use App\Http\Requests\Task\BaseTaskRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterTaskRequest extends BaseTaskRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
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
}
