<?php

declare(strict_types=1);

namespace App\Domain\Episode\Projection;

use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\PodcastId;
use DateTimeImmutable;

final class EpisodeDownload
{
    private(set) int $id;
    private(set) string $episodeId;
    private(set) string $podcastId;
    private(set) DateTimeImmutable $occurredAt;
    private function __construct() {}
    public static function create(
        EpisodeId $episodeId,
        PodcastId $podcastId,
        DateTimeImmutable $occurredAt,
    ): self {
        $download = new self();
        $download->episodeId = $episodeId->toString();
        $download->podcastId = $podcastId->toString();
        $download->occurredAt = $occurredAt;

        return $download;
    }
}
