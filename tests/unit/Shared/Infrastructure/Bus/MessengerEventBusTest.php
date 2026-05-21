<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Infrastructure\Bus;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\EventId;
use App\Shared\Domain\ValueObject\PodcastId;
use App\Shared\Infrastructure\Bus\MessengerEventBusInterface;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerEventBusTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;
    private MessengerEventBusInterface $eventBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->eventBus   = new MessengerEventBusInterface($this->messageBus);
    }

    #[Test]
    public function delegates_dispatch_to_messenger_bus(): void
    {
        $event = new EpisodeDownloadRecorded(
            eventId:    EventId::fromString('550e8400-e29b-41d4-a716-446655440001'),
            episodeId:  EpisodeId::fromString('550e8400-e29b-41d4-a716-446655440002'),
            podcastId:  PodcastId::fromString('550e8400-e29b-41d4-a716-446655440003'),
            occurredAt: new DateTimeImmutable('2024-03-15T10:00:00+00:00'),
        );

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $this->eventBus->dispatch($event);
    }
}
