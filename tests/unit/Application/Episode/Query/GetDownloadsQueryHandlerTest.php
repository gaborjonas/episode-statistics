<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\Episode\Query;

use App\Application\Episode\DTO\DownloadsResult;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQuery;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQueryHandler;
use App\Domain\Episode\ValueObject\DateRange;
use App\Shared\ValueObject\EpisodeId;
use App\Shared\ValueObject\PodcastId;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NativeQuery;
use PHPUnit\Framework\TestCase;

final class GetDownloadsQueryHandlerTest extends TestCase
{
    private function makeHandler(array $dbRows): GetDownloadsQueryHandler
    {
        $nativeQuery = $this->createStub(NativeQuery::class);
        $nativeQuery->method('setParameters')->willReturnSelf();
        $nativeQuery->method('getArrayResult')->willReturn($dbRows);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('createNativeQuery')->willReturn($nativeQuery);

        return new GetDownloadsQueryHandler($em);
    }

    public function test_returns_downloads_result_with_data_from_db(): void
    {
        $result = $this->makeHandler([
            ['date' => '2024-01-01', 'count' => 5],
            ['date' => '2024-01-02', 'count' => 3],
        ])($this->makeQuery('2024-01-01', '2024-01-02'));

        $this->assertInstanceOf(DownloadsResult::class, $result);
        $this->assertSame([
            ['date' => '2024-01-01', 'count' => 5],
            ['date' => '2024-01-02', 'count' => 3],
        ], $result->downloads);
    }

    public function test_fills_missing_dates_with_zero_count(): void
    {
        $result = $this->makeHandler([
            ['date' => '2024-01-02', 'count' => 7],
        ])($this->makeQuery('2024-01-01', '2024-01-03'));

        $this->assertSame([
            ['date' => '2024-01-01', 'count' => 0],
            ['date' => '2024-01-02', 'count' => 7],
            ['date' => '2024-01-03', 'count' => 0],
        ], $result->downloads);
    }

    public function test_returns_all_zero_counts_when_db_returns_empty(): void
    {
        $result = $this->makeHandler([])($this->makeQuery('2024-01-01', '2024-01-02'));

        $this->assertSame([
            ['date' => '2024-01-01', 'count' => 0],
            ['date' => '2024-01-02', 'count' => 0],
        ], $result->downloads);
    }

    public function test_result_carries_query_identifiers(): void
    {
        $query  = $this->makeQuery('2024-01-01', '2024-01-01');
        $result = $this->makeHandler([])($query);

        $this->assertSame($query->podcastId->toString(), $result->podcastId);
        $this->assertSame($query->episodeId->toString(), $result->episodeId);
        $this->assertSame('2024-01-01', $result->from);
        $this->assertSame('2024-01-01', $result->to);
    }

    public function test_passes_all_query_parameters_to_sql(): void
    {
        // kills mutants: removing/mangling any of the four parameter entries
        $nativeQuery = $this->createMock(NativeQuery::class);
        $nativeQuery->expects($this->once())
            ->method('setParameters')
            ->with([
                'podcastId'   => '550e8400-e29b-41d4-a716-446655440000',
                'episodeId'   => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'from'        => '2024-01-15',
                'toExclusive' => '2024-01-17', // to (2024-01-16) + 1 day
            ])
            ->willReturnSelf();
        $nativeQuery->expects($this->once())->method('getArrayResult')->willReturn([]);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('createNativeQuery')->willReturn($nativeQuery);

        (new GetDownloadsQueryHandler($em))($this->makeQuery('2024-01-15', '2024-01-16'));
    }

    public function test_casts_db_count_to_integer(): void
    {
        // kills mutant: removing (int) cast leaves string count instead of int
        $result = $this->makeHandler([
            ['date' => '2024-01-01', 'count' => '9'],
        ])($this->makeQuery('2024-01-01', '2024-01-01'));

        $this->assertSame(9, $result->downloads[0]['count']);
    }

    private function makeQuery(string $from, string $to): GetDownloadsQuery
    {
        return new GetDownloadsQuery(
            podcastId: PodcastId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            episodeId: EpisodeId::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8'),
            dateRange: new DateRange(
                new DateTimeImmutable($from),
                new DateTimeImmutable($to),
            ),
        );
    }
}
