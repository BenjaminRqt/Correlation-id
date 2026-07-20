<?php

declare(strict_types=1);

namespace BenjaminRqt\CorrelationIdBundle\Tests\Http;

use BenjaminRqt\CorrelationIdBundle\Data\ValueObject\CorrelationId;
use BenjaminRqt\CorrelationIdBundle\Http\CorrelationIdHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CorrelationIdHttpClientTest extends TestCase
{
    private const string HEADER_NAME = 'X-Correlation-ID';
    private const string CORRELATION_ID = 'd8d089ec-72c8-44c1-a0bf-1906e5fc3524';

    protected function tearDown(): void
    {
        CorrelationId::setNextGeneratedId(null);
        $reflection = new \ReflectionClass(\BenjaminRqt\CorrelationIdBundle\Data\ValueObject\Uuid::class);
        $property = $reflection->getProperty('generatedInstance');
        $property->setValue(null, null);

        parent::tearDown();
    }

    public function testRequestWithCorrelationIdInRequestAttributes(): void
    {
        $request = new Request();
        $request->attributes->set(self::HEADER_NAME, self::CORRELATION_ID);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://example.com',
                $this->callback(function (array $options) {
                    return isset($options['headers'][self::HEADER_NAME]) &&
                           $options['headers'][self::HEADER_NAME] === self::CORRELATION_ID;
                })
            )
            ->willReturn($mockResponse);

        $client = new CorrelationIdHttpClient($mockHttpClient, $requestStack, self::HEADER_NAME);
        $client->request('GET', 'https://example.com');
    }

    public function testRequestWithoutCorrelationIdInRequestAttributesGeneratesOne(): void
    {
        CorrelationId::setNextGeneratedId(self::CORRELATION_ID);

        $request = new Request();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://example.com',
                $this->callback(function (array $options) {
                    return isset($options['headers'][self::HEADER_NAME]) &&
                           $options['headers'][self::HEADER_NAME] === self::CORRELATION_ID;
                })
            )
            ->willReturn($mockResponse);

        $client = new CorrelationIdHttpClient($mockHttpClient, $requestStack, self::HEADER_NAME);
        $client->request('GET', 'https://example.com');
    }

    public function testStreamDelegatesToDecoratedClient(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $requestStack = new RequestStack();
        $responses = [$this->createMock(ResponseInterface::class)];

        $mockHttpClient->expects($this->once())
            ->method('stream')
            ->with($responses, 10.0);

        $client = new CorrelationIdHttpClient($mockHttpClient, $requestStack, self::HEADER_NAME);
        $client->stream($responses, 10.0);
    }

    public function testWithOptionsReturnsCloneWithNewDecoratedClient(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClientWithOptions = $this->createMock(HttpClientInterface::class);

        $mockHttpClient->expects($this->once())
            ->method('withOptions')
            ->with(['base_uri' => 'https://example.com'])
            ->willReturn($mockHttpClientWithOptions);

        $requestStack = new RequestStack();
        $client = new CorrelationIdHttpClient($mockHttpClient, $requestStack, self::HEADER_NAME);

        $newClient = $client->withOptions(['base_uri' => 'https://example.com']);

        $this->assertNotSame($client, $newClient);

        // Use reflection to check if decoratedHttpClient is updated
        $reflection = new \ReflectionClass(CorrelationIdHttpClient::class);
        $property = $reflection->getProperty('decoratedHttpClient');
        $this->assertSame($mockHttpClientWithOptions, $property->getValue($newClient));
    }
}
