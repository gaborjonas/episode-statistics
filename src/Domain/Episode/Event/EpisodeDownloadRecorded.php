<?php

declare(strict_types=1);

namespace App\Domain\Episode\Event;

use App\Domain\Shared\Event\DomainEvent;
use App\Domain\Shared\ValueObject\EpisodeId;
use App\Domain\Shared\ValueObject\EventId;
use App\Domain\Shared\ValueObject\PodcastId;
use DateTimeImmutable;

final readonly class EpisodeDownloadRecorded implements DomainEvent
{
    public function __construct(
        public EventId $eventId,
        public EpisodeId $episodeId,
        public PodcastId $podcastId,
        public DateTimeImmutable $occurredAt,
    ) {}
}
