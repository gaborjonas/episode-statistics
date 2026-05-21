<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Http\Request;

use App\Domain\Episode\ValueObject\DateRange;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DownloadsRequest
{
    public function __construct(
        #[Assert\DateTime(format: DateTimeInterface::ATOM)]
        public ?string $from = null,
        #[Assert\DateTime(format: DateTimeInterface::ATOM)]
        public ?string $to = null,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function toDateRange(): DateRange
    {
        $utc   = new DateTimeZone('UTC');
        $today = new DateTimeImmutable('today', $utc);

        $from = $this->from !== null
            ? new DateTimeImmutable($this->from, $utc)
            : $today->modify('-6 days');

        $to = $this->to !== null
            ? new DateTimeImmutable($this->to, $utc)
            : $today;

        return new DateRange($from, $to);
    }
}
