<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\DependencyInjection;

use BenjaminRqt\CorrelationIdBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, []);

        $this->assertArrayHasKey('header_name', $config);
        $this->assertSame('X-Correlation-ID', $config['header_name']);
    }

    public function testCustomConfiguration(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, [
            'correlation_id' => [
                'header_name' => 'Custom-Header',
            ],
        ]);

        $this->assertArrayHasKey('header_name', $config);
        $this->assertSame('Custom-Header', $config['header_name']);
    }
}
