<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\DependencyInjection;

use BenjaminRqt\CorrelationIdBundle\DependencyInjection\CorrelationIdExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CorrelationIdExtensionTest extends TestCase
{
    public function testLoadWithDefaultConfig(): void
    {
        $container = new ContainerBuilder();
        $extension = new CorrelationIdExtension();

        $extension->load([], $container);

        $this->assertTrue($container->hasParameter('correlation_id.header_name'));
        $this->assertSame('X-Correlation-ID', $container->getParameter('correlation_id.header_name'));
    }

    public function testLoadWithCustomConfig(): void
    {
        $container = new ContainerBuilder();
        $extension = new CorrelationIdExtension();

        $extension->load([['header_name' => 'Custom-Header']], $container);

        $this->assertTrue($container->hasParameter('correlation_id.header_name'));
        $this->assertSame('Custom-Header', $container->getParameter('correlation_id.header_name'));
    }
}
