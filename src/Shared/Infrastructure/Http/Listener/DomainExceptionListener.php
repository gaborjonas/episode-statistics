<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Listener;

use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class DomainExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof DomainException) {
            return;
        }

        $event->setResponse(
            new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ),
        );
    }
}
