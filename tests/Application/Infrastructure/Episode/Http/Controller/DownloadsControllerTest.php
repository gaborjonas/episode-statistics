<?php

declare(strict_types=1);

namespace App\Tests\Application\Infrastructure\Episode\Http\Controller;

use App\Application\Episode\DTO\DownloadsResult;
use App\Shared\Domain\Bus\QueryBus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DownloadsControllerTest extends WebTestCase
{
    private const string PODCAST_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string EPISODE_ID = '550e8400-e29b-41d4-a716-446655440002';

    private MockObject&QueryBus $queryBus;

    protected function setUp(): void
    {
        $client = static::createClient();

        $this->queryBus = $this->createMock(QueryBus::class);
        $client->getContainer()->set(QueryBus::class, $this->queryBus);
    }

    #[Test]
    public function returns_200_with_correct_json_shape(): void
    {
        $this->queryBus->expects($this->once())->method('dispatch')->willReturn(
            new DownloadsResult(
                podcastId: self::PODCAST_ID,
                episodeId: self::EPISODE_ID,
                from:      '2024-03-13T15:30:00Z',
                to:        '2024-03-15T15:30:00Z',
                downloads: [
                    ['date' => '2024-03-13', 'count' => 1],
                    ['date' => '2024-03-14', 'count' => 0],
                    ['date' => '2024-03-15', 'count' => 4],
                ],
            )
        );

        static::getClient()->request('GET', $this->url(), ['from' => '2024-03-13T15:30:00Z', 'to' => '2024-03-15T15:30:00Z']);

        $this->assertResponseIsSuccessful();
        $body = $this->json();
        $this->assertSame(self::PODCAST_ID, $body['podcast_id']);
        $this->assertSame(self::EPISODE_ID, $body['episode_id']);
        $this->assertSame('2024-03-13T15:30:00Z', $body['from']);
        $this->assertSame('2024-03-15T15:30:00Z', $body['to']);
        $this->assertCount(3, $body['downloads']);
    }

    #[Test]
    public function returns_200_with_default_date_range_when_no_dates_given(): void
    {
        $this->queryBus->expects($this->once())->method('dispatch')->willReturn(
            new DownloadsResult(
                podcastId: self::PODCAST_ID,
                episodeId: self::EPISODE_ID,
                from:      '2024-03-10',
                to:        '2024-03-16',
                downloads: [],
            )
        );

        static::getClient()->request('GET', $this->url());

        $this->assertResponseStatusCodeSame(200);
    }

    #[Test]
    public function returns_404_for_invalid_podcast_uuid(): void
    {
        $this->queryBus->expects($this->never())->method('dispatch');

        static::getClient()->request('GET', '/podcasts/not-a-uuid/episodes/' . self::EPISODE_ID . '/downloads');

        $this->assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function returns_404_for_invalid_episode_uuid(): void
    {
        $this->queryBus->expects($this->never())->method('dispatch');

        static::getClient()->request('GET', '/podcasts/' . self::PODCAST_ID . '/episodes/not-a-uuid/downloads');

        $this->assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function returns_404_when_from_is_invalid_date(): void
    {
        $this->queryBus->expects($this->never())->method('dispatch');

        static::getClient()->request('GET', $this->url(), ['from' => 'not-a-date', 'to' => '2024-03-15']);

        $this->assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function returns_404_when_to_is_invalid_date(): void
    {
        $this->queryBus->expects($this->never())->method('dispatch');

        static::getClient()->request('GET', $this->url(), ['from' => '2024-03-13', 'to' => 'not-a-date']);

        $this->assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function returns_404_when_from_is_after_to(): void
    {
        $this->queryBus->expects($this->never())->method('dispatch');

        static::getClient()->request('GET', $this->url(), ['from' => '2024-03-15', 'to' => '2024-03-13']);

        $this->assertResponseStatusCodeSame(404);
    }

    private function url(): string
    {
        return '/podcasts/' . self::PODCAST_ID . '/episodes/' . self::EPISODE_ID . '/downloads';
    }

    /**
     * @return array<mixed>
     */
    private function json(): array
    {
        return json_decode(static::getClient()->getResponse()->getContent(), true);
    }
}
