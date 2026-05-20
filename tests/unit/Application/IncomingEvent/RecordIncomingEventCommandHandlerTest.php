<?php

namespace App\Tests\unit\Application\IncomingEvent;

use App\Application\IncomingEvent\Command\RecordIncomingEventCommand;
use App\Application\IncomingEvent\Command\RecordIncomingEventCommandHandler;
use App\Domain\Episode\Event\EpisodeDownloadRecorded;
use App\Domain\IncomingEvent\Projection\IncomingEvent;
use App\Domain\IncomingEvent\Repository\IncomingEventRepositoryInterface;
use App\Shared\Domain\Bus\EventBusInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecordIncomingEventCommandHandlerTest extends TestCase
{
    private const string EVENT_ID = '550e8400-e29b-41d4-a716-446655440001';
    private const string EPISODE_ID = '550e8400-e29b-41d4-a716-446655440002';
    private const string PODCAST_ID = '550e8400-e29b-41d4-a716-446655440003';

    private IncomingEventRepositoryInterface&MockObject $repository;
    private EventBusInterface&MockObject $eventBus;
    private RecordIncomingEventCommandHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(IncomingEventRepositoryInterface::class);
        $this->eventBus = $this->createMock(EventBusInterface::class);
        $this->handler = new RecordIncomingEventCommandHandler($this->repository, $this->eventBus);
    }

    public function test_skips_processing_when_event_already_exists(): void
    {
        $command = $this->makeCommand();

        $this->repository->expects($this->once())->method('exists')->with($command->eventId)->willReturn(true);
        $this->repository->expects($this->never())->method('append');
        $this->eventBus->expects($this->never())->method('dispatch');

        ($this->handler)($command);
    }

    public function test_appends_incoming_event_when_new(): void
    {
        $command = $this->makeCommand();

        $this->repository->expects($this->once())->method('exists')->willReturn(false);
        $this->repository
            ->expects($this->once())
            ->method('append')
            ->with(
                $this->callback(static function (IncomingEvent $event) use ($command): bool {
                    return $event->id === $command->eventId
                        && $event->type === $command->type
                        && $event->data === $command->data;
                }),
            );
        $this->eventBus->expects($this->once())->method('dispatch');

        ($this->handler)($command);
    }

    public function test_dispatches_episode_download_recorded_event(): void
    {
        $command = $this->makeCommand();

        $this->repository->expects($this->once())->method('exists')->willReturn(false);
        $this->repository->expects($this->once())->method('append');
        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(static function (EpisodeDownloadRecorded $event) use ($command): bool {
                    return $event->eventId->toString() === $command->eventId
                        && $event->episodeId->toString() === $command->data['episode_id']
                        && $event->podcastId->toString() === $command->data['podcast_id'];
                }),
            );

        ($this->handler)($command);
    }

    public function test_does_not_dispatch_event_for_unknown_type(): void
    {
        $command = new RecordIncomingEventCommand(
            eventId: self::EVENT_ID,
            type: 'some.other.event',
            occurredAt: '2024-03-15T10:00:00+00:00',
            data: [],
            createdAt: '2024-03-15T10:01:00+00:00',
        );

        $this->repository->expects($this->once())->method('exists')->willReturn(false);
        $this->repository->expects($this->once())->method('append');
        $this->eventBus->expects($this->never())->method('dispatch');

        ($this->handler)($command);
    }

    private function makeCommand(): RecordIncomingEventCommand
    {
        return new RecordIncomingEventCommand(
            eventId: self::EVENT_ID,
            type: 'episode.downloaded',
            occurredAt: '2024-03-15T10:00:00+00:00',
            data: ['episode_id' => self::EPISODE_ID, 'podcast_id' => self::PODCAST_ID],
            createdAt: '2024-03-15T10:01:00+00:00',
        );
    }
}
