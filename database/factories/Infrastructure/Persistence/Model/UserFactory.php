<?php

namespace Database\Factories\Infrastructure\Persistence\Model;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\User\Role\Role;
use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LaravelUserModel>
 */
class UserFactory extends Factory
{
    protected $model = LaravelUserModel::class;

    public function definition(): array
    {
        $id = new Id(Str::uuid()->toString());
        $name = new Name(fake()->name());
        $email = new Email(fake()->unique()->freeEmail());
        $password = new Password('P4sSW0rd@!)_');

        return [
            'id' => $id->getValue(),
            'name' => $name->getValue(),
            'email' => $email->getValue(),
            'email_verified_at' => now(),
            'password' => $password->getValue(),
            'role' => Role::USER->value,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
