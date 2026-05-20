<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\Episode\Http\Request;

use App\Domain\Episode\ValueObject\DateRange;
use App\Infrastructure\Episode\Http\Request\DownloadsQueryRequest;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class DownloadsQueryRequestTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }

    public function test_to_date_range_uses_explicit_dates(): void
    {
        $request = new DownloadsQueryRequest(from: '2024-03-10', to: '2024-03-15');

        $range = $request->toDateRange();

        $this->assertSame('2024-03-10', $range->from->format('Y-m-d'));
        $this->assertSame('2024-03-15', $range->to->format('Y-m-d'));
    }

    public function test_to_date_range_defaults_to_last_7_days_when_null(): void
    {
        $request = new DownloadsQueryRequest();
        $utc     = new DateTimeZone('UTC');

        $range = $request->toDateRange();

        $expectedFrom = new DateTimeImmutable('today', $utc)->modify('-6 days');
        $expectedTo   = new DateTimeImmutable('today', $utc);

        $this->assertSame($expectedFrom->format('Y-m-d'), $range->from->format('Y-m-d'));
        $this->assertSame($expectedTo->format('Y-m-d'), $range->to->format('Y-m-d'));
    }

    public function test_to_date_range_returns_date_range_instance(): void
    {
        $request = new DownloadsQueryRequest(from: '2024-01-01', to: '2024-01-07');

        $this->assertInstanceOf(DateRange::class, $request->toDateRange());
    }

    public function test_validate_date_range_skips_when_from_is_null(): void
    {
        $request = new DownloadsQueryRequest(from: null, to: '2024-03-15');

        $this->context->expects($this->never())->method('buildViolation');

        $request->validateDateRange($this->context, null);
    }

    public function test_validate_date_range_skips_when_to_is_null(): void
    {
        $request = new DownloadsQueryRequest(from: '2024-03-10', to: null);

        $this->context->expects($this->never())->method('buildViolation');

        $request->validateDateRange($this->context, null);
    }

    public function test_validate_date_range_skips_when_both_null(): void
    {
        $request = new DownloadsQueryRequest();

        $this->context->expects($this->never())->method('buildViolation');

        $request->validateDateRange($this->context, null);
    }

    public function test_validate_date_range_adds_violation_when_from_exceeds_to(): void
    {
        $request = new DownloadsQueryRequest(from: '2024-03-15', to: '2024-03-10');

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('atPath')->with('from')->willReturnSelf();
        $builder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with('"from" must not be after "to"')
            ->willReturn($builder);

        $request->validateDateRange($this->context, null);
    }

    public function test_validate_date_range_adds_violation_when_range_exceeds_max_days(): void
    {
        $from    = '2024-01-01';
        $to      = (new DateTimeImmutable($from))->modify('+' . (DateRange::MAX_DAYS + 1) . ' days')->format('Y-m-d');
        $request = new DownloadsQueryRequest(from: $from, to: $to);

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())->method('atPath')->with('from')->willReturnSelf();
        $builder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with(sprintf('Date range must not exceed %d days', DateRange::MAX_DAYS))
            ->willReturn($builder);

        $request->validateDateRange($this->context, null);
    }

    public function test_validate_date_range_passes_for_valid_range(): void
    {
        $request = new DownloadsQueryRequest(from: '2024-03-10', to: '2024-03-15');

        $this->context->expects($this->never())->method('buildViolation');

        $request->validateDateRange($this->context, null);
    }
}
