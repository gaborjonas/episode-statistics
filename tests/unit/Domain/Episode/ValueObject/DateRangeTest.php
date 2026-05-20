<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Episode\ValueObject;

use App\Domain\Episode\ValueObject\DateRange;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class DateRangeTest extends TestCase
{
    public function test_creates_valid_range(): void
    {
        $from = new DateTimeImmutable('2024-01-01');
        $to   = new DateTimeImmutable('2024-01-31');

        $range = new DateRange($from, $to);

        self::assertSame($from, $range->from);
        self::assertSame($to, $range->to);
    }

    public function test_same_day_from_and_to_is_valid(): void
    {
        $day = new DateTimeImmutable('2024-06-15');

        $range = new DateRange($day, $day);

        self::assertSame($day, $range->from);
    }

    public function test_exactly_365_days_is_valid(): void
    {
        $from = new DateTimeImmutable('2024-01-01');
        $to   = $from->modify('+365 days');

        $range = new DateRange($from, $to);

        self::assertSame($from, $range->from);
    }

    public function test_throws_when_from_exceeds_to(): void
    {
        $this->expectException(\Throwable::class);

        new DateRange(
            new DateTimeImmutable('2024-02-01'),
            new DateTimeImmutable('2024-01-01'),
        );
    }

    public function test_throws_when_range_exceeds_365_days(): void
    {
        $this->expectException(\Throwable::class);

        new DateRange(
            new DateTimeImmutable('2024-01-01'),
            (new DateTimeImmutable('2024-01-01'))->modify('+366 days'),
        );
    }
}
