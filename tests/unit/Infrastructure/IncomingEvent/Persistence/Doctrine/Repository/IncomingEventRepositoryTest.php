<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository;

use App\Infrastructure\IncomingEvent\Persistence\Doctrine\Projection\IncomingEvent;
use App\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository\IncomingEventRepository;
use DateTimeImmutable;
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
        $this->em->expects($this->once())
            ->method('find')
            ->with(IncomingEvent::class, 'id-1')
            ->willReturn(IncomingEvent::create('id-1', 'episode.download', new DateTimeImmutable(), [], new DateTimeImmutable()));

        $this->assertTrue((new IncomingEventRepository($this->em))->exists('id-1'));
    }

    #[Test]
    public function exists_returns_false_when_row_not_found(): void
    {
        $this->em->expects($this->once())
            ->method('find')
            ->with(IncomingEvent::class, 'id-missing')
            ->willReturn(null);

        $this->assertFalse((new IncomingEventRepository($this->em))->exists('id-missing'));
    }

    #[Test]
    public function append_persists_and_flushes(): void
    {
        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(IncomingEvent::class));
        $this->em->expects($this->once())->method('flush');

        (new IncomingEventRepository($this->em))->append(
            'id-1',
            'episode.download',
            new DateTimeImmutable(),
            ['episode_id' => 'ep-1'],
            new DateTimeImmutable(),
        );
    }
}
