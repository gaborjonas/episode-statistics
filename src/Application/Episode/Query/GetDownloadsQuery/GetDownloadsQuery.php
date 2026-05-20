<?php

declare(strict_types=1);

namespace App\Application\Episode\Query\GetDownloadsQuery;

use App\Domain\Episode\ValueObject\DateRange;
use App\Shared\Domain\Bus\Query;
use App\Shared\ValueObject\EpisodeId;
use App\Shared\ValueObject\PodcastId;

final readonly class GetDownloadsQuery implements Query
{
    public function __construct(
        public PodcastId $podcastId,
        public EpisodeId $episodeId,
        public DateRange $dateRange,
    ) {}
}
