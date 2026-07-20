<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\Data\ValueObject;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    private const string UUID_STRING = 'd8d089ec-72c8-44c1-a0bf-1906e5fc3524';

    protected function tearDown(): void
    {
        Uuid::setNextGeneratedId(null);

        // Reset generatedInstance using reflection since it's private and static
        $reflection = new \ReflectionClass(Uuid::class);
        $property = $reflection->getProperty('generatedInstance');
        $property->setValue(null, null);

        parent::tearDown();
    }

    public function testConstruct(): void
    {
        $uuid = new Uuid(self::UUID_STRING);
        $this->assertSame(self::UUID_STRING, $uuid->getId());
    }

    public function testGenerateNewInstance(): void
    {
        $uuid1 = Uuid::generate();
        $this->assertInstanceOf(Uuid::class, $uuid1);

        // Uuid::generate() stores the instance, so calling it again returns the same instance
        $uuid2 = Uuid::generate();
        $this->assertSame($uuid1, $uuid2);
    }

    public function testSetNextGeneratedId(): void
    {
        Uuid::setNextGeneratedId(self::UUID_STRING);
        $uuid = Uuid::generate();

        $this->assertSame(self::UUID_STRING, $uuid->getId());
    }
}
