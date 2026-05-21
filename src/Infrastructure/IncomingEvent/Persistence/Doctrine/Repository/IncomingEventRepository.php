<?php

declare(strict_types=1);

namespace App\Infrastructure\IncomingEvent\Persistence\Doctrine\Repository;

use App\Domain\IncomingEvent\Projection\IncomingEvent;
use App\Domain\IncomingEvent\Repository\IncomingEventRepositoryInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

final readonly class IncomingEventRepository implements IncomingEventRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /** @throws Exception */
    public function exists(string $id): bool
    {
        return (bool) $this->em->getConnection()->fetchOne(
            'SELECT 1 FROM incoming_events WHERE id = :id',
            ['id' => $id],
        );
    }

    public function append(IncomingEvent $event): void
    {
        $this->em->persist($event);
        $this->em->flush();
    }
}
