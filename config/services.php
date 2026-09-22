<?php

declare(strict_types=1);

use BenjaminRqt\CorrelationIdBundle\EventSubscriber\CorrelationIdSubscriber;
use BenjaminRqt\CorrelationIdBundle\Http\CorrelationIdHttpClient;
use BenjaminRqt\CorrelationIdBundle\Logger\CorrelationIdProcessor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(CorrelationIdSubscriber::class)
        ->arg('$headerName', '%correlation_id.header_name%')
        ->tag('kernel.event_subscriber');

    $services->set(CorrelationIdProcessor::class)
        ->arg('$requestStack', service('request_stack'))
        ->arg('$headerName', '%correlation_id.header_name%')
        ->tag('monolog.processor');

    $services->set(CorrelationIdHttpClient::class)
        ->decorate('http_client.transport', priority: 10)
        ->arg('$decoratedHttpClient', service('.inner'))
        ->arg('$requestStack', service('request_stack'))
        ->arg('$headerName', '%correlation_id.header_name%');
};