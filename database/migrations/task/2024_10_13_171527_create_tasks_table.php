<?php

use App\Enums\Task\TaskStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')
                ->index()
                ->primary();
            $table->foreignUuid('user_id')
                ->constrained('users');
            $table->string('title', 100);
            $table->text('description');
            $table
                ->enum('status', array_map(fn (TaskStatusEnum $task): string => $task->value, TaskStatusEnum::cases()));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
