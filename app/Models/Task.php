<?php

namespace App\Models;

use App\Enums\Task\TaskStatusEnum;
use App\Infrastructure\Persistence\Model\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (string $task): TaskStatusEnum => TaskStatusEnum::from($task),
            set: fn (TaskStatusEnum $task): string => $task->value
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
