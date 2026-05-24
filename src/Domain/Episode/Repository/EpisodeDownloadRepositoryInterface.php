<?php

declare(strict_types=1);

namespace App\Domain\Episode\Repository;

use App\Domain\Episode\ValueObject\DateRange;
use App\Domain\Shared\ValueObject\EpisodeId;
use App\Domain\Shared\ValueObject\PodcastId;
use App\Infrastructure\Episode\Persistence\Doctrine\Projection\EpisodeDownload;

interface EpisodeDownloadRepositoryInterface
{
    public function save(EpisodeDownload $episodeDownload): void;

    /** @return array<string, int> */
    public function countByDate(PodcastId $podcastId, EpisodeId $episodeId, DateRange $dateRange): array;
}
