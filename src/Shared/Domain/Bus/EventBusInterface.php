<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus;

use App\Shared\Domain\Event\DomainEvent;

interface EventBusInterface
{
    public function dispatch(DomainEvent $event): void;
}
