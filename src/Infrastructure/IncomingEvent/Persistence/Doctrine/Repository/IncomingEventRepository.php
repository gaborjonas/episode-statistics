<?php

declare(strict_types=1);

namespace App\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository;

use App\Domain\IncomingEvent\Projection\IncomingEvent;
use App\Domain\IncomingEvent\Repository\IncomingEventRepositoryInterface;
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

    public function append(IncomingEvent $event): void
    {
        $this->em->persist($event);
        $this->em->flush();
    }
}
