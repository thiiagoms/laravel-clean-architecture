<?php

namespace Database\Factories\Infrastructure\Persistence\Model;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Infrastructure\Persistence\Model\Task as LaravelTaskModel;
use App\Infrastructure\Persistence\Model\User as LaravelUserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LaravelTaskModel>
 */
class TaskFactory extends Factory
{
    protected $model = LaravelTaskModel::class;

    public function definition(): array
    {
        $id = new Id(Str::uuid()->toString());
        $title = new Title(fake()->title());
        $description = new Description(fake()->paragraph());

        $owner = LaravelUserModel::factory()->createOne();

        return [
            'id' => $id->getValue(),
            'user_id' => $owner->id,
            'title' => $title->getValue(),
            'description' => $description->getValue(),
            'status' => Status::TODO->value,
        ];
    }
}
