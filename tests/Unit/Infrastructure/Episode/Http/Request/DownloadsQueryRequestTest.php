<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Episode\Http\Request;

use App\Infrastructure\Episode\Http\Request\DownloadsRequest;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DownloadsQueryRequestTest extends TestCase
{
    #[Test]
    public function to_date_range_uses_explicit_dates(): void
    {
        $request = new DownloadsRequest(from: '2024-03-10', to: '2024-03-15');

        $range = $request->toDateRange();

        $this->assertSame('2024-03-10', $range->from->format('Y-m-d'));
        $this->assertSame('2024-03-15', $range->to->format('Y-m-d'));
    }

    #[Test]
    public function to_date_range_defaults_to_last_7_days_when_null(): void
    {
        $request = new DownloadsRequest();
        $utc     = new DateTimeZone('UTC');

        $range = $request->toDateRange();

        $expectedFrom = new DateTimeImmutable('today', $utc)->modify('-6 days');
        $expectedTo   = new DateTimeImmutable('today', $utc);

        $this->assertSame($expectedFrom->format('Y-m-d'), $range->from->format('Y-m-d'));
        $this->assertSame($expectedTo->format('Y-m-d'), $range->to->format('Y-m-d'));
    }

}
