<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\Episode\Query;

use App\Application\Episode\DTO\DownloadsResult;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQuery;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQueryHandler;
use App\Domain\Episode\ValueObject\DateRange;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\PodcastId;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NativeQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetDownloadsQueryHandlerTest extends TestCase
{
    private const string PODCAST_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string EPISODE_ID = '550e8400-e29b-41d4-a716-446655440002';

    private EntityManagerInterface&MockObject $em;
    private GetDownloadsQueryHandler $handler;

    protected function setUp(): void
    {
        $this->em      = $this->createMock(EntityManagerInterface::class);
        $this->handler = new GetDownloadsQueryHandler($this->em);
    }

    public function test_returns_downloads_result_with_correct_shape(): void
    {
        $query = $this->makeQuery('2024-03-14', '2024-03-15');

        $nativeQuery = $this->createMock(NativeQuery::class);
        $nativeQuery->method('setParameters')->willReturnSelf();
        $nativeQuery->method('getArrayResult')->willReturn([
            ['date' => '2024-03-14', 'count' => 3],
            ['date' => '2024-03-15', 'count' => 7],
        ]);
        $this->em->method('createNativeQuery')->willReturn($nativeQuery);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(DownloadsResult::class, $result);
        $this->assertSame(self::PODCAST_ID, $result->podcastId);
        $this->assertSame(self::EPISODE_ID, $result->episodeId);
        $this->assertSame('2024-03-14', $result->from);
        $this->assertSame('2024-03-15', $result->to);
        $this->assertCount(2, $result->downloads);
        $this->assertSame(['date' => '2024-03-14', 'count' => 3], $result->downloads[0]);
        $this->assertSame(['date' => '2024-03-15', 'count' => 7], $result->downloads[1]);
    }

    public function test_fills_zero_for_days_with_no_downloads(): void
    {
        $query = $this->makeQuery('2024-03-13', '2024-03-15');

        $nativeQuery = $this->createMock(NativeQuery::class);
        $nativeQuery->method('setParameters')->willReturnSelf();
        $nativeQuery->method('getArrayResult')->willReturn([
            ['date' => '2024-03-15', 'count' => 5],
        ]);
        $this->em->method('createNativeQuery')->willReturn($nativeQuery);

        $result = ($this->handler)($query);

        $this->assertCount(3, $result->downloads);
        $this->assertSame(['date' => '2024-03-13', 'count' => 0], $result->downloads[0]);
        $this->assertSame(['date' => '2024-03-14', 'count' => 0], $result->downloads[1]);
        $this->assertSame(['date' => '2024-03-15', 'count' => 5], $result->downloads[2]);
    }

    public function test_returns_all_zeros_when_no_database_rows(): void
    {
        $query = $this->makeQuery('2024-03-14', '2024-03-15');

        $nativeQuery = $this->createMock(NativeQuery::class);
        $nativeQuery->method('setParameters')->willReturnSelf();
        $nativeQuery->method('getArrayResult')->willReturn([]);
        $this->em->method('createNativeQuery')->willReturn($nativeQuery);

        $result = ($this->handler)($query);

        $this->assertCount(2, $result->downloads);
        foreach ($result->downloads as $day) {
            $this->assertSame(0, $day['count']);
        }
    }

    public function test_single_day_range_returns_one_entry(): void
    {
        $query = $this->makeQuery('2024-06-01', '2024-06-01');

        $nativeQuery = $this->createMock(NativeQuery::class);
        $nativeQuery->method('setParameters')->willReturnSelf();
        $nativeQuery->method('getArrayResult')->willReturn([
            ['date' => '2024-06-01', 'count' => 42],
        ]);
        $this->em->method('createNativeQuery')->willReturn($nativeQuery);

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
