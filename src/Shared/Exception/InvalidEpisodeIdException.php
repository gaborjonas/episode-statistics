<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use App\Shared\Domain\Exception\DomainException;

final class InvalidEpisodeIdException extends DomainException
{
    public static function fromRawValue(string $value): self
    {
        return new self(sprintf('"%s" is not a valid episode UUID', $value));
    }
}
