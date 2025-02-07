<?php

declare(strict_types=1);

namespace ComCompany\CorrelationIdBundle;

use ComCompany\CorrelationIdBundle\DependencyInjection\CorrelationIdExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class CorrelationIdBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new CorrelationIdExtension();
    }
}
