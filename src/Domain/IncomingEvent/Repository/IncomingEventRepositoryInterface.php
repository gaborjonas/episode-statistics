<?php

declare(strict_types=1);

namespace App\Domain\IncomingEvent\Repository;

use App\Domain\IncomingEvent\Projection\IncomingEvent;

interface IncomingEventRepositoryInterface
{
    public function exists(string $id): bool;

    public function append(IncomingEvent $event): void;
}
