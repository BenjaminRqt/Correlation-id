<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\Logger;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use BenjaminRqt\CorrelationIdBundle\Logger\CorrelationIdProcessor;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CorrelationIdProcessorTest extends TestCase
{
    private const HEADER_NAME = 'X-Correlation-ID';
    private const CORRELATION_ID = 'd8d089ec-72c8-44c1-a0bf-1906e5fc3524';

    protected function tearDown(): void
    {
        CorrelationId::setNextGeneratedId(null);
        $reflection = new \ReflectionClass(\BenjaminRqt\CorrelationIdBundle\Data\ValueObject\Uuid::class);
        $property = $reflection->getProperty('generatedInstance');
        $property->setAccessible(true);
        $property->setValue(null, null);

        parent::tearDown();
    }

    public function testInvokeWithRequestHavingHeader(): void
    {
        $request = new Request();
        $request->headers->set(self::HEADER_NAME, self::CORRELATION_ID);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $processor = new CorrelationIdProcessor($requestStack, self::HEADER_NAME);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: []
        );

        $result = $processor($record);

        $this->assertArrayHasKey('correlation_id', $result->extra);
        $this->assertSame(self::CORRELATION_ID, $result->extra['correlation_id']);
    }

    public function testInvokeWithRequestMissingHeader(): void
    {
        CorrelationId::setNextGeneratedId(self::CORRELATION_ID);

        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $processor = new CorrelationIdProcessor($requestStack, self::HEADER_NAME);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: []
        );

        $result = $processor($record);

        $this->assertArrayHasKey('correlation_id', $result->extra);
        $this->assertSame(self::CORRELATION_ID, $result->extra['correlation_id']);
    }

    public function testInvokeWithoutRequest(): void
    {
        $requestStack = new RequestStack();
        $processor = new CorrelationIdProcessor($requestStack, self::HEADER_NAME);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
            extra: []
        );

        $result = $processor($record);

        $this->assertArrayNotHasKey('correlation_id', $result->extra);
    }
}
