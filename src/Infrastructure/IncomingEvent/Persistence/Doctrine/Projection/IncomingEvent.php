<?php

declare(strict_types=1);

namespace App\Infrastructure\IncomingEvent\Persistence\Doctrine\Projection;

use DateTimeImmutable;

final class IncomingEvent
{
    /** @var array<string,mixed> */
    private(set) array $data;
    private(set) string $id;
    private(set) string $type;
    private(set) DateTimeImmutable $occurredAt;
    private(set) DateTimeImmutable $createdAt;

    private function __construct() {}

    /**
     * @param array<string,mixed> $data
     */
    public static function create(
        string $id,
        string $type,
        DateTimeImmutable $occurredAt,
        array $data,
        DateTimeImmutable $createdAt,
    ): self {
        $event = new self();
        $event->id = $id;
        $event->type = $type;
        $event->occurredAt = $occurredAt;
        $event->data = $data;
        $event->createdAt = $createdAt;

        return $event;
    }
}
