<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Task;

use App\Contracts\Repositories\ReadableRepositoryContract;
use App\Contracts\Repositories\WritableRepositoryContract;

interface TaskRepositoryContract extends ReadableRepositoryContract, WritableRepositoryContract {}
