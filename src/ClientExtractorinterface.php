<?php

namespace Chadicus\Psr\Http\ServerMiddleware;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Contract for objects responsible for generating a client from an incoming HTTP request.
 */
interface ClientExtractorInterface
{
    /**
     * Extracts the client from the incoming HTTP request.
     *
     * @param ServerRequestInterface $request The incoming HTTP request.
     *
     * @return LimitedClientInterface
     */
    public function extract(RequestInterface $request) : LimitedClientInterface;
}
