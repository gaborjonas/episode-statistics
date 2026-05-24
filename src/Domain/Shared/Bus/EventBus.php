<?php

declare(strict_types=1);

namespace App\Domain\Shared\Bus;

use App\Domain\Shared\Event\DomainEvent;

interface EventBus
{
    public function dispatch(DomainEvent $event): void;
}
