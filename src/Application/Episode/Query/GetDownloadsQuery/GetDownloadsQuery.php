<?php

declare(strict_types=1);

namespace App\Application\Episode\Query\GetDownloadsQuery;

use App\Domain\Episode\ValueObject\DateRange;
use App\Domain\Shared\Bus\Query;
use App\Domain\Shared\ValueObject\EpisodeId;
use App\Domain\Shared\ValueObject\PodcastId;

final readonly class GetDownloadsQuery implements Query
{
    public function __construct(
        public PodcastId $podcastId,
        public EpisodeId $episodeId,
        public DateRange $dateRange,
    ) {}
}
