<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\IncomingEvent\Projection;

use App\Domain\IncomingEvent\Projection\IncomingEvent;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class IncomingEventTest extends TestCase
{
    public function test_create_sets_all_properties(): void
    {
        $id         = 'abc12345-0000-0000-0000-000000000001';
        $type       = 'episode.download';
        $occurredAt = new DateTimeImmutable('2024-03-15 10:00:00');
        $data       = ['episode_id' => 'ep-1', 'podcast_id' => 'pod-1'];
        $createdAt  = new DateTimeImmutable('2024-03-15 10:01:00');

        $event = IncomingEvent::create($id, $type, $occurredAt, $data, $createdAt);

        self::assertSame($id, $event->id);
        self::assertSame($type, $event->type);
        self::assertSame($occurredAt, $event->occurredAt);
        self::assertSame($data, $event->data);
        self::assertSame($createdAt, $event->createdAt);
    }
}
