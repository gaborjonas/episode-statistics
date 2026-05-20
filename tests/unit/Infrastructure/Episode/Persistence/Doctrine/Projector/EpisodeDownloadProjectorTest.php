<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\Episode\Persistence\Doctrine\Projector;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\Episode\Projection\EpisodeDownload;
use App\Infrastructure\Episode\Persistence\Doctrine\Projector\EpisodeDownloadProjector;
use App\Shared\ValueObject\EpisodeId;
use App\Shared\ValueObject\EventId;
use App\Shared\ValueObject\PodcastId;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EpisodeDownloadProjectorTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private EpisodeDownloadProjector $projector;

    protected function setUp(): void
    {
        $this->em        = $this->createMock(EntityManagerInterface::class);
        $this->projector = new EpisodeDownloadProjector($this->em);
    }

    public function test_persists_and_flushes_episode_download_on_event(): void
    {
        $event = new EpisodeDownloadRecorded(
            eventId:    EventId::fromString('550e8400-e29b-41d4-a716-446655440001'),
            episodeId:  EpisodeId::fromString('550e8400-e29b-41d4-a716-446655440002'),
            podcastId:  PodcastId::fromString('550e8400-e29b-41d4-a716-446655440003'),
            occurredAt: new DateTimeImmutable('2024-03-15T10:00:00+00:00'),
        );

        $this->em->expects(self::once())
            ->method('persist')
            ->with(self::isInstanceOf(EpisodeDownload::class));

        $this->em->expects(self::once())->method('flush');

        ($this->projector)($event);
    }
}
