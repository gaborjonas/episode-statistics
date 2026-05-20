<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

use App\Shared\Exception\InvalidEventIdException;

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
