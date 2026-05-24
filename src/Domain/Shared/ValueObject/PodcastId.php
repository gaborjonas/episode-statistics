<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidPodcastIdException;

final class PodcastId extends Id
{

    public function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw InvalidPodcastIdException::fromRawValue($value);
        }

        $this->value = $value;
    }
}
