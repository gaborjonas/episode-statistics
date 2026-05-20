<?php

declare(strict_types=1);

namespace App\Application\Episode\Query\GetDownloadsQuery;

use App\Application\Episode\DTO\DownloadsResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetDownloadsQueryHandler
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function __invoke(GetDownloadsQuery $query): DownloadsResult
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('count', 'count', 'integer');

        $rows = $this->em->createNativeQuery(
            'SELECT DATE(occurred_at) AS date, COUNT(*)::int AS count
             FROM episode_downloads
             WHERE podcast_id  = :podcastId
               AND episode_id  = :episodeId
               AND occurred_at >= :from
               AND occurred_at  < :toExclusive
             GROUP BY DATE(occurred_at)
             ORDER BY date ASC',
            $rsm,
        )->setParameters([
            'podcastId'   => $query->podcastId->toString(),
            'episodeId'   => $query->episodeId->toString(),
            'from'        => $query->dateRange->from->format('Y-m-d'),
            'toExclusive' => $query->dateRange->to->modify('+1 day')->format('Y-m-d'),
        ])->getArrayResult();

        $countsByDate = array_column($rows, 'count', 'date');

        $downloads = [];
        $current   = $query->dateRange->from;
        while ($current <= $query->dateRange->to) {
            $date        = $current->format('Y-m-d');
            $downloads[] = ['date' => $date, 'count' => (int) ($countsByDate[$date] ?? 0)];
            $current     = $current->modify('+1 day');
        }

        return new DownloadsResult(
            podcastId: $query->podcastId->toString(),
            episodeId: $query->episodeId->toString(),
            from:      $query->dateRange->from->format('Y-m-d'),
            to:        $query->dateRange->to->format('Y-m-d'),
            downloads: $downloads,
        );
    }
}
