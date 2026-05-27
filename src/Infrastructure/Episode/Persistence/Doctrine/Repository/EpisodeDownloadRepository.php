<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Persistence\Doctrine\Repository;

use App\Domain\Episode\Repository\EpisodeDownloadRepositoryInterface;
use App\Domain\Episode\ValueObject\DateRange;
use App\Domain\Shared\ValueObject\EpisodeId;
use App\Domain\Shared\ValueObject\PodcastId;
use App\Infrastructure\Episode\Persistence\Doctrine\Projection\EpisodeDownload;
use Doctrine\ORM\EntityManagerInterface;

final readonly class EpisodeDownloadRepository implements EpisodeDownloadRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(EpisodeDownload $episodeDownload): void
    {
        $this->em->persist($episodeDownload);
        $this->em->flush();
    }

    public function countByDate(PodcastId $podcastId, EpisodeId $episodeId, DateRange $dateRange): array
    {
        $sql = 'SELECT DATE(occurred_at) AS date, COUNT(*)::int AS count
             FROM episode_downloads
             WHERE podcast_id  = :podcastId
               AND episode_id  = :episodeId
               AND occurred_at >= :from
               AND occurred_at  < :toExclusive
             GROUP BY DATE(occurred_at)
             ORDER BY date ASC';

        $rows = $this->em
            ->getConnection()
            ->executeQuery(
                sql: $sql,
                params: [
                    'podcastId'   => $podcastId->toString(),
                    'episodeId'   => $episodeId->toString(),
                    'from'        => $dateRange->from->format('Y-m-d'),
                    'toExclusive' => $dateRange->to->modify('+1 day')->format('Y-m-d'),
                ]
            )
            ->fetchAllAssociative();

        return array_column($rows, 'count', 'date');
    }
}
