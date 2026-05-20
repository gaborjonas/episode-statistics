<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Infrastructure\Bus;

use App\Shared\Infrastructure\Bus\MessengerCommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use stdClass;

final class MessengerCommandBusTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;
    private MessengerCommandBus $commandBus;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->commandBus = new MessengerCommandBus($this->messageBus);
    }

    public function test_delegates_dispatch_to_messenger_bus(): void
    {
        $command = new stdClass();

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command));

        $this->commandBus->dispatch($command);
    }
}
