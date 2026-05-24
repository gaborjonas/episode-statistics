<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Shared\Bus;

use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\Shared\ValueObject\EpisodeId;
use App\Domain\Shared\ValueObject\EventId;
use App\Domain\Shared\ValueObject\PodcastId;
use App\Infrastructure\Shared\Bus\MessengerEventBus;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerEventBusTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;
    private MessengerEventBus $eventBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->eventBus   = new MessengerEventBus($this->messageBus);
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
