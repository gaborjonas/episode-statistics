<?php

declare(strict_types=1);

namespace App\Domain\IncomingEvent\Projection;

use DateTimeImmutable;

final class IncomingEvent
{
    public string $id;
    public string $type;
    public DateTimeImmutable $occurredAt;
    /**
     * @var array<string,mixed> $data
     */
    public array $data;
    public DateTimeImmutable $createdAt;

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
