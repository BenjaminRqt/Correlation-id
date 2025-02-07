<?php

declare(strict_types=1);

namespace ComCompany\CorrelationIdBundle\Tests\Messenger\Middleware;

use ComCompany\CorrelationIdBundle\Messenger\Middleware\CorrelationIdMiddleware;
use ComCompany\CorrelationIdBundle\Messenger\Stamp\CorrelationIdStamp;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

/**
 *
 */
class CorrelationIdMiddlewareTest extends TestCase
{
    public function testHandle(): void
    {
        $middleware = new CorrelationIdMiddleware();
        $envelope = new Envelope(new stdClass());
        $stack = new StackMiddleware();

        $envelope = $middleware->handle($envelope, $stack);

        $this->assertArrayHasKey(CorrelationIdStamp::class, $envelope->all());
    }
}
