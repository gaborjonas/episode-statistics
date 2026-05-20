<?php

declare(strict_types=1);

namespace App\Infrastructure\IncomingEvent\Http\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class WebHookRequest
{
    /**
     * @param array{episode_id: string, podcast_id: string} $data
     */
    public function __construct(
        #[Assert\NotBlank]
        #[SerializedName('type')]
        public string $type,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[SerializedName('event_id')]
        public string $eventId,
        #[Assert\DateTime]
        #[Assert\NotBlank]
        #[SerializedName('occurred_at')]
        public string $occurredAt,
        #[Assert\Collection(
            fields: [
                'episode_id' => new Assert\Uuid,
                'podcast_id' => new Assert\Uuid,
            ],
        )]
        public array $data,
    ) {}
}
