<?php

declare(strict_types=1);

namespace App\Domain\Episode\Exception;

use App\Shared\Domain\Exception\DomainException;
use App\Shared\ValueObject\EpisodeId;

final class EpisodeNotFoundException extends DomainException
{
    public static function withId(EpisodeId $id): self
    {
        return new self(sprintf('Episode "%s" not found', $id->toString()));
    }
}
