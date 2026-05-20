<?php

declare(strict_types=1);

namespace App\Application\Episode\Command\RecordEpisodeDownloadCommand;

use App\Shared\Domain\Bus\Command;

final readonly class RecordEpisodeDownloadCommand implements Command
{
    /**
     * @param array<string,mixed> $data
     */
    public function __construct(
        public string $eventId,
        public string $type,
        public string $occurredAt,
        public array $data,
        public string $createdAt,
    ) {}
}
