<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\EventId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class IdTest extends TestCase
{
    private const string VALID_UUID = '550e8400-e29b-41d4-a716-446655440001';

    #[Test]
    public function rejects_uuid_with_leading_garbage(): void
    {
        // kills mutant: removing ^ anchor lets "xxx-<uuid>" match
        $this->expectException(Throwable::class);

        EventId::fromString('xxx-' . self::VALID_UUID);
    }

    #[Test]
    public function rejects_uuid_with_trailing_garbage(): void
    {
        // kills mutant: removing $ anchor lets "<uuid>-extra" match
        $this->expectException(Throwable::class);

        EventId::fromString(self::VALID_UUID . '-extra');
    }

    #[Test]
    public function accepts_uppercase_hex_uuid(): void
    {
        // kills mutant: removing i flag rejects uppercase hex digits
        $upper = strtoupper(self::VALID_UUID);

        $id = EventId::fromString($upper);

        $this->assertSame($upper, $id->toString());
    }
}
