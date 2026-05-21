<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Persistence\Doctrine\Repository;

use App\Domain\Episode\Repository\EpisodeDownloadRepositoryInterface;
use App\Domain\Episode\ValueObject\DateRange;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\PodcastId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

final readonly class EpisodeDownloadRepository implements EpisodeDownloadRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function countByDate(PodcastId $podcastId, EpisodeId $episodeId, DateRange $dateRange): array
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
            'podcastId'   => $podcastId->toString(),
            'episodeId'   => $episodeId->toString(),
            'from'        => $dateRange->from->format('Y-m-d'),
            'toExclusive' => $dateRange->to->modify('+1 day')->format('Y-m-d'),
        ])->getArrayResult();

        return array_column($rows, 'count', 'date');
    }
}