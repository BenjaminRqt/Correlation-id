<?php

declare(strict_types=1);

namespace ComCompany\CorrelationIdBundle\Logger;

use ComCompany\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CorrelationIdProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly string $headerName
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request) {
            $correlationId = $request->headers->get($this->headerName) ?? CorrelationId::generate()->getId();

            $record = $record->with(extra: [...$record->extra, 'correlation_id' => $correlationId]);
        }

        return $record;
    }
}
