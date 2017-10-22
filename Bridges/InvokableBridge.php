<?php

namespace PHPPM\Bridges;

use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7;

class InvokableBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request)
    {
        if ($this->middleware === null) {
            // internal server error
            return new Psr7\Response(500, ['Content-type' => 'text/plain'], 'Middleware not configured during bootstrap');
        }

        $middleware = $this->middleware;
        return $middleware($request);
    }
}
