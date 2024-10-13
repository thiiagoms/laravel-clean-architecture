<?php

declare(strict_types=1);

namespace App\Services\User\Find;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\User\Find\FindUserByIdServiceContract;
use App\Contracts\Validators\Uuid\UuidValidatorContract;
use App\Messages\System\SystemMessage;
use App\Models\User;
use DomainException;

class FindUserByIdService implements FindUserByIdServiceContract
{
    public function __construct(
        private readonly UuidValidatorContract $uuidValidator,
        private readonly UserRepositoryContract $userRepository
    ) {}

    private function checkUserExists(User|bool $user): void
    {
        throw_if($user === false, new DomainException(SystemMessage::RESOURCE_NOT_FOUND));
    }

    /**
     * @throws DomainException
     */
    public function handle(string $id): User|bool
    {
        $this->uuidValidator->checkUuidIsValid($id);

        $user = $this->userRepository->find($id);

        $this->checkUserExists($user);

        return $user;
    }
}
