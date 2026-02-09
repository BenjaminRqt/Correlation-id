<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\EventSubscriber;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use Exception;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorrelationIdSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly string $headerName)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequest()->headers->has($this->headerName)) {
            $event->getRequest()->attributes->set(
                $this->headerName,
                $event->getRequest()->headers->get($this->headerName)
            );

            return;
        }

        $event->getRequest()->attributes->set($this->headerName, CorrelationID::generate()->getId());
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($event->getRequest()->headers->has($this->headerName)) {
            $event->getResponse()->headers->set(
                $this->headerName,
                $event->getRequest()->headers->get($this->headerName)
            );

            return;
        }

        $event->getResponse()->headers->set($this->headerName, CorrelationID::generate()->getId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 255],
            KernelEvents::RESPONSE => ['onKernelResponse', -255],
        ];
    }
}
