<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Http\Request;

use App\Domain\Episode\ValueObject\DateRange;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class DownloadsQueryRequest
{
    public function __construct(
        #[Assert\Date]
        public ?string $from = null,
        #[Assert\Date]
        public ?string $to = null,
    ) {}

    #[Assert\Callback]
    public function validateDateRange(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->from === null || $this->to === null) {
            return;
        }

        $from = new DateTimeImmutable($this->from);
        $to   = new DateTimeImmutable($this->to);

        if ($from > $to) {
            $context->buildViolation('"from" must not be after "to"')
                ->atPath('from')
                ->addViolation();

            return;
        }

        if ((int) $from->diff($to)->days > DateRange::MAX_DAYS) {
            $context->buildViolation(
                sprintf('Date range must not exceed %d days', DateRange::MAX_DAYS),
            )
                ->atPath('from')
                ->addViolation();
        }
    }

    public function toDateRange(): DateRange
    {
        $utc = new DateTimeZone('UTC');

        $from = $this->from !== null
            ? new DateTimeImmutable($this->from, $utc)
            : new DateTimeImmutable('today', $utc)->modify('-6 days');

        $to = $this->to !== null
            ? new DateTimeImmutable($this->to, $utc)
            : new DateTimeImmutable('today', $utc);

        return new DateRange($from, $to);
    }
}
