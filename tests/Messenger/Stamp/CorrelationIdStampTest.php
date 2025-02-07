<?php

declare(strict_types=1);

namespace ComCompany\CorrelationIdBundle\Tests\Messenger\Stamp;

use ComCompany\CorrelationIdBundle\Messenger\Stamp\CorrelationIdStamp;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class CorrelationIdStampTest extends TestCase
{
    public function testGetCorrelationId(): void
    {
        $stamp = new CorrelationIdStamp('foo');
        $this->assertEquals('foo', $stamp->getCorrelationId());
    }
}
