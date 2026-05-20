<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidEventIdException;

final class EventId extends Id
{
    public function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw InvalidEventIdException::fromRawValue($value);
        }

        $this->value = $value;
    }
}
