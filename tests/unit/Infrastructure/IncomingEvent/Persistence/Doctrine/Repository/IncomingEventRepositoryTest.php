<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository;

use App\Domain\IncomingEvent\Projection\IncomingEvent;
use App\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository\IncomingEventRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class IncomingEventRepositoryTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    #[Test]
    public function exists_returns_true_when_row_found(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchOne')
            ->with('SELECT 1 FROM incoming_events WHERE id = :id', ['id' => 'id-1'])
            ->willReturn('1');
        $this->em->expects($this->once())->method('getConnection')->willReturn($connection);

        $this->assertTrue((new IncomingEventRepository($this->em))->exists('id-1'));
    }

    #[Test]
    public function exists_returns_false_when_row_not_found(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchOne')
            ->with('SELECT 1 FROM incoming_events WHERE id = :id', ['id' => 'id-missing'])
            ->willReturn(false);
        $this->em->expects($this->once())->method('getConnection')->willReturn($connection);

        $this->assertFalse((new IncomingEventRepository($this->em))->exists('id-missing'));
    }

    #[Test]
    public function append_persists_and_flushes(): void
    {
        $event = IncomingEvent::create(
            'id-1',
            'episode.download',
            new DateTimeImmutable(),
            ['episode_id' => 'ep-1'],
            new DateTimeImmutable(),
        );

        $this->em->expects($this->once())->method('persist')->with($event);
        $this->em->expects($this->once())->method('flush');

        (new IncomingEventRepository($this->em))->append($event);
    }
}
