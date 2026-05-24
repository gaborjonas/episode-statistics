<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Bus;

use App\Domain\Shared\Bus\EventBus;
use App\Domain\Shared\Event\DomainEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $eventBus
    ) {}

    public function dispatch(DomainEvent $event): void
    {
        $this->eventBus->dispatch($event);
    }
}
