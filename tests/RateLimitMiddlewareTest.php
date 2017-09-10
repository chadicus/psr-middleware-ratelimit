<?php

namespace ChadicusTest\Psr\Http\ServerMiddleware;

use Chadicus\Psr\Http\ServerMiddleware\ClientExtractorInterface;
use Chadicus\Psr\Http\ServerMiddleware\LimitedClientInterface;
use Chadicus\Psr\Http\ServerMiddleware\LimitedResponseFactoryInterface;
use Chadicus\Psr\Http\ServerMiddleware\RateLimitMiddleware;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;

/**
 * @coversDefaultClass \Chadicus\Psr\Http\ServerMiddleware\RateLimitMiddleware
 * @covers ::__construct
 */
final class RateLimitMiddlewareTest extends TestCase
{
    /**
     * @test
     * @covers ::__invoke
     *
     * @return void
     */
    public function invoke()
    {
        $middleware = $this->getMiddleware(
            $this->getClientExtractor($this->getLimitedClient(true)),
            $this->getResponseFactory(new Response('php://memory', 429))
        );

        $nextMiddleware = function ($request, $response) {
            return $response;
        };

        $response = new Response();

        $this->assertSame(
            $response,
            $middleware(new ServerRequest(), $response, $nextMiddleware)
        );
    }

    /**
     * @test
     * @covers ::__invoke
     *
     * @return void
     */
    public function invokeLimitExceeded()
    {
        $limitResponse = new Response('php://memory', 429);

        $middleware = $this->getMiddleware(
            $this->getClientExtractor($this->getLimitedClient(false)),
            $this->getResponseFactory($limitResponse)
        );

        $nextMiddleware = function ($request, $response) {
            throw new \Exception('$next was call but should not have been.');
        };

        $this->assertSame(
            $limitResponse,
            $middleware(new ServerRequest(), new Response(), $nextMiddleware)
        );
    }

    private function getLimitedClient(bool $canMakeRequest) : LimitedClientInterface
    {
        $mock = $this->getMockBuilder('\\Chadicus\\Psr\\Http\\ServerMiddleware\\LimitedClientInterface')->getMock();
        $mock->method('canMakeRequest')->willReturn($canMakeRequest);
        return $mock;
    }

    private function getClientExtractor(LimitedClientInterface $client) : ClientExtractorInterface
    {
        $mock = $this->getMockBuilder('\\Chadicus\\Psr\\Http\\ServerMiddleware\\ClientExtractorInterface')->getMock();
        $mock->method('extract')->willReturn($client);
        return $mock;
    }

    private function getResponseFactory(Response $response) : LimitedResponseFactoryInterface
    {
        $mock = $this->getMockBuilder(
            '\\Chadicus\\Psr\\Http\\ServerMiddleware\\LimitedResponseFactoryInterface'
        )->getMock();
        $mock->method('createResponse')->willReturn($response);
        return $mock;
    }

    private function getMiddleware(
        ClientExtractorInterface $clientExtractor,
        LimitedResponseFactoryInterface $responseFactory
    ) : RateLimitMiddleware {
        return new RateLimitMiddleware($clientExtractor, $responseFactory);
    }
}
