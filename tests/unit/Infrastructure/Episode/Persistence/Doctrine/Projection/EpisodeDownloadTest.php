<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\Episode\Persistence\Doctrine\Projection;

use App\Domain\Shared\ValueObject\EpisodeId;
use App\Domain\Shared\ValueObject\PodcastId;
use App\Infrastructure\Episode\Persistence\Doctrine\Projection\EpisodeDownload;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EpisodeDownloadTest extends TestCase
{
    #[Test]
    public function create_sets_all_properties(): void
    {
        $episodeId  = EpisodeId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $podcastId  = PodcastId::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $occurredAt = new DateTimeImmutable('2024-03-15 12:00:00');

        $download = EpisodeDownload::create(
            episodeId:  $episodeId,
            podcastId:  $podcastId,
            occurredAt: $occurredAt,
        );

        $this->assertSame($episodeId->toString(), $download->episodeId);
        $this->assertSame($podcastId->toString(), $download->podcastId);
        $this->assertSame($occurredAt, $download->occurredAt);
    }
}
