<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidPodcastIdException;

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
