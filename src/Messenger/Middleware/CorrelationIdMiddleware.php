<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Messenger\Middleware;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use BenjaminRqt\CorrelationIdBundle\Messenger\Stamp\CorrelationIdStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CorrelationIdMiddleware implements MiddlewareInterface
{
    /**
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
