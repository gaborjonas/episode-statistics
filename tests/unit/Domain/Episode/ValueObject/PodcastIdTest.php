<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Episode\ValueObject;

use App\Shared\Domain\Exception\InvalidPodcastIdException;
use App\Shared\Domain\ValueObject\PodcastId;
use PHPUnit\Framework\TestCase;

final class PodcastIdTest extends TestCase
{
    private const string VALID_UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_creates_valid_uuid(): void
    {
        $id = new PodcastId('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $id->toString());
    }

    public function test_throws_on_invalid_string(): void
    {
        $this->expectException(InvalidPodcastIdException::class);

        new PodcastId('not-a-uuid');
    }

    public function test_throws_on_empty_string(): void
    {
        $this->expectException(InvalidPodcastIdException::class);

        new PodcastId('');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $a = new PodcastId(self::VALID_UUID);
        $b = new PodcastId(self::VALID_UUID);

        $this->assertTrue($a->equals($b));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $a = new PodcastId(self::VALID_UUID);
        $b = new PodcastId('6ba7b810-9dad-11d1-80b4-00c04fd430c8');

        $this->assertFalse($a->equals($b));
    }

    public function test_from_string_named_constructor(): void
    {
        $id = PodcastId::fromString(self::VALID_UUID);

        $this->assertSame(self::VALID_UUID, $id->toString());
    }
}
