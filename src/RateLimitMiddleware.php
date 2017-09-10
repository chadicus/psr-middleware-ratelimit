<?php

namespace Chadicus\Psr\Http\ServerMiddleware;

use Chadicus\Psr\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RateLimitMiddleware implements MiddlewareInterface
{
    /**
     * @var ClientExtractorInterface
     */
    private $extractor;

    /**
     * @var LimitedResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Create a new instance of the middleware.
     *
     * @param ClientExtractorInterface        $extractor       Obtains the client from the HTTP request.
     * @param LimitedResponseFactoryInterface $responseFactory Factory object for creating 429 responses.
     */
    public function __construct(ClientExtractorInterface $extractor, LimitedResponseFactoryInterface $responseFactory)
    {
        $this->extractor = $extractor;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Execute this middleware.
     *
     * @param  ServerRequestInterface $request  The PSR7 request.
     * @param  ResponseInterface      $response The PSR7 response.
     * @param  callable               $next     The Next middleware.
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $client = $this->clientExtractor->extract($request);
        if (!$client->canMakeRequest($request)) {
            return $this->responseFactory->createResponse($client);
        }

        return $next($request, $response);
    }
}
