<?php

declare(strict_types=1);

namespace App\Domain\IncomingEvent\Repository;

use DateTimeImmutable;

interface IncomingEventRepositoryInterface
{
    public function exists(string $id): bool;

    /** @param array<string,mixed> $data */
    public function append(
        string $id,
        string $type,
        DateTimeImmutable $occurredAt,
        array $data,
        DateTimeImmutable $createdAt,
    ): void;
}
