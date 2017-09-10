<?php

namespace Chadicus\Psr\Http\ServerMiddleware;

use Psr\Http\Message\ResponseInterface;

/**
 * Contract for objects creating a 429 Too Many Requests response.
 */
interface LimitedResponseFactoryInterface
{
    /**
     * Create a new response.
     *
     * @param LimitedClientInterface $client The client being limited.
     *
     * @return ResponseInterface
     */
    public function createResponse(LimitedClientInterface $client) : ResponseInterface;
}
