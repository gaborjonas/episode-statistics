<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\EventBusInterface;
use App\Shared\Domain\Event\DomainEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerEventBusInterface implements EventBusInterface
{
    public function __construct(
        private MessageBusInterface $eventBus
    ) {}

    public function dispatch(DomainEvent $event): void
    {
        $this->eventBus->dispatch($event);
    }
}
