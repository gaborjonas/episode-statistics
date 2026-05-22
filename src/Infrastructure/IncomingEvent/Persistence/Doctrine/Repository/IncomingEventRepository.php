<?php

declare(strict_types=1);

namespace App\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository;

use App\Domain\IncomingEvent\Repository\IncomingEventRepositoryInterface;
use App\Infrastructure\IncomingEvent\Persistence\Doctrine\Projection\IncomingEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final readonly class IncomingEventRepository implements IncomingEventRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function exists(string $id): bool
    {
        return $this->em->find(IncomingEvent::class, $id) !== null;
    }

    /** @param array<string,mixed> $data */
    public function append(
        string $id,
        string $type,
        DateTimeImmutable $occurredAt,
        array $data,
        DateTimeImmutable $createdAt,
    ): void {
        $this->em->persist(IncomingEvent::create($id, $type, $occurredAt, $data, $createdAt));
        $this->em->flush();
    }
}
