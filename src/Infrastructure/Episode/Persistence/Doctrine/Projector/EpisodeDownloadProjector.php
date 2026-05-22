<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Persistence\Doctrine\Projector;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\Episode\Projection\EpisodeDownload;
use App\Domain\Episode\Repository\EpisodeDownloadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EpisodeDownloadProjector
{
    public function __construct(
        private EpisodeDownloadRepositoryInterface $repository,
    ) {}

    public function __invoke(EpisodeDownloadRecorded $event): void
    {
        $this->repository->save(
            EpisodeDownload::create(
                episodeId: $event->episodeId,
                podcastId: $event->podcastId,
                occurredAt: $event->occurredAt,
            ),
        );
    }
}
