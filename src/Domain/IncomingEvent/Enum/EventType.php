<?php

declare(strict_types=1);

namespace App\Domain\IncomingEvent\Enum;

enum EventType: string
{
    case EpisodeDownloaded = 'episode.downloaded';

}
