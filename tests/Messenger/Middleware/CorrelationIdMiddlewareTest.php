<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\Messenger\Middleware;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use BenjaminRqt\CorrelationIdBundle\Messenger\Middleware\CorrelationIdMiddleware;
use BenjaminRqt\CorrelationIdBundle\Messenger\Stamp\CorrelationIdStamp;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

class CorrelationIdMiddlewareTest extends TestCase
{
    private const string CORRELATION_ID = 'd8d089ec-72c8-44c1-a0bf-1906e5fc3524';

    protected function tearDown(): void
    {
        CorrelationId::setNextGeneratedId(null);
        $reflection = new \ReflectionClass(\BenjaminRqt\CorrelationIdBundle\Data\ValueObject\Uuid::class);
        $property = $reflection->getProperty('generatedInstance');
        $property->setValue(null, null);

        parent::tearDown();
    }

    public function testHandleAddsStampIfMissing(): void
    {
        CorrelationId::setNextGeneratedId(self::CORRELATION_ID);

        $middleware = new CorrelationIdMiddleware();
        $envelope = new Envelope(new stdClass());
        $stack = new StackMiddleware();

        $envelope = $middleware->handle($envelope, $stack);

        $this->assertArrayHasKey(CorrelationIdStamp::class, $envelope->all());
        /** @var CorrelationIdStamp $stamp */
        $stamp = $envelope->last(CorrelationIdStamp::class);
        $this->assertSame(self::CORRELATION_ID, $stamp->getCorrelationId());
    }

    public function testHandleDoesNotOverwriteExistingStamp(): void
    {
        $existingCorrelationId = 'existing-id';
        $middleware = new CorrelationIdMiddleware();
        $envelope = new Envelope(new stdClass(), [new CorrelationIdStamp($existingCorrelationId)]);
        $stack = new StackMiddleware();

        $envelope = $middleware->handle($envelope, $stack);

        $this->assertArrayHasKey(CorrelationIdStamp::class, $envelope->all());
        /** @var CorrelationIdStamp $stamp */
        $stamp = $envelope->last(CorrelationIdStamp::class);
        $this->assertSame($existingCorrelationId, $stamp->getCorrelationId());
    }
}
