<?php

namespace Database\Factories\Infrastructure\Persistence\Model;

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

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = New Name(fake()->name());
        $email = new Email(fake()->unique()->freeEmail());
        $password = new Password('P4sSW0rd@!)_');

        return [
            'id' => Str::uuid()->toString(),
            'name' => $name->getValue(),
            'email' => $email->getValue(),
            'email_verified_at' => now(),
            'password' => $password->getValue(),
            'role' => Role::USER->value,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
