<?php

declare(strict_types=1);

namespace App\Application\Episode\DTO;

final readonly class DownloadsResult
{
    /**
     * @param list<array{date: string, count: int}> $downloads
     */
    public function __construct(
        public string $podcastId,
        public string $episodeId,
        public string $from,
        public string $to,
        public array  $downloads,
    ) {}
}
