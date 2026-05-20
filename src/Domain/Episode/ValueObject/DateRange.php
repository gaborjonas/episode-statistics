<?php

declare(strict_types=1);

namespace App\Domain\Episode\ValueObject;

use App\Domain\Episode\Exception\InvalidDateRangeException;
use DateTimeImmutable;

final readonly class DateRange
{
    public const int MAX_DAYS = 365;

    public function __construct(
        public DateTimeImmutable $from,
        public DateTimeImmutable $to,
    ) {
        if ($from > $to) {
            throw InvalidDateRangeException::fromMustNotExceedTo();
        }

        if ((int) $from->diff($to)->days > self::MAX_DAYS) {
            throw InvalidDateRangeException::exceedsMaxRange(self::MAX_DAYS);
        }
    }
}
