<?php

declare(strict_types=1);

namespace App\Domain\Download\Exception;

use App\Shared\Domain\Exception\DomainException;

final class InvalidDateRangeException extends DomainException
{
    public static function fromMustNotExceedTo(): self
    {
        return new self('"from" must not be after "to"');
    }

    public static function exceedsMaxRange(int $days): self
    {
        return new self(sprintf('Date range must not exceed %d days', $days));
    }
}
