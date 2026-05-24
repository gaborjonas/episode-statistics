<?php

declare(strict_types=1);

namespace App\Application\IncomingEvent\Command;

use App\Domain\Shared\Bus\Command;

final readonly class RecordIncomingEventCommand implements Command
{
    /**
     * @param array<string,mixed> $data
     */
    public function __construct(
        public string $eventId,
        public string $type,
        public string $occurredAt,
        public array $data,
    ) {}
}
