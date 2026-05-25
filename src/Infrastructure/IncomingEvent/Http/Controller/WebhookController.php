<?php

declare(strict_types=1);

namespace App\Infrastructure\IncomingEvent\Http\Controller;

use App\Application\IncomingEvent\Command\RecordIncomingEventCommand;
use App\Domain\Shared\Bus\CommandBus;
use App\Infrastructure\IncomingEvent\Http\Request\WebHookRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[OA\Tag(name: 'Webhooks')]
final readonly class WebhookController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    #[OA\Post(
        description: 'Accepts platform events. Unknown event types are acknowledged (200) and ignored.',
        summary: 'Receive a platform webhook event',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['type', 'event_id', 'occurred_at', 'data'],
                properties: [
                    new OA\Property(property: 'type', type: 'string', example: 'episode.downloaded'),
                    new OA\Property(property: 'event_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'occurred_at', type: 'string', format: 'date-time'),
                    new OA\Property(
                        property: 'data',
                        properties: [
                            new OA\Property(property: 'episode_id', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'podcast_id', type: 'string', format: 'uuid'),
                        ],
                        type: 'object',
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 202, description: 'IncomingEvent acknowledged'),
            new OA\Response(response: 422, description: 'Invalid event data'),
        ],
    )]
    #[Route(path: '/webhook', name: 'webhook', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] WebHookRequest $request,
    ): JsonResponse {
        $command = new RecordIncomingEventCommand(
            eventId: $request->eventId,
            type: $request->type,
            occurredAt: $request->occurredAt,
            data: $request->data,
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse([
            'message' => 'Request accepted.',
        ], Response::HTTP_ACCEPTED);
    }
}
