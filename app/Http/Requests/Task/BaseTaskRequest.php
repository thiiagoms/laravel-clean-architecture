<?php

namespace App\Http\Requests\Task;

use App\Messages\Task\TaskDescriptionMessage;
use App\Messages\Task\TaskStatusMessage;
use App\Messages\Task\TaskTitleMessage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

abstract class BaseTaskRequest extends FormRequest
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

    private function getTaskTitleMessages(): array
    {
        return [
            'title.required' => TaskTitleMessage::taskTitleIsRequired(),
            'title.max' => TaskTitleMessage::taskTitleMaxLength(),
            'title.string' => TaskTitleMessage::taskTitleMustBeString(),
        ];
    }

    private function getTaskDescriptionMessages(): array
    {
        return [
            'description.required' => TaskDescriptionMessage::taskDescriptionIsRequired(),
            'description.string' => TaskDescriptionMessage::taskDescriptionMustBeString(),
        ];
    }

    private function getTaskStatusMessages(): array
    {
        return [
            'status.required' => TaskStatusMessage::taskStatusIsRequired(),
            'status.Illuminate\Validation\Rules\Enum' => TaskStatusMessage::taskStatusIsInvalid(),
        ];
    }

    public function messages(): array
    {
        return array_merge(
            $this->getTaskTitleMessages(),
            $this->getTaskDescriptionMessages(),
            $this->getTaskStatusMessages()
        );
    }
}
