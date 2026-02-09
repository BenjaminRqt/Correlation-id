<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Http;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class CorrelationIdHttpClient implements HttpClientInterface
{
    public function __construct(
        private HttpClientInterface $decoratedHttpClient,
        private readonly RequestStack $requestStack,
        private readonly string $headerName
    ) {
    }

    /**
     * @param array{headers?: array<string, string>} $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $correlationId = $request?->attributes->get($this->headerName);

        $options['headers'] = array_merge($options['headers'] ?? [], [
            $this->headerName => $correlationId ?? CorrelationId::generate()->getId(),
        ]);

        return $this->decoratedHttpClient->request($method, $url, $options);
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->decoratedHttpClient->stream($responses, $timeout);
    }

    /**
     * @param array<mixed> $options
     */
    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->decoratedHttpClient = $this->decoratedHttpClient->withOptions($options);

        return $clone;
    }
}
