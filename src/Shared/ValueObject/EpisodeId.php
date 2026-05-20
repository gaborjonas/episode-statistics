<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

use App\Shared\Exception\InvalidEpisodeIdException;

final class EpisodeId extends Id
{
    public function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw InvalidEpisodeIdException::fromRawValue($value);
        }

        $this->value = $value;
    }

}
