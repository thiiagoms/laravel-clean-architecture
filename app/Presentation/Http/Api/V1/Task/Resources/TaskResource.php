<?php

namespace App\Presentation\Http\Api\V1\Task\Resources;

use App\Domain\Entity\Task\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Task $task */
        $task = $this->resource;

        return [
            'id' => $task->getId()->getValue(),
            'title' => $task->getTitle()->getValue(),
            'description' => $task->getDescription()->getValue(),
            'status' => $task->getStatus()->value,
            'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $task->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
