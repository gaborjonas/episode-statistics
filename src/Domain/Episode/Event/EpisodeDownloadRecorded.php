<?php

declare(strict_types=1);

namespace App\Domain\Episode\Event;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\ValueObject\EpisodeId;
use App\Shared\ValueObject\EventId;
use App\Shared\ValueObject\PodcastId;
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
