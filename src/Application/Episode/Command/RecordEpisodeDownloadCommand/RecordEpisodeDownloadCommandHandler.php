<?php

declare(strict_types=1);

namespace App\Application\Episode\Command\RecordEpisodeDownloadCommand;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\IncomingEvent\Projection\IncomingEvent;
use App\Domain\IncomingEvent\Repository\IncomingEventRepositoryInterface;
use App\Shared\Domain\Bus\EventBusInterface;
use App\Shared\ValueObject\EpisodeId;
use App\Shared\ValueObject\EventId;
use App\Shared\ValueObject\PodcastId;
use DateMalformedStringException;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecordEpisodeDownloadCommandHandler
{
    public function __construct(
        private IncomingEventRepositoryInterface $eventStoreRepository,
        private EventBusInterface $eventBus,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(RecordEpisodeDownloadCommand $command): void
    {
        // use the event_id as idempotency key to avoid duplicate processing
        if ($this->eventStoreRepository->exists($command->eventId)) {
            return;
        }

        $this->eventStoreRepository->append(
            IncomingEvent::create(
                $command->eventId,
                $command->type,
                new DateTimeImmutable($command->occurredAt),
                $command->data,
                new DateTimeImmutable(),
            ),
        );

        $this->eventBus->dispatch(
            new EpisodeDownloadRecorded(
                eventId: EventId::fromString($command->eventId),
                episodeId: EpisodeId::fromString($command->data['episode_id']),
                podcastId: PodcastId::fromString($command->data['podcast_id']),
                occurredAt: new DateTimeImmutable($command->occurredAt),
            ),
        );
    }
}
