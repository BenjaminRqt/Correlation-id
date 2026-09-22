<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\Sentry;

use BenjaminRqt\CorrelationIdBundle\Sentry\SentryCorrelationIdListener;
use PHPUnit\Framework\TestCase;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SentryCorrelationIdListenerTest extends TestCase
{
    private const string CORRELATION_ID = 'd8d089ec-72c8-44c1-a0bf-1906e5fc3524';
    private const string HEADER_NAME = 'X-Correlation-ID';

    private HubInterface $hub;

    protected function setUp(): void
    {
        if (!class_exists(Hub::class)) {
            $this->markTestSkipped('Sentry SDK is not installed.');
        }

        $this->hub = new Hub();
        SentrySdk::setCurrentHub($this->hub);
    }

    public function testOnKernelRequestSetsCorrelationIdTag(): void
    {
        $request = new Request();
        $request->attributes->set(self::HEADER_NAME, self::CORRELATION_ID);

        (new SentryCorrelationIdListener(self::HEADER_NAME))
            ->onKernelRequest($this->requestEvent($request));

        self::assertSame(self::CORRELATION_ID, $this->currentScopeTag('correlation_id'));
    }

    public function testOnKernelRequestDoesNothingWhenNoCorrelationId(): void
    {
        (new SentryCorrelationIdListener(self::HEADER_NAME))
            ->onKernelRequest($this->requestEvent(new Request()));

        self::assertNull($this->currentScopeTag('correlation_id'));
    }

    public function testOnKernelRequestIgnoresSubRequests(): void
    {
        $request = new Request();
        $request->attributes->set(self::HEADER_NAME, self::CORRELATION_ID);

        (new SentryCorrelationIdListener(self::HEADER_NAME))
            ->onKernelRequest($this->requestEvent($request, HttpKernelInterface::SUB_REQUEST));

        self::assertNull($this->currentScopeTag('correlation_id'));
    }

    public function testGetSubscribedEvents(): void
    {
        $events = SentryCorrelationIdListener::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::REQUEST, $events);
        self::assertSame(['onKernelRequest', 250], $events[KernelEvents::REQUEST]);
    }

    private function requestEvent(
        Request $request,
        int $type = HttpKernelInterface::MAIN_REQUEST,
    ): RequestEvent {
        return new RequestEvent($this->createMock(HttpKernelInterface::class), $request, $type);
    }

    private function currentScopeTag(string $key): ?string
    {
        $event = Event::createEvent();
        $this->hub->configureScope(static fn (Scope $scope) => $scope->applyToEvent($event));

        return $event->getTags()[$key] ?? null;
    }
}
