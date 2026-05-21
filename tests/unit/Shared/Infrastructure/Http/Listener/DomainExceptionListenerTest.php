<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Infrastructure\Http\Listener;

use App\Shared\Domain\Exception\DomainException;
use App\Shared\Infrastructure\Http\Listener\DomainExceptionListener;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use RuntimeException;
use Throwable;

final class DomainExceptionListenerTest extends TestCase
{
    private DomainExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new DomainExceptionListener();
    }

    #[Test]
    public function sets_422_json_response_for_domain_exception(): void
    {
        $exception = new class('Something went wrong') extends DomainException {};

        $event = $this->makeEvent($exception);

        ($this->listener)($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame('{"error":"Something went wrong"}', $response->getContent());
    }

    #[Test]
    public function ignores_non_domain_exceptions(): void
    {
        $event = $this->makeEvent(new RuntimeException('Unexpected'));

        ($this->listener)($event);

        $this->assertNull($event->getResponse());
    }

    private function makeEvent(Throwable $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/'),
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );
    }
}
