<?php

declare(strict_types=1);

namespace App\Tests\unit\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository;

use App\Domain\IncomingEvent\Projection\IncomingEvent;
use App\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository\IncomingEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class IncomingEventRepositoryTest extends TestCase
{
    private EntityManagerInterface&MockObject $em;
    private IncomingEventRepository $repository;

    protected function setUp(): void
    {
        $this->em         = $this->createMock(EntityManagerInterface::class);
        $this->repository = new IncomingEventRepository($this->em);
    }

    public function test_exists_returns_true_when_entity_found(): void
    {
        $entity = IncomingEvent::create('id-1', 'type', new DateTimeImmutable(), [], new DateTimeImmutable());

        $this->em->expects(self::once())
            ->method('find')
            ->with(IncomingEvent::class, 'id-1')
            ->willReturn($entity);

        self::assertTrue($this->repository->exists('id-1'));
    }

    public function test_exists_returns_false_when_entity_not_found(): void
    {
        $this->em->expects(self::once())
            ->method('find')
            ->with(IncomingEvent::class, 'id-missing')
            ->willReturn(null);

        self::assertFalse($this->repository->exists('id-missing'));
    }

    public function test_append_persists_and_flushes(): void
    {
        $event = IncomingEvent::create(
            'id-1',
            'episode.download',
            new DateTimeImmutable(),
            ['episode_id' => 'ep-1'],
            new DateTimeImmutable(),
        );

        $this->em->expects(self::once())->method('persist')->with($event);
        $this->em->expects(self::once())->method('flush');

        $this->repository->append($event);
    }
}
