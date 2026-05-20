<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus;

interface QueryBus
{
    public function dispatch(object $query): mixed;
}
