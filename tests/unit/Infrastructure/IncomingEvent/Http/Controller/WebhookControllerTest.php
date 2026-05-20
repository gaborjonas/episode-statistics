<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\IncomingEvent\Http\Controller;

use App\Application\IncomingEvent\Command\RecordIncomingEventCommand;
use App\Infrastructure\IncomingEvent\Http\Controller\WebhookController;
use App\Infrastructure\IncomingEvent\Http\Request\WebHookRequest;
use App\Shared\Domain\Bus\CommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class WebhookControllerTest extends TestCase
{
    private CommandBus&MockObject $commandBus;
    private WebhookController $controller;

    protected function setUp(): void
    {
        $this->commandBus = $this->createMock(CommandBus::class);
        $this->controller = new WebhookController($this->commandBus);
    }

    public function test_dispatches_record_incoming_event_command(): void
    {
        $request = new WebHookRequest(
            type:       'episode.downloaded',
            eventId:    '550e8400-e29b-41d4-a716-446655440001',
            occurredAt: '2024-03-15T10:00:00+00:00',
            data:       [
                'episode_id' => '550e8400-e29b-41d4-a716-446655440002',
                'podcast_id' => '550e8400-e29b-41d4-a716-446655440003',
            ],
        );

        $this->commandBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static fn (RecordIncomingEventCommand $cmd): bool =>
                $cmd->eventId === '550e8400-e29b-41d4-a716-446655440001'
                && $cmd->type === 'episode.downloaded'
                && $cmd->occurredAt === '2024-03-15T10:00:00+00:00'
                && $cmd->data === $request->data
            ));

        $response = ($this->controller)($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function test_returns_202_accepted(): void
    {
        $request = new WebHookRequest(
            type:       'some.event',
            eventId:    '550e8400-e29b-41d4-a716-446655440004',
            occurredAt: '2024-06-01T08:00:00+00:00',
            data:       [
                'episode_id' => '550e8400-e29b-41d4-a716-446655440005',
                'podcast_id' => '550e8400-e29b-41d4-a716-446655440006',
            ],
        );

        $this->commandBus->method('dispatch');

        $response = ($this->controller)($request);

        $body = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $body);
    }
}
