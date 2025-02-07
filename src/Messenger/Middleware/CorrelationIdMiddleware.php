<?php

declare(strict_types=1);

namespace ComCompany\CorrelationIdBundle\Messenger\Middleware;

use ComCompany\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use ComCompany\CorrelationIdBundle\Messenger\Stamp\CorrelationIdStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CorrelationIdMiddleware implements MiddlewareInterface
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @throws \Exception
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (isset($envelope->all()[CorrelationIdStamp::class])) {
            return $stack->next()->handle($envelope, $stack);
        }

        $envelope = $envelope->with(
            new CorrelationIdStamp(CorrelationId::generate()->getId())
        );

        return $stack->next()->handle($envelope, $stack);
    }
}
