<?php

namespace Chadicus\Psr\Http\ServerMiddleware;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Contract for api clients which can be limited.
 */
interface LimitedClientInterface
{
    /**
     * Return true if the client can make 1 additional request.
     *
     * @param ServerRequestInterface $request The HTTP request attempted by the client.
     *
     * @return boolean
     */
    public function canMakeRequest(ServerRequestInterface $request) : bool;
}
