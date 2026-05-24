<?php

declare(strict_types=1);

namespace App\Domain\Episode\Exception;

use App\Domain\Shared\Exception\DomainException;

final class InvalidDateRangeException extends DomainException
{
    public static function fromMustNotExceedTo(): self
    {
        return new self('"from" must not be after "to"');
    }

}
