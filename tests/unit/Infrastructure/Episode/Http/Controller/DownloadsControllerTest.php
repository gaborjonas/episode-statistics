<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\Episode\Http\Controller;

use App\Application\Episode\DTO\DownloadsResult;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQuery;
use App\Infrastructure\Episode\Http\Controller\DownloadsController;
use App\Infrastructure\Episode\Http\Request\DownloadsQueryRequest;
use App\Shared\Domain\Bus\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class DownloadsControllerTest extends TestCase
{
    private const string PODCAST_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string EPISODE_ID = '550e8400-e29b-41d4-a716-446655440002';

    private QueryBus&MockObject $queryBus;
    private DownloadsController $controller;

    protected function setUp(): void
    {
        $this->queryBus   = $this->createMock(QueryBus::class);
        $this->controller = new DownloadsController($this->queryBus);
    }

    public function test_dispatches_query_and_returns_json_response(): void
    {
        $result = new DownloadsResult(
            podcastId: self::PODCAST_ID,
            episodeId: self::EPISODE_ID,
            from:      '2024-03-13',
            to:        '2024-03-15',
            downloads: [
                ['date' => '2024-03-13', 'count' => 1],
                ['date' => '2024-03-14', 'count' => 0],
                ['date' => '2024-03-15', 'count' => 4],
            ],
        );

        $this->queryBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GetDownloadsQuery::class))
            ->willReturn($result);

        $queryRequest = new DownloadsQueryRequest(from: '2024-03-13', to: '2024-03-15');
        $response     = ($this->controller)(self::PODCAST_ID, self::EPISODE_ID, $queryRequest);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame(self::PODCAST_ID, $body['podcast_id']);
        $this->assertSame(self::EPISODE_ID, $body['episode_id']);
        $this->assertSame('2024-03-13', $body['from']);
        $this->assertSame('2024-03-15', $body['to']);
        $this->assertCount(3, $body['downloads']);
    }

    public function test_uses_default_date_range_when_no_query_provided(): void
    {
        $result = new DownloadsResult(
            podcastId: self::PODCAST_ID,
            episodeId: self::EPISODE_ID,
            from:      '2024-03-10',
            to:        '2024-03-16',
            downloads: [],
        );

        $this->queryBus->expects($this->once())->method('dispatch')->willReturn($result);

        $response = ($this->controller)(self::PODCAST_ID, self::EPISODE_ID);

        $this->assertSame(200, $response->getStatusCode());
    }
}
