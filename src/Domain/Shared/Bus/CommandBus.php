<?php

declare(strict_types=1);

namespace App\Domain\Shared\Bus;

interface CommandBus
{
    public function dispatch(object $command): void;
}
