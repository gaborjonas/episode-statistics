<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Episode\ValueObject;

use App\Domain\Episode\ValueObject\DateRange;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class DateRangeTest extends TestCase
{
    #[Test]
    public function creates_valid_range(): void
    {
        $from = new DateTimeImmutable('2024-01-01');
        $to   = new DateTimeImmutable('2024-01-31');

        $range = new DateRange($from, $to);

        $this->assertSame($from, $range->from);
        $this->assertSame($to, $range->to);
    }

    #[Test]
    public function same_day_from_and_to_is_valid(): void
    {
        $day = new DateTimeImmutable('2024-06-15');

        $range = new DateRange($day, $day);

        $this->assertSame($day, $range->from);
    }

    #[Test]
    public function large_range_is_valid(): void
    {
        $from = new DateTimeImmutable('2024-01-01');
        $to   = $from->modify('+365 days');

        $range = new DateRange($from, $to);

        $this->assertSame($from, $range->from);
    }

    #[Test]
    public function throws_when_from_exceeds_to(): void
    {
        $this->expectException(Throwable::class);

        new DateRange(
            new DateTimeImmutable('2024-02-01'),
            new DateTimeImmutable('2024-01-01'),
        );
    }

}
