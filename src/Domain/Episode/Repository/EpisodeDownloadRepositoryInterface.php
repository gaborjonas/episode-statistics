<?php

declare(strict_types=1);

namespace App\Domain\Episode\Repository;

use App\Domain\Episode\ValueObject\DateRange;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\PodcastId;

interface EpisodeDownloadRepositoryInterface
{
    /** @return array<string, int> */
    public function countByDate(PodcastId $podcastId, EpisodeId $episodeId, DateRange $dateRange): array;
}
