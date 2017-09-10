<?php

namespace Chadicus\Psr\Http\ServerMiddleware;

use Chadicus\Psr\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RateLimitMiddleware implements MiddlewareInterface
{
    /**
     * Extractor for obtaining a client from the incoming HTTP request
     *
     * @var ClientExtractorInterface
     */
    private $extractor;

    /**
     * Create a new instance of the middleware.
     *
     * @param ClientExtractorInterface $extractor Obtains the client from the HTTP request.
     */
    public function __construct(ClientExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
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
            return $response->withStatusCode(429);
        }

        return $next($request, $response);
    }
}
