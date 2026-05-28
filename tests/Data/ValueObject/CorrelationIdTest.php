<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\Data\ValueObject;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use PHPUnit\Framework\TestCase;

class CorrelationIdTest extends TestCase
{
    public function testInheritance(): void
    {
        $correlationId = new CorrelationId('d8d089ec-72c8-44c1-a0bf-1906e5fc3524');
        $this->assertInstanceOf(\BenjaminRqt\CorrelationIdBundle\Data\ValueObject\Uuid::class, $correlationId);
    }
}
