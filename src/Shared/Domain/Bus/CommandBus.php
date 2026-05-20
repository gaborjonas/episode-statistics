<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus;

interface CommandBus
{
    public function dispatch(object $command): void;
}
