<?php

declare(strict_types=1);

namespace App\Infrastructure\Episode\Http\Controller;

use App\Application\Episode\DTO\DownloadsResult;
use App\Application\Episode\Query\GetDownloadsQuery\GetDownloadsQuery;
use App\Infrastructure\Episode\Http\Request\DownloadsRequest;
use App\Shared\Domain\Bus\QueryBus;
use App\Shared\Domain\ValueObject\EpisodeId;
use App\Shared\Domain\ValueObject\PodcastId;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[AsController]
#[OA\Tag(name: 'Downloads')]
final readonly class DownloadsController
{
    public function __construct(
        private QueryBus $queryBus
    ) {}

    #[OA\Get(
        description: 'Returns a contiguous 7-day time series of daily download counts scoped to a specific episode within a podcast. Days with no downloads are included with count 0.',
        summary: 'Daily download counts for an episode within a podcast',
        parameters: [
            new OA\Parameter(
                name: 'podcastId', in: 'path', required: true, schema: new OA\Schema(
                type: 'string',
                format: 'uuid',
            ),
            ),
            new OA\Parameter(
                name: 'episodeId', in: 'path', required: true, schema: new OA\Schema(
                type: 'string',
                format: 'uuid',
            ),
            ),
            new OA\Parameter(
                name: 'from', in: 'query', required: false, schema: new OA\Schema(
                type: 'string',
                format: 'datetime',
                example: '2026-05-13',
            ),
            ),
            new OA\Parameter(
                name: 'to', in: 'query', required: false, schema: new OA\Schema(
                type: 'string',
                format: 'datetime',
                example: '2026-05-19',
            ),
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Daily download time series'),
            new OA\Response(response: 400, description: 'Invalid podcast or episode UUID'),
            new OA\Response(response: 422, description: 'Invalid date range'),
        ],
    )]
    #[Route(path: '/podcasts/{podcastId}/episodes/{episodeId}/downloads',
        name: 'episode_downloads',
        requirements: [
            'podcastId' => Requirement::UUID,
            'episodeId' => Requirement::UUID,
        ],
        methods: ['GET']
    )]
    public function __invoke(
        string $podcastId,
        string $episodeId,
        #[MapQueryString] DownloadsRequest $query = new DownloadsRequest(),
    ): JsonResponse {
        $podcast = PodcastId::fromString($podcastId);
        $episode = EpisodeId::fromString($episodeId);

        /** @var DownloadsResult $result */
        $result = $this->queryBus->dispatch(new GetDownloadsQuery($podcast, $episode, $query->toDateRange()));

        return new JsonResponse([
            'podcast_id' => $result->podcastId,
            'episode_id' => $result->episodeId,
            'from' => $result->from,
            'to' => $result->to,
            'downloads' => $result->downloads,
        ]);
    }
}
