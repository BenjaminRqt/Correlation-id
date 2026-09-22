<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Sentry;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class SentryCorrelationIdListener implements EventSubscriberInterface
{
    public function __construct(private string $headerName)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        /** @var string|null $correlationId */
        $correlationId = $event->getRequest()->attributes->get($this->headerName);

        if (!$correlationId) {
            return;
        }

        \Sentry\configureScope(
            fn (\Sentry\State\Scope $scope) => $scope->setTag('correlation_id', $correlationId)
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 250],
        ];
    }
}
