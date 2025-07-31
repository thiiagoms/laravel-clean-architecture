<?php

namespace App\Presentation\Api\V1\User\Resources;

use App\Domain\Entity\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->getId()->getValue(),
            'name' => $user->getName()->getValue(),
            'email' => $user->getEmail()->getValue(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
