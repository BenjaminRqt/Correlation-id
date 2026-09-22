<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\DependencyInjection;

use BenjaminRqt\CorrelationIdBundle\Sentry\SentryCorrelationIdListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CorrelationIdExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('correlation_id.header_name', $config['header_name']);

        (new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config')))
            ->load('services.php');

        if (class_exists(\Sentry\State\Scope::class)) {
            $container->register(SentryCorrelationIdListener::class)
                ->setArguments(['%correlation_id.header_name%'])
                ->addTag('kernel.event_subscriber')
                ->setAutowired(false);
        }
    }
}
