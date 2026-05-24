<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Episode\ValueObject;

use App\Domain\Shared\Exception\InvalidEpisodeIdException;
use App\Domain\Shared\ValueObject\EpisodeId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EpisodeIdTest extends TestCase
{
    private const string VALID_UUID = '550e8400-e29b-41d4-a716-446655440000';

    #[Test]
    public function creates_valid_uuid(): void
    {
        $id = new EpisodeId('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $id->toString());
    }

    #[Test]
    public function throws_on_invalid_string(): void
    {
        $this->expectException(InvalidEpisodeIdException::class);

        new EpisodeId('not-a-uuid');
    }

    #[Test]
    public function throws_on_empty_string(): void
    {
        $this->expectException(InvalidEpisodeIdException::class);

        new EpisodeId('');
    }

    #[Test]
    public function equals_returns_true_for_same_value(): void
    {
        $a = new EpisodeId(self::VALID_UUID);
        $b = new EpisodeId(self::VALID_UUID);

        $this->assertTrue($a->equals($b));
    }

    #[Test]
    public function equals_returns_false_for_different_value(): void
    {
        $a = new EpisodeId(self::VALID_UUID);
        $b = new EpisodeId('6ba7b810-9dad-11d1-80b4-00c04fd430c8');

        $this->assertFalse($a->equals($b));
    }

    #[Test]
    public function from_string_named_constructor(): void
    {
        $id = EpisodeId::fromString(self::VALID_UUID);

        $this->assertSame(self::VALID_UUID, $id->toString());
    }
}
