<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\ValueObject;

use App\Shared\ValueObject\EventId;
use PHPUnit\Framework\TestCase;
use Throwable;

final class IdTest extends TestCase
{
    private const string VALID_UUID = '550e8400-e29b-41d4-a716-446655440001';

    public function test_rejects_uuid_with_leading_garbage(): void
    {
        // kills mutant: removing ^ anchor lets "xxx-<uuid>" match
        $this->expectException(Throwable::class);

        EventId::fromString('xxx-' . self::VALID_UUID);
    }

    public function test_rejects_uuid_with_trailing_garbage(): void
    {
        // kills mutant: removing $ anchor lets "<uuid>-extra" match
        $this->expectException(Throwable::class);

        EventId::fromString(self::VALID_UUID . '-extra');
    }

    public function test_accepts_uppercase_hex_uuid(): void
    {
        // kills mutant: removing i flag rejects uppercase hex digits
        $upper = strtoupper(self::VALID_UUID);

        $id = EventId::fromString($upper);

        $this->assertSame($upper, $id->toString());
    }
}
