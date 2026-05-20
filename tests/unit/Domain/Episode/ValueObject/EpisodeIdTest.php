<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Episode\ValueObject;

use App\Shared\Exception\InvalidEpisodeIdException;
use App\Shared\ValueObject\EpisodeId;
use PHPUnit\Framework\TestCase;

final class EpisodeIdTest extends TestCase
{
    private const string VALID_UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_creates_valid_uuid(): void
    {
        $id = new EpisodeId('550e8400-e29b-41d4-a716-446655440000');

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $id->toString());
    }

    public function test_throws_on_invalid_string(): void
    {
        $this->expectException(InvalidEpisodeIdException::class);

        new EpisodeId('not-a-uuid');
    }

    public function test_throws_on_empty_string(): void
    {
        $this->expectException(InvalidEpisodeIdException::class);

        new EpisodeId('');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $a = new EpisodeId(self::VALID_UUID);
        $b = new EpisodeId(self::VALID_UUID);

        self::assertTrue($a->equals($b));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $a = new EpisodeId(self::VALID_UUID);
        $b = new EpisodeId('6ba7b810-9dad-11d1-80b4-00c04fd430c8');

        self::assertFalse($a->equals($b));
    }

    public function test_from_string_named_constructor(): void
    {
        $id = EpisodeId::fromString(self::VALID_UUID);

        self::assertSame(self::VALID_UUID, $id->toString());
    }
}
