<?php

declare(strict_types=1);

namespace App\Domain\Entity\User;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Role\Exception\InvalidRoleTransitionException;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use DateTimeImmutable;

class User
{
    private readonly DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        private Name $name,
        private Email $email,
        private Password $password,
        private Role $role,
        private readonly ?Id $id = null,
        private ?DateTimeImmutable $emailConfirmedAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {

        $now = new DateTimeImmutable;

        $this->createdAt = $createdAt ?? $now;
        $this->updatedAt = $updatedAt ?? $now;
    }

    public function getId(): ?Id
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getEmailConfirmedAt(): ?DateTimeImmutable
    {
        return $this->emailConfirmedAt;
    }

    public function changeNameTo(Name $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function changeEmailTo(Email $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function changePasswordTo(Password $password): void
    {
        $this->password = $password;
        $this->touch();
    }

    public function becomeAdmin(User $admin): void
    {
        if ($admin->getRole()->isAdmin() === false) {
            throw new InvalidRoleTransitionException(from: Role::USER, to: Role::ADMIN, user: $this);
        }

        $this->setRole(Role::ADMIN);
        $this->touch();
    }

    public function becomeUser(): void
    {
        $this->setRole(Role::USER);
        $this->touch();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isEmailAlreadyConfirmed(): bool
    {
        return $this->emailConfirmedAt !== null;
    }

    public function markEmailAsConfirmed(): void
    {
        if ($this->isEmailAlreadyConfirmed()) {
            return;
        }

        $this->emailConfirmedAt = new \DateTimeImmutable;
        $this->touch();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id?->getValue(),
            'name' => $this->name->getValue(),
            'email' => $this->email->getValue(),
            'password' => $this->password->getValue(),
            'role' => $this->role->value,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'emailConfirmedAt' => $this->emailConfirmedAt?->format('Y-m-d H:i:s'),
        ];
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable;
    }

    private function setRole(Role $role): void
    {
        $this->role = $role;
    }
}
