<?php

declare(strict_types=1);

namespace App\Application\IncomingEvent\Command;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\IncomingEvent\Enum\EventType;
use App\Domain\IncomingEvent\Repository\IncomingEventRepositoryInterface;
use App\Shared\Domain\Bus\EventBus;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\EventId;
use App\Shared\Domain\ValueObject\PodcastId;
use DateMalformedStringException;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecordIncomingEventCommandHandler
{
    public function __construct(
        private IncomingEventRepositoryInterface $eventStoreRepository,
        private EventBus $eventBus,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(RecordIncomingEventCommand $command): void
    {
        // use the event_id as idempotency key to avoid duplicate processing
        if ($this->eventStoreRepository->exists($command->eventId)) {
            return;
        }

        $occurredAt = new DateTimeImmutable($command->occurredAt);

        $this->eventStoreRepository->append(
            $command->eventId,
            $command->type,
            $occurredAt,
            $command->data,
            new DateTimeImmutable(),
        );

        if (EventType::tryFrom($command->type) === EventType::EpisodeDownloaded) {
            $this->eventBus->dispatch(
                new EpisodeDownloadRecorded(
                    eventId: EventId::fromString($command->eventId),
                    episodeId: EpisodeId::fromString($command->data['episode_id']),
                    podcastId: PodcastId::fromString($command->data['podcast_id']),
                    occurredAt: $occurredAt,
                ),
            );
        }
    }
}
