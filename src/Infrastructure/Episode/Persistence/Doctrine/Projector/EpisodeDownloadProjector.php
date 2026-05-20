<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Persistence\Doctrine\Projector;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\Episode\Projection\EpisodeDownload;
use DateMalformedStringException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EpisodeDownloadProjector
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(EpisodeDownloadRecorded $event): void
    {
        $this->em->persist(
            EpisodeDownload::create(
                episodeId: $event->episodeId,
                podcastId: $event->podcastId,
                occurredAt: $event->occurredAt,
            ),
        );

        $this->em->flush();
    }
}
