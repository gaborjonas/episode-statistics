<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\Episode\Query;

use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQuery;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQueryHandler;
use App\Domain\Episode\Repository\EpisodeDownloadRepositoryInterface;
use App\Domain\Episode\ValueObject\DateRange;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\PodcastId;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class GetDownloadsQueryHandlerTest extends TestCase
{
    private const string PODCAST_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string EPISODE_ID = '550e8400-e29b-41d4-a716-446655440002';

    private EpisodeDownloadRepositoryInterface&Stub $repository;
    private GetDownloadsQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createStub(EpisodeDownloadRepositoryInterface::class);
        $this->handler    = new GetDownloadsQueryHandler($this->repository);
    }

    #[Test]
    public function returns_downloads_result_with_correct_shape(): void
    {
        $query = $this->makeQuery('2024-03-14', '2024-03-15');

        $this->repository->method('countByDate')->willReturn([
            '2024-03-14' => 3,
            '2024-03-15' => 7,
        ]);

        $result = ($this->handler)($query);

        $this->assertSame(self::PODCAST_ID, $result->podcastId);
        $this->assertSame(self::EPISODE_ID, $result->episodeId);
        $this->assertSame('2024-03-14', $result->from);
        $this->assertSame('2024-03-15', $result->to);
        $this->assertCount(2, $result->downloads);
        $this->assertSame(['date' => '2024-03-14', 'count' => 3], $result->downloads[0]);
        $this->assertSame(['date' => '2024-03-15', 'count' => 7], $result->downloads[1]);
    }

    #[Test]
    public function fills_zero_for_days_with_no_downloads(): void
    {
        $query = $this->makeQuery('2024-03-13', '2024-03-15');

        $this->repository->method('countByDate')->willReturn([
            '2024-03-15' => 5,
        ]);

        $result = ($this->handler)($query);

        $this->assertCount(3, $result->downloads);
        $this->assertSame(['date' => '2024-03-13', 'count' => 0], $result->downloads[0]);
        $this->assertSame(['date' => '2024-03-14', 'count' => 0], $result->downloads[1]);
        $this->assertSame(['date' => '2024-03-15', 'count' => 5], $result->downloads[2]);
    }

    #[Test]
    public function returns_all_zeros_when_no_database_rows(): void
    {
        $query = $this->makeQuery('2024-03-14', '2024-03-15');

        $this->repository->method('countByDate')->willReturn([]);

        $result = ($this->handler)($query);

        $this->assertCount(2, $result->downloads);
        foreach ($result->downloads as $day) {
            $this->assertSame(0, $day['count']);
        }
    }

    #[Test]
    public function single_day_range_returns_one_entry(): void
    {
        $query = $this->makeQuery('2024-06-01', '2024-06-01');

        $this->repository->method('countByDate')->willReturn([
            '2024-06-01' => 42,
        ]);

        $result = ($this->handler)($query);

        $this->assertCount(1, $result->downloads);
        $this->assertSame(42, $result->downloads[0]['count']);
    }

    private function makeQuery(string $from, string $to): GetDownloadsQuery
    {
        return new GetDownloadsQuery(
            podcastId:  PodcastId::fromString(self::PODCAST_ID),
            episodeId:  EpisodeId::fromString(self::EPISODE_ID),
            dateRange:  new DateRange(
                new DateTimeImmutable($from),
                new DateTimeImmutable($to),
            ),
        );
    }
}
