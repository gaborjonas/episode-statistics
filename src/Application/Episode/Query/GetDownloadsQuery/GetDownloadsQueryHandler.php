<?php

declare(strict_types=1);

namespace App\Application\Episode\Query\GetDownloadsQuery;

use App\Application\Episode\DTO\DownloadsResult;
use App\Domain\Episode\Repository\EpisodeDownloadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetDownloadsQueryHandler
{
    public function __construct(
        private EpisodeDownloadRepositoryInterface $repository,
    ) {}

    public function __invoke(GetDownloadsQuery $query): DownloadsResult
    {
        $countsByDate = $this->repository->countByDate(
            $query->podcastId,
            $query->episodeId,
            $query->dateRange,
        );

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
