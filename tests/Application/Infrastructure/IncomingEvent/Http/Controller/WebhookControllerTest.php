<?php

declare(strict_types=1);

namespace App\Tests\Application\Infrastructure\IncomingEvent\Http\Controller;

use App\Domain\IncomingEvent\Enum\EventType;
use App\Domain\Shared\Bus\CommandBus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class WebhookControllerTest extends WebTestCase
{
    private const string EVENT_ID    = '550e8400-e29b-41d4-a716-446655440000';
    private const string PODCAST_ID  = '550e8400-e29b-41d4-a716-446655440001';
    private const string EPISODE_ID  = '550e8400-e29b-41d4-a716-446655440002';
    private const string OCCURRED_AT = '2024-03-14T12:00:00+00:00';

    private CommandBus&MockObject $commandBus;

    protected function setUp(): void
    {
        $client = static::createClient();

        $this->commandBus = $this->createMock(CommandBus::class);
        $client->getContainer()->set(CommandBus::class, $this->commandBus);
    }

    #[Test]
    public function returns_202_for_valid_episode_downloaded_event(): void
    {
        $this->commandBus->expects($this->once())->method('dispatch');

        $this->post($this->payload());

        $this->assertResponseStatusCodeSame(202);
        $body = $this->json();
        $this->assertSame('Request accepted.', $body['message']);
    }

    #[Test]
    public function returns_202_for_unknown_event_type(): void
    {
        $this->commandBus->expects($this->once())->method('dispatch');

        $this->post($this->payload(['type' => 'unknown.event']));

        $this->assertResponseStatusCodeSame(202);
    }

    #[Test]
    public function returns_422_when_event_id_is_missing(): void
    {
        $this->commandBus->expects($this->never())->method('dispatch');

        $body = json_decode($this->payload(), true);
        unset($body['event_id']);

        $this->post(json_encode($body, JSON_THROW_ON_ERROR));

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function returns_422_when_event_id_is_not_a_uuid(): void
    {
        $this->commandBus->expects($this->never())->method('dispatch');

        $this->post($this->payload(['event_id' => 'not-a-uuid']));

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function returns_422_when_occurred_at_is_missing(): void
    {
        $this->commandBus->expects($this->never())->method('dispatch');

        $body = json_decode($this->payload(), true);
        unset($body['occurred_at']);

        $this->post(json_encode($body, JSON_THROW_ON_ERROR));

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function returns_422_when_occurred_at_is_not_a_datetime(): void
    {
        $this->commandBus->expects($this->never())->method('dispatch');

        $this->post($this->payload(['occurred_at' => 'not-a-datetime']));

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function returns_422_when_type_is_missing(): void
    {
        $this->commandBus->expects($this->never())->method('dispatch');

        $body = json_decode($this->payload(), true);
        unset($body['type']);

        $this->post(json_encode($body, JSON_THROW_ON_ERROR));

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function returns_422_when_data_episode_id_is_not_a_uuid(): void
    {
        $this->commandBus->expects($this->never())->method('dispatch');

        $this->post($this->payload([
            'data' => ['episode_id' => 'not-a-uuid', 'podcast_id' => self::PODCAST_ID],
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    private function post(string $content): void
    {
        static::getClient()->request('POST', '/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: $content);
    }

    /**
     * @param array<string,mixed> $overrides
     */
    private function payload(array $overrides = []): string
    {
        return json_encode(array_merge([
            'type'        => EventType::EpisodeDownloaded->value,
            'event_id'    => self::EVENT_ID,
            'occurred_at' => self::OCCURRED_AT,
            'data'        => [
                'episode_id' => self::EPISODE_ID,
                'podcast_id' => self::PODCAST_ID,
            ],
        ], $overrides), JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<mixed>
     */
    private function json(): array
    {
        return json_decode(static::getClient()->getResponse()->getContent(), true);
    }
}
